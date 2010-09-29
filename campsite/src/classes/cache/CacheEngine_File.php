<?php
/**
 * @package Campsite
 */

require_once(dirname(__FILE__).'/CacheEngine.php');

class CacheEngine_File extends CacheEngine
{
    private static $m_name = 'File',
                   $m_description = "It allows to store cache in a file system.",
                   $m_cache = array(),
                   $m_update = false,
                   $m_enabled = false;

    const CACHE_FILE_NAME = 'cache.php';
    const CACHE_LIFETIME = 600;

    public function getName()
    {
        return self::$m_name;
    }

    public function __construct()
    {
        if (SystemPref::Get('DBCacheEngine') == 'File') {
            self::$m_enabled = true;
            $cacheFileName = $GLOBALS['g_campsiteDir'].'/'.self::CACHE_FILE_NAME;
            if (file_exists($cacheFileName)) {
                include $cacheFileName;
            }
            if (isset($cache)) {
                self::$m_cache = &$cache;
            } else {
                self::$m_cache = array(
                    'expired' => time() + self::CACHE_LIFETIME,
                    'counter' => 0,
                );
            }
        }
    }

    public function __destruct()
    {
        if (self::$m_enabled) {
            if (self::$m_cache['expired'] < time()) {
                self::$m_cache = array(
                    'expired' => time() + self::CACHE_LIFETIME,
                    'counter' => 0,
                );
                self::$m_update = true;
            }
            if (self::$m_update) {
                ++self::$m_cache['counter'];
                $cacheFileName = $GLOBALS['g_campsiteDir'].'/'.self::CACHE_FILE_NAME;
                $content = "<?php\n \$cache = " . var_export(self::$m_cache, true) . ';';
                file_put_contents($cacheFileName, $content);
            }
        }
    }

    /**
     * Inserts the value identified by the given key in the cache.
     * Returns false if the key already existed and does not
     * overwrite the existing key.
     * @param $p_key
     * @param $p_value
     * @return boolean
     */
    public function addValue($p_key, $p_value, $p_ttl = 0)
    {
        self::$m_cache[$p_key]['value'] = $p_value;
        self::$m_cache[$p_key]['timestamp'] = time();
        self::$m_cache[$p_key]['ttl'] = $p_ttl == 0 ? 0 : time() + $p_ttl;
        self::$m_update = true;
        return true;
    }


    /**
     * Stores the value identified by the given key in the cache.
     * Returns true on success, false on failure.
     * @param $p_key
     * @param $p_value
     * @return boolean
     */
    public function storeValue($p_key, $p_value, $p_ttl = 0)
    {
        return self::addValue($p_key, $p_value, $p_ttl);
    }

    /**
     * Returns true if a value identified by the given key was
     * stored in the cache.
     * @param $key
     * @return mixed
     */
    public function hasValueKey($p_key)
    {
        if (isset(self::$m_cache[$p_key])) {
            $key = &self::$m_cache[$p_key];
        } else {
            return false;
        }
        if ($key['ttl'] == 0 || $key['ttl'] < time()) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Fetches the value identified by the given key from the cache.
     * @param $key
     * @return mixed
     */
    public function fetchValue($p_key)
    {
        if (isset(self::$m_cache[$p_key])) {
            $key = &self::$m_cache[$p_key];
        } else {
            return false;
        }
        if ($key['ttl'] == 0 || $key['ttl'] < time()) {
            return $key['value'];
        } else {
            return false;
        }
    }


    /**
     * Delete the value identified by the given key from the cache.
     * Returns true on success, false on failure.
     * @param $key
     * @return boolean
     */
    public function deleteValue($p_key)
    {
        unset(self::$m_cache[$p_key]);
        self::$m_update = true;
        return true;
    }


    /**
     * Deletes the values stored in the cache.
     * Returns true on success, false on failure.
     * @return boolean
     */
    public function clearValues()
    {
        self::$m_cache = array();
        self::$m_update = true;
        return true;
    }


    /**
     * Stores the current page under the given key (identifier).
     * Returns true on success, false on failure.
     * @param $p_key
     * @param $p_value
     * @return boolean
     */
    public function storePage($p_key, $p_value, $p_ttl = 0)
    {
        throw new UnsupportedCacheOperation('store page');
    }


    /**
     * Delete the page identified by the given key from the cache.
     * @param $key
     * @return void
     */
    public function deletePage($p_key)
    {
        throw new UnsupportedCacheOperation('delete page');
    }


    /**
     * Deletes the pages stored in the cache.
     * @return void
     */
    public function clearPages()
    {
        return self::clearValues();
    }


    /**
     * Deletes the expired values and pages stored in the cache.
     * @return void
     */
    public function garbageCollector()
    {
    }


    /**
     * Returns true if the page caching was supported, false otherwise.
     * @return boolean
     */
    public function pageCachingSupported()
    {
        return false;
    }


    /**
     * Returns true if the engine was supported in PHP, false otherwise.
     * @return boolean
     */
    public function isSupported()
    {
        return true;
    }


    /**
     * Returns a short description of the cache engine.
     * @return string
     */
    public function description()
    {
        return self::$m_description;
    }


    /**
     * Returns an array of cached data; false if invalid type.
     * @param $p_type
     * @return array
     */
    public function getInfo($p_type = self::CACHE_VALUES_INFO)
    {
        return;
    }


    /**
     * Returns an array of shared memory data
     * @return array
     */
    public function getMemInfo()
    {
        return;
    }
} // class CacheEngine

?>