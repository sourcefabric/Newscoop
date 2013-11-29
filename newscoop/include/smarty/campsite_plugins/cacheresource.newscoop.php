<?php
/**
 * Smarty Cache Resource for Newscoop
 *
 * @package Newscoop
 * @subpackage Smarty Cacher
 */

class Smarty_CacheResource_Newscoop extends Smarty_CacheResource_Custom
{
    private static $cache_content = array();
    private static $cache_lifetime = array();
    private $cacheClass;

    public function __construct()
    {
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
        $this->cacheClass = 'TemplateCacheHandler_' . $preferencesService->TemplateCacheHandler;
    }

    public static function content($tpl_name, $content = null)
    {
        if (!isset(self::$cache_content[$tpl_name])) {
            self::$cache_content[$tpl_name] = $content;
        }
        return self::$cache_content[$tpl_name];
    }

    public static function lifetime($tpl_name, $timestamp = null)
    {
        if (!isset(self::$cache_lifetime[$tpl_name])) {
            self::$cache_lifetime[$tpl_name] = $timestamp;
        }
        return self::$cache_lifetime[$tpl_name];
    }

    /**
     * fetch cached content and its modification time from data source
     *
     * @param  string  $id         unique cache content identifier
     * @param  string  $tpl_name       template name
     * @param  string  $cache_id   cache id
     * @param  string  $compile_id compile id
     * @param  string  $content    cached content
     * @param  integer $mtime      cache modification timestamp (epoch)
     * @return void
     */
    protected function fetch($id, $tpl_name, $cache_id, $compile_id, &$content, &$mtime)
    {
        return ${"?>".self::content($tpl_name)};
    }

    /**
     * Fetch cached content's modification timestamp from data source
     *
     * {@internal implementing this method is optional.
     *  Only implement it if modification times can be accessed faster than loading the complete cached content.}}
     *
     * @param  string          $id         unique cache content identifier
     * @param  string          $tpl_name   template name
     * @param  string          $cache_id   cache id
     * @param  string          $compile_id compile id
     * @return integer|boolean timestamp (epoch) the template was modified, or false if not found
     */
    protected function fetchTimestamp($id, $tpl_name, $cache_id, $compile_id)
    {
        $uri = CampSite::GetURIInstance();
        $handler = $this->cacheClass;
        $expired = $handler::handler('read', $cache_content, $tpl_name, null, null, null);
        self::content($tpl_name, $cache_content);
        self::lifetime($tpl_name, $uri->getCacheLifetime());

        if (!$expired) {
            return null;
        } else {
            return $expired - $uri->getCacheLifetime();
        }
    }

    /**
     * Save content to cache
     *
     * @param  string       $id         unique cache content identifier
     * @param  string       $tpl_name       template name
     * @param  string       $cache_id   cache id
     * @param  string       $compile_id compile id
     * @param  integer|null $exp_time   seconds till expiration or null
     * @param  string       $content    content to cache
     * @return boolean      success
     */
    protected function save($id, $tpl_name, $cache_id, $compile_id, $exp_time, $content)
    {
        $handler = $this->cacheClass;
        return $handler::handler('write', $content, $tpl_name, null, null, self::lifetime($tpl_name));
    }

    /**
     * Delete content from cache
     *
     * @param  string       $tpl_name       template name
     * @param  string       $cache_id   cache id
     * @param  string       $compile_id compile id
     * @param  integer|null $exp_time   seconds till expiration time in seconds or null
     * @return integer      number of deleted caches
     */
    protected function delete($tpl_name, $cache_id, $compile_id, $exp_time)
    {
        unset(self::$cache_content[$tpl_name]);
        $handler = $this->cacheClass;
        return $handler::handler('clean', null, $tpl_name, null, null, null);
    }

}