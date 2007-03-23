<?php
/**
 * @package Campsite
 */

define('OBJECT_CACHE_SERIAL_HEADER', "<?php\n/*");
define('OBJECT_CACHE_SERIAL_FOOTER', "*/\n?".">");

/**
 * @package Campsite
 */
abstract class CampBaseCache {
    /**
     * @var string The place to store the object cache files.
     */
	private $m_cacheDir;

    /**
     * @var boolean Whether cache is enabled or not.
     */
    private $m_cacheEnabled = false;

    /**
     * @var boolean Whether cache dir exists and is writable or not.
     */
	private $m_diskCacheEnabled = false;

    /**
     * @var int Expiration time for the objects in cache (in seconds).
     */
	private $m_expirationTime = 900;

    /**
     * @var string The lock file name.
     */
	private $m_flockFilename = 'camp_object_cache.lock';

    /**
     * @var resource Mutual exception.
     */
	private $m_mutex;

    /**
     * @var array Cache holder.
     */
	private $m_cache = array();

    /**
     * @var array Dirty objects holder.
     */
	private $m_dirtyObjects = array();

    /**
     * @var array Non existent objects holder.
     */
	private $m_nonExistentObjects = array();

    /**
     * @var int Number of cached items that were loaded from disk.
     */
	private $m_coldCacheHits = 0;

    /**
     * @var int Number of cached items accessed that were already in memory.
     */
	private $m_hotCacheHits = 0;

    /**
     * @var int Number of items fetched from database as they were not in the cache.
     */
	private $m_cacheMisses = 0;

    /**
     * @var string Secret string to use for hashing.
     */
	private $m_secret = '';


    /**
     *
     */
	private function __construct() {}


    /**
     *
     */
    protected function init()
    {

		global $Campsite;

		register_shutdown_function(array(&$this, "__destruct"));

		if ($Campsite['ENABLE_CACHE'] == false) {
			return;
        } else {
            $this->m_cacheEnabled = true;
        }

		if (ini_get('safe_mode')) {
			return;
        }

		if (isset($Campsite['CACHE_PATH'])) {
			$this->m_cacheDir = $Campsite['CACHE_PATH'];
        } else {
			$this->m_cacheDir = $Campsite['CAMPSITE_DIR'].'/var/cache/';
        }

		if (is_writable($this->m_cacheDir) && is_dir($this->m_cacheDir)) {
            $this->m_diskCacheEnabled = true;
		} elseif (is_writable($Campsite['CAMPSITE_DIR'].'/var')) {
            $this->m_diskCacheEnabled = true;
		}

		if (isset($Campsite['CACHE_EXPIRATION_TIME'])) {
			$this->m_expirationTime = $Campsite['CACHE_EXPIRATION_TIME'];
        }

		if (isset($Campsite['CAMP_SECRET'])) {
			$this->m_secret = $Campsite['CAMP_SECRET'];
        } else {
			$this->m_secret = $Campsite['DATABASE_USER']
                .$Campsite['DATABASE_NAME']
                .$Campsite['DATABASE_SERVER_ADDRESS']
                .$Campsite['WWW_DIR'];
        }
	} // fn __construct


    /**
     *
     */
	private function acquireLock()
    {
        $this->m_mutex = @fopen($this->m_cacheDir.$this->m_flockFilename, 'w');
        if ($this->m_mutex == false) {
			return false;
        }
        flock($this->m_mutex, LOCK_EX);

        return true;
	} // fn acquireLock


    /**
     *
     */
	protected function addObject($p_id, $p_data, $p_group = 'default', $p_expire = '')
    {
        if (empty($p_group)) {
            $p_group = 'default';
        }

        if ($this->getObject($p_id, $p_group, false) !== false) {
            return false;
        }

        return $this->setObject($p_id, $p_data, $p_group, $p_expire);
	} // fn add


    /**
     *
     */
    protected function deleteObject($p_id, $p_group = 'default', $p_force = false)
    {
        if (empty($p_group)) {
            $p_group = 'default';
        }

        if (!$p_force && $this->getObject($p_id, $p_group, false) === false) {
            return false;
        }

        unset($this->m_cache[$p_group][$p_id]);
        $this->m_nonExistentObjects[$p_group][$p_id] = true;
        $this->m_dirtyObjects[$p_group][] = $p_id;

        return true;
    } // fn delete


    /**
     *
     */
	function flush()
    {
        if (!$this->isEnabled()) {
            return true;
        }

        if (!$this->acquireLock()) {
            return false;
        }

        $this->removeCacheDir();
        $this->m_cache = array();
        $this->m_dirtyObjects = array();
        $this->m_nonExistentObjects = array();

        $this->unlock();

        return true;
	} // fn flush


    /**
     *
     */
	protected function getObject($p_id, $p_group = 'default', $p_countHits = true)
    {
        if (empty($p_group)) {
            $p_group = 'default';
        }

        if (isset($this->m_cache[$p_group][$p_id])) {
            if ($p_countHits) {
                $this->m_hotCacheHits += 1;
            }
            return $this->m_cache[$p_group][$p_id];
        }

		if (isset($this->m_nonExistentObjects[$p_group][$p_id])) {
            return false;
        }

        $cacheFile = $this->m_cacheDir.$p_group.'/'.$this->genKey($p_id).'.php';
        if (!file_exists($cacheFile)) {
            $this->m_nonExistentObjects[$p_group][$p_id] = true;
            $this->m_cacheMisses += 1;

            return false;
        }

        $now = time();
        if ((filemtime($cacheFile) + $this->m_expirationTime) <= $now) {
            $this->m_cacheMisses += 1;
            $this->delete($p_id, $p_group, true);

            return false;
        }

		$this->m_cache[$p_group][$p_id] = unserialize(base64_decode(substr(@file_get_contents($cacheFile), strlen(OBJECT_CACHE_SERIAL_HEADER), -strlen(OBJECT_CACHE_SERIAL_FOOTER))));
		if ($this->m_cache[$p_group][$p_id] === false) {
            $this->m_cache[$p_group][$p_id] = '';
        }

		$this->m_coldCacheHits += 1;

        return $this->m_cache[$p_group][$p_id];
    } // fn get


    /**
     *
     */
    private function genKey($p_data)
    {
        if (function_exists('hash_hmac')) {
            $this->m_key = hash_hmac('md5', $p_data, $this->m_secret);
        } else {
            $this->m_key = md5($p_data . $this->m_secret);
        }

        return $this->m_key;
    } // fn genKey


    /**
     *
     */
    private function isEnabled()
    {
        return ($this->m_cacheEnabled && $this->m_diskCacheEnabled);
    } // fn isEnabled


    /**
     *
     */
	private function makeGroupDir($p_group, $p_perms)
    {
        $makeDir = '';
        foreach (split('/', $p_group) as $subDir) {
            $makeDir .= "$subDir/";
            if (!file_exists($this->m_cacheDir.$makeDir)) {
                if (!@mkdir($this->m_cacheDir.$makeDir)) {
                    break;
                }
                @chmod($this->m_cacheDir.$makeDir, $p_perms);
            }

            if (!file_exists($this->m_cacheDir.$makeDir."index.php")) {
                $filePerms = $p_perms & 0000666;
                @touch($this->m_cacheDir.$makeDir."index.php");
                @chmod($this->m_cacheDir.$makeDir."index.php", $filePerms);
            }
        }

        return $this->m_cacheDir."$p_group/";
	} // fn makeGroupDir


    /**
     *
     */
	private function removeCacheDir()
    {
        $dir = $this->m_cacheDir;
        $dir = rtrim($dir, '/');
        $topDir = $dir;
        $stack = array($dir);
        $index = 0;

        while ($index < count($stack)) {
			$dir = $stack[$index];
            $dh = @opendir($dir);
            if (!$dh) {
                return false;
            }
            while (($file = @readdir($dh)) !== false) {
                if ($file == '.' or $file == '..') {
                    continue;
                }

                if (@is_dir($dir.'/'.$file)) {
                    $stack[] = $dir.'/'.$file;
                } else if (@is_file($dir.'/'.$file)) {
                    @unlink($dir.'/'.$file);
                }
            }
            $index++;
        }

        $stack = array_reverse($stack);
        foreach($stack as $dir) {
            if ( $dir != $topDir) {
                @rmdir($dir);
            }
        }
	} // fn removeCacheDir


    /**
     *
     */
    private function unlock()
    {
        flock($this->m_mutex, LOCK_UN);
        fclose($this->m_mutex);
    } // fn unlock


    /**
     *
     */
	function replace($p_id, $p_data, $p_group = 'default', $p_expire = '')
    {
        if (empty($p_group)) {
            $group = 'default';
        }

        if ($this->getObject($p_id, $p_group, false) === false) {
            return false;
        }

        return $this->setObject($p_id, $p_data, $p_group, $p_expire);
	} // fn replace


    /**
     *
     */
    protected function setObject($p_id, $p_data, $p_group = 'default', $p_expire = '')
    {
        if (empty($p_group)) {
            $p_group = 'default';
        }

        if ($p_data == NULL) {
            $p_data = '';
        }

        $this->m_cache[$p_group][$p_id] = $p_data;
        unset($this->m_nonExistentObjects[$p_group][$p_id]);
        $this->m_dirtyObjects[$p_group][] = $p_id;

        return true;
    } // fn set


    /**
     *
     */
    private function save()
    {
        $this->stats();

        if (!$this->isEnabled()) {
            return true;
        }

        if (empty($this->m_dirtyObjects)) {
            return true;
        }

        $stat = stat($Campsite['CAMPSITE_DIR'].'/var');
        $dirPerms = $stat['mode'] & 0007777;
        $filePerms = $dirPerms & 0000666;

        if (!file_exists($this->m_cacheDir)) {
            if (!@mkdir($this->m_cacheDir)) {
                return false;
            }
            @chmod($this->m_cacheDir, $dirPerms);
        }

        if (!file_exists($this->m_cacheDir."index.php")) {
            @touch($this->m_cacheDir."index.php");
            @chmod($this->m_cacheDir."index.php", $filePerms);
        }

        if (!$this->acquireLock()) {
            return false;
        }

        $errors = 0;
        foreach ($this->m_dirtyObjects as $group => $ids) {
            $groupDir = $this->makeGroupDir($group, $dirPerms);
            $ids = array_unique($ids);
            foreach ($ids as $id) {
                $cacheFile = $groupDir.$this->genKey($id).'.php';
                if (!isset($this->m_cache[$group][$id])) {
                    if (file_exists($cacheFile)) {
                        @unlink($cacheFile);
                    }
                    continue;
                }

                $tempFile = tempnam($groupDir, 'tmp');
                $serial = OBJECT_CACHE_SERIAL_HEADER.base64_encode(serialize($this->m_cache[$group][$id])).OBJECT_CACHE_SERIAL_FOOTER;
                $fd = @fopen($tempFile, 'w');
                if ($fd === false) {
                    $errors++;
                    continue;
                }
                fputs($fd, $serial);
                fclose($fd);
                if (!@rename($tempFile, $cacheFile)) {
                    if (@copy($tempFile, $cacheFile)) {
                        @unlink($tempFile);
                    } else {
                        $errors++;
                    }
                }
                @chmod($cacheFile, $filePerms);
            }
        }

        $this->m_dirtyObjects = array();
        $this->unlock();

        if ($errors) {
            return false;
        }

        return true;
    } // fn save


    /**
     *
     */
    private function stats()
    {
        echo "<p>";
        echo "<strong>Cold Cache Hits:</strong> ".$this->m_coldCacheHits."<br/>";
        echo "<strong>Hot Cache Hits:</strong> ".$this->m_hotCacheHits."<br/>";
        echo "<strong>Cache Misses:</strong> ".$this->m_cacheMisses."<br/>";
        echo "</p>";

		foreach ($this->m_cache as $group => $cache) {
            echo "<p>";
            echo "<strong>Group:</strong> $group<br/>";
            echo "<strong>Cache:</strong>";
            echo "<pre>";
            print_r($cache);
            echo "</pre>";
            if (isset ($this->m_dirtyObjects[$group])) {
                echo "<strong>Dirty Objects:</strong>";
                echo "<pre>";
                print_r(array_unique($this->m_dirtyObjects[$group]));
                echo "</pre>";
                echo "</p>";
            }
        }
    } // fn stats


    /**
     *
     */
    protected function getKey()
    {
        return $this->m_key;
    } // fn getKey


    /**
     *
     */
    public function __destruct()
    {
        $this->save();
        return true;
    } // fn __destruct

} // fn class CampBaseCache

?>