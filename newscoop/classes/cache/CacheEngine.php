<?php

class InvalidCacheEngine extends Exception
{
}

class UnsupportedCacheEngine extends Exception
{
}

class UnsupportedCacheOperation extends Exception
{
}

abstract class CacheEngine
{
    const CACHE_VALUES_INFO = 1;
    const CACHE_PAGES_INFO = 2;


    abstract public function getName();

    /**
     * Inserts the value identified by the given key in the cache.
     * Returns false if the key already existed and does not
     * overwrite the existing key.
     * @param $p_key
     * @param $p_value
     * @return boolean
     */
    abstract public function addValue($p_key, $p_value, $p_ttl = 0);

    /**
     * Stores the value identified by the given key in the cache.
     * Returns true on success, false on failure.
     * @param string $p_key
     * @param string $p_value
     * @return boolean
     */
    abstract public function storeValue($p_key, $p_value, $p_ttl = 0);

    /**
     * Returns true if a value identified by the given key was
     * stored in the cache.
     * @param $key
     * @return mixed
     */
    abstract public function hasValueKey($p_key);

    /**
     * Fetches the value identified by the given key from the cache.
     * @param $key
     * @param string $p_key
     * @return mixed
     */
    abstract public function fetchValue($p_key);

    /**
     * Delete the value identified by the given key from the cache.
     * @param $key
     * @param string $p_key
     * @return void
     */
    abstract public function deleteValue($p_key);

    /**
     * Deletes the values stored in the cache.
     * @return void
     */
    abstract public function clearValues();

    /**
     * Stores the current page under the given key (identifier).
     * Returns true on success, false on failure.
     * @param $p_key
     * @param $p_value
     * @return boolean
     */
    abstract public function storePage($p_key, $p_value, $p_ttl = 0);

    /**
     * Delete the page identified by the given key from the cache.
     * @param $key
     * @return void
     */
    abstract public function deletePage($p_key);

    /**
     * Deletes the pages stored in the cache.
     * @return void
     */
    abstract public function clearPages();

    /**
     * Deletes the expired values and pages stored in the cache.
     * @return void
     */
    abstract public function garbageCollector();

    /**
     * Returns true if the page caching was supported, false otherwise.
     * @return boolean
     */
    abstract public function pageCachingSupported();

    /**
     * Returns true if the engine was supported in PHP, false otherwise.
     * @return boolean
     */
    abstract public function isSupported();

    /**
     * Returns a short description of the cache engine.
     * @return string
     */
    abstract public function description();

    /**
     * Returns an array of cached data; false if invalid type.
     * @param $p_type
     * @return array
     */
    abstract public function getInfo($p_type = self::CACHE_VALUES_INFO);

    /**
     * Returns an array of shared memory data
     * @return array
     */
    abstract public function getMemInfo();

    /**
     * Loads the engine specified by the given name.
     * @param $p_engineName
     * @return boolean
     */
    public static function Factory($p_engineName, $p_path = null)
    {
        if (is_null($p_path)) {
            $path = dirname(__FILE__);
        } else {
            $path = $p_path;
        }
        $filePath = "$path/CacheEngine_$p_engineName.php";
        if (!file_exists($filePath)) {
            throw new InvalidCacheEngine($p_engineName);
        }
        require_once($filePath);
        $className = "CacheEngine_$p_engineName";
        if (!class_exists($className)) {
            throw new InvalidCacheEngine($p_engineName);
        }
        $cacheObj = new $className;
        if ($cacheObj->isSupported()) {
            return $cacheObj;
        }
        return null;
    }

    /**
     * Returns an array of available engines containing
     * engine name -> info pairs.
     * @param $p_path
     * @return array
     */
    public static function AvailableEngines($p_path = null)
    {
        if (is_null($p_path)) {
            $path = dirname(__FILE__);
        } else {
            $path = $p_path;
        }

        $includeFiles = glob(realpath($path) . '/CacheEngine_*.php');
        $engines = array();
        foreach ($includeFiles as $includeFile) {
            if (preg_match('/CacheEngine_([^.]+)\.php/', $includeFile, $matches) == 0) {
                continue;
            }

            require_once($includeFile);
            $engineName = $matches[1];
            $className = "CacheEngine_$engineName";
            if (class_exists($className)) {
                $cacheEngine = new $className;
                $engines[$engineName] = array(
                    'is_supported'=>$cacheEngine->isSupported(),
                    'page_caching_supported'=>$cacheEngine->pageCachingSupported(),
                    'file'=>"$path/CacheEngine_$engineName.php",
                    'description'=>$cacheEngine->description());
            }
        }
        return $engines;
    }
} // class CacheEngine

?>
