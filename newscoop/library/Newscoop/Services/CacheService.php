<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Paweł Mikołąjczuk <pawel.mikolajczuk@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Cache\CacheKey;

/**
 * Cache service
 */
class CacheService
{
    /**
     * Instance of cache driver
     *
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    protected $cacheDriver = null;

    /**
     * @var \Newscoop\NewscoopBundle\Services\SystemPreferencesService
     */
    protected $systemPreferences;

    /**
     * Initialize cache driver (based on system preferences settings, default is array)
     *
     * @param \Newscoop\NewscoopBundle\Services\SystemPreferencesService $systemPreferences
     */
    public function __construct($systemPreferences)
    {
        $this->systemPreferences = $systemPreferences;
    }

    /**
     * Fetch data from cache
     *
     * @param string|array $id
     *
     * @return mixed
     */
    public function fetch($id)
    {
        return $this->getCacheDriver()->fetch($this->getCacheKey($id));
    }

    /**
     * Check if cache have provided key
     *
     * @param string|array $id
     *
     * @return boolean
     */
    public function contains($id)
    {
        return $this->getCacheDriver()->contains($this->getCacheKey($id));
    }

    /**
     * Save new value in cache
     *
     * @param string|array $id
     * @param mixed        $data
     * @param integer      $lifeTime
     *
     * @return boolean
     */
    public function save($id, $data, $lifeTime = 1400)
    {
        return $this->getCacheDriver()->save($this->getCacheKey($id), $data, $lifeTime);
    }

    /**
     * Delete key from cache
     *
     * @param string|array $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        return $this->getCacheDriver()->delete($this->getCacheKey($id));
    }

    public function getCacheKey($id, $namespace = null)
    {
        if (is_a($id, 'Newscoop\CacheKey')) {
            return $id->key;
        }

        if (is_array($id)) {
            foreach ($id as $key => $value) {
                if (is_object($value)) {
                    $id[$key] = serialize($value);
                }
            }

            $id = implode('__', $id);
        }

        // make cache key short
        $id = md5($id.'|'.$this->systemPreferences->SiteSecretKey);

        if ($namespace) {
            $namespace = $this->getNamespace($namespace);

            return $namespace.'__'.$id;
        }

        return new CacheKey(array('key' => $id));
    }

    public function getNamespace($namespace)
    {
        if ($this->getCacheDriver()->contains($namespace)) {
            return $this->getCacheDriver()->fetch($namespace);
        }

        $value = $namespace .'|'.time().'|'.$this->systemPreferences->SiteSecretKey;
        $this->getCacheDriver()->save($namespace, $value);

        return $value;
    }

    public function clearNamespace($namespace)
    {
        $this->getCacheDriver()->save($namespace, time());
    }

    /**
     * Get array of avaiable cache drivers (based on system configurations)
     *
     * @return array
     */
    public function getAvailableCacheEngines()
    {
        $engines = array();

        if (extension_loaded('apc') && ini_get('apc.enabled')) {
            $engines['Apc'] = 'apc';
        }

        if (class_exists('\Redis')) {
            $engines['Redis'] = 'redis';
        }

        if (class_exists('\Memcache')) {
            $engines['Memcache'] = 'memcache';
        }

        if (class_exists('\Memcached')) {
            $engines['Memcached'] = 'memcached';
        }

        if (extension_loaded('xcache') && ini_get('xcache.cacher')) {
            $engines['Xcache'] = 'xcache';
        }

        return $engines;
    }

    /**
     * Get cache driver instance
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    public function getCacheDriver()
    {
        if (!is_null($this->cacheDriver)) {
            return $this->cacheDriver;
        }

        if (php_sapi_name() === 'cli') {
            return $this->cacheDriver = new \Doctrine\Common\Cache\ArrayCache();
        }

        try {
            switch ($this->systemPreferences->get('DBCacheEngine', 'Array')) {
                case 'apc':
                    $this->cacheDriver = new \Doctrine\Common\Cache\ApcCache();
                    break;
                case 'memcache':
                    $memcache = new \Memcache();
                    $memcache->connect(
                        $this->systemPreferences->get('DBCacheEngineHost', '127.0.0.1'), 
                        $this->systemPreferences->get('DBCacheEnginePort', '11211')
                    );

                    $this->cacheDriver = new \Doctrine\Common\Cache\MemcacheCache();
                    $this->cacheDriver->setMemcache($memcache);
                    break;
                case 'memcached':
                    $memcached = new \Memcached();
                    $memcached->addServer(
                        $this->systemPreferences->get('DBCacheEngineHost', '127.0.0.1'), 
                        $this->systemPreferences->get('DBCacheEnginePort', '11211')
                    );

                    $this->cacheDriver = new \Doctrine\Common\Cache\MemcachedCache();
                    $this->cacheDriver->setMemcached($memcached);
                    break;
                case 'xcache':
                    $this->cacheDriver = new \Doctrine\Common\Cache\XcacheCache();
                    break;
                case 'redis':
                    $redis = new \Redis();
                    $redis->connect(
                        $this->systemPreferences->get('DBCacheEngineHost', '127.0.0.1'), 
                        $this->systemPreferences->get('DBCacheEnginePort', '6379')
                    );
                    $this->cacheDriver = new \Doctrine\Common\Cache\RedisCache();
                    $this->cacheDriver->setRedis($redis);
                    break;
                default:
                    $this->cacheDriver = new \Doctrine\Common\Cache\ArrayCache();
                    break;
            }
        } catch (\Exception $e) {
            $this->cacheDriver = new \Doctrine\Common\Cache\ArrayCache();
        }

        return $this->cacheDriver;
    }
}
