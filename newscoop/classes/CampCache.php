<?php
/**
 * @package Campsite
 */

require_once(__DIR__ . '/../conf/install_conf.php');
require_once('cache/CacheEngine.php');

define('CACHE_SERIAL_HEADER', "<?php\n/*");
define('CACHE_SERIAL_FOOTER', "*/\n?".">");


 /**
  * @package Campsite
  * @deprecated from 4.3, removed in 4.4, use newscoop.cache service
  */
final class CampCache
{
    /**
     * Stores the cache engine wrapper object.
     * @var CacheEngine
     */
    private $m_cacheEngine = null;

    /**
     * The cache key for the current cache object.
     *
     * @var string
     */
    private $m_key = null;

    /**
     * A secret string to be used for hashing cache keys.
     *
     * @var string
     */
    private $m_secret = null;

    /**
     * Holds instance of the class.
     *
     * @var CampCache
     */
    private static $m_instance = null;

    private static $m_enabled = null;

    private static $m_storeRequests = 0;

    private static $m_fetchRequests = 0;

    private static $m_hits = 0;

    private static $m_missKeys = array();


    /**
     * CampCache class constructor.
     * @deprecated from 4.3, removed in 4.4, use newscoop.cache service
     */
    private function __construct($p_cacheEngine)
    {
        return;

        global $Campsite;

        if (empty($p_cacheEngine)) {
            return;
        }

        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
        $this->m_cacheEngine = CacheEngine::Factory($p_cacheEngine);
        if (is_null($this->m_cacheEngine) || !$this->m_cacheEngine->isSupported()) {
            self::$m_enabled = false;
            $preferencesService->DBCacheEngine = '';
            return;
        } else {
            self::$m_enabled = true;
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


    public static function initialized()
    {
        return !is_null(self::$m_instance);
    }


    /**
     * Singleton function that returns the global class object.
     *
     * @return CampCache
     */
    public static function singleton()
    {
        if (is_null(self::$m_instance)) {
            $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
            self::$m_instance = new CampCache($preferencesService->DBCacheEngine);
        }
        return self::$m_instance;
    } // fn singleton


    /**
     * Alias for the store() method.
     *
     * An apc_add() method will be available in future releases of APC
     * providing a different behave than apc_store(). This is basically
     * why we implement this alias function.
     *
     * @param string
     *    $p_key The cache key for the object
     * @param mixed
     *    $p_data The expected data to be cached
     * @param int optional
     *    $p_ttl The ttl for the object in cache
     *
     * @return boolean
     *    TRUE on success, FALSE on failure
     */
    public function add($p_key, $p_data, $p_ttl = 0)
    {
        return $this->store($p_key, $p_data, $p_ttl);
    } // fn add


    /**
     * Fetch an object from cache.
     *
     * @param string
     *    The cache key of the object
     *
     * @return mixed
     *    The unserialized data.
     */
    public function fetch($p_key)
    {
        if (!self::$m_enabled) {
            return false;
        }
        self::$m_fetchRequests ++;
        $serial = $this->m_cacheEngine->fetchValue($this->genKey($p_key));
        if ($serial !== false) {
            self::$m_hits ++;
        } else {
            if (!isset(self::$m_missKeys[$p_key])) {
                self::$m_missKeys[$p_key] = 1;
            } else {
                self::$m_missKeys[$p_key]++;
            }
            return false;
        }

        return $this->unserialize($serial);
    } // fn fetch


    /**
     * Store the given data into cache.
     *
     * @param string
     *    $p_key The cache key for the object
     * @param mixed
     *    $p_data The expected data to be cached
     * @param int optional
     *    $p_ttl The ttl for the object in cache
     *
     * @return boolean
     *    TRUE on success, FALSE on failure
     */
    public function store($p_key, $p_data, $p_ttl = 0)
    {
        if (!self::$m_enabled) {
            return false;
        }
        self::$m_storeRequests ++;
        $p_data = $this->serialize($p_data);

        return $this->m_cacheEngine->storeValue($this->genKey($p_key), $p_data, $p_ttl);
    } // fn fetch


    /**
     * Remove the object with given cache key from cache.
     *
     * @param string
     *    $p_key The cache key for the object.
     *
     * @return boolean
     *    TRUE on success, FALSE on failure
     */
    public function delete($p_key)
    {
        if (!self::$m_enabled) {
            return false;
        }

        return $this->m_cacheEngine->deleteValue($this->genKey($p_key));
    } // fn delete


    /**
     * Clears the cache.
     *
     * @param string
     *    $p_type If given is 'user', the user cache will be cleard,
     *            otherwise the system cache (cached files) will be.
     *
     * @return boolean
     *    TRUE on success, FALSE on failure
     */
    public function clear($p_type = null)
    {
        if (!self::$m_enabled) {
            return false;
        }

        if ($p_type == 'user') {
            return $this->m_cacheEngine->clearValues();
        } else {
            return $this->m_cacheEngine->clearPages();
        }
    } // fn clear


    /**
     * Retrieves cache information and metadata from the cache store.
     *
     * @param string
     *    $p_type If given is 'user', information about the user cache will
     *            be returned, otherwise system cache information.
     *
     * @return mixed
     *    array Cached data and metadata
     *    boolean FALSE on failure
     */
    public function info($p_type = null)
    {
        if (!self::$m_enabled) {
            return false;
        }
        $type = $p_type == 'user' ? CacheEngine::CACHE_VALUES_INFO : CacheEngine::CACHE_PAGES_INFO;
        return $this->m_cacheEngine->getInfo($type);
    } // fn info


    /**
     * Retrieves shared memory allocation information.
     *
     * @return mixed
     *    array Shared memory allocation data
     *    boolean FALSE on failure
     */
    public function meminfo()
    {
        if (!self::$m_enabled) {
            return false;
        }
        return $this->m_cacheEngine->getMemInfo();
    } // fn meminfo


    /**
     * Serializes the given data.
     *
     * @param mixed
     *    $p_data The data to be serialized
     *
     * @return string
     *    The serialized data
     */
    private function serialize($p_data)
    {
        return CACHE_SERIAL_HEADER.base64_encode(serialize($p_data)).CACHE_SERIAL_FOOTER;
    } // fn serialize


    /**
     * Unserializes the given serialized data.
     *
     * @param string
     *    $p_serial The serialized data
     *
     * @return mixed
     *    The unserialized data
     */
    private function unserialize($p_serial)
    {
        return unserialize(base64_decode(substr($p_serial, strlen(CACHE_SERIAL_HEADER), -strlen(CACHE_SERIAL_FOOTER))));
    } // fn unserialize


    /**
     * Generates the hash key for a cache object.
     *
     * @param string
     *    $p_data The database object key to hashing
     *
     * @return string
     *    The hash key
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


    public static function GetStoreRequests()
    {
        return self::$m_storeRequests;
    }


    public static function GetFetchRequests()
    {
        return self::$m_fetchRequests;
    }


    public static function GetHits()
    {
        return self::$m_hits;
    }


    public static function GetMissKeys()
    {
        return self::$m_missKeys;
    }


    /**
     * Returns true if the given cache engine was supported
     *
     * @param $p_cacheEngine
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public static function IsSupported($p_cacheEngine = null)
    {
        if (empty($p_cacheEngine)) {
            return false;
        }
        try {
            if ($cacheEngine = CacheEngine::Factory($p_cacheEngine)) {
                return true;
            } else {
                return false;
            }
        } catch (InvalidCacheEngine $ex) {
            return false;
        }
    }



    /**
     * Returns true if the given cache engine was enabled
     *
     * @param $p_cacheEngine
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public static function IsEnabled($p_cacheEngine = null)
    {
        /**
         * @deprecated from 4.3, removed in 4.4, use newscoop.cache service
         */
        return false;
    }
}
