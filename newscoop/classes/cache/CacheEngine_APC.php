<?php

require_once(dirname(__FILE__).'/CacheEngine.php');

class CacheEngine_APC extends CacheEngine
{
    private static $m_name = 'APC';

    private static $m_description = "The Alternative PHP Cache (APC)
    is a free and open opcode cache for PHP. It was conceived of to
    provide a free, open, and robust framework for caching and
    optimizing PHP intermediate code.";


    public function getName()
    {
        return self::$m_name;
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
        return apc_add($p_key, $p_value, $p_ttl);
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
        apc_delete($p_key);
        return apc_store($p_key, $p_value, $p_ttl);
    }


    /**
     * Returns true if a value identified by the given key was
     * stored in the cache.
     * @param $key
     * @return boolean
     */
    public function hasValueKey($p_key)
    {
        return apc_fetch($p_key) !== false;
    }


    /**
     * Fetches the value identified by the given key from the cache.
     * @param $key
     * @return mixed
     */
    public function fetchValue($p_key)
    {
        return apc_fetch($p_key);
    }


    /**
     * Delete the value identified by the given key from the cache.
     * Returns true on success, false on failure.
     * @param $key
     * @return boolean
     */
    public function deleteValue($p_key)
    {
        return apc_delete($p_key);
    }


    /**
     * Deletes the values stored in the cache.
     * Returns true on success, false on failure.
     * @return boolean
     */
    public function clearValues()
    {
        return apc_clear_cache('user');
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
     * @return boolean
     */
    public function clearPages()
    {
        return apc_clear_cache();
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
        return ini_get('apc.enabled') && function_exists('apc_store');
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
        switch ($p_type) {
            case self::CACHE_VALUES_INFO:
                return apc_cache_info('user');
            case self::CACHE_PAGES_INFO:
                return apc_cache_info();
            default:
                return false;
        }
        return apc_cache_info();
    }


    /**
     * Returns an array of shared memory data
     * @return array
     */
    public function getMemInfo()
    {
        return apc_sma_info();
    }
} // class CacheEngine

?>