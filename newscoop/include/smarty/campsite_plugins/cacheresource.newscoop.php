<?php
/**
 * Smarty Cache Resource for Newscoop
 *
 * @package Newscoop
 * @subpackage Smarty Cacher
 */

class Smarty_CacheResource_Newscoop extends Smarty_CacheResource_Custom
{
    private $cacheClass;

    public function __construct()
    {
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
        $this->cacheClass = 'TemplateCacheHandler_' . $preferencesService->TemplateCacheHandler;
    }

    /**
     * fetch cached content and its modification time from data source
     *
     * @param  string  $id            unique cache content identifier
     * @param  string  $tpl_name      template name
     * @param  string  $cache_id      cache id
     * @param  string  $compile_id    compile id
     * @param  string  $content       cached content
     * @param  integer $mtime         cache modification timestamp (epoch)
     * @param  integer $cacheLifetime cache lifetime in seconds
     * @return void
     */
    protected function fetch($id, $tpl_name, $cache_id, $compile_id, &$content, &$mtime, $cacheLifetime = 0)
    {
        $themesService = \Zend_Registry::get('container')->getService('newscoop_newscoop.themes_service');
        $handler = $this->cacheClass;
        $expired = $handler::handler('read', $cache_content, $tpl_name, null, null, null);
        if ($cacheLifetime == 0) {
            $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
            $cacheKey = $cacheService->getCacheKey(array('template', $themesService->getThemePath(), $tpl_name), 'template');
            if ($cacheService->contains($cacheKey)) {
                $cacheLifetime = $cacheService->fetch($cacheKey);
            } else {
                $template = new Template($themesService->getThemePath() . $tpl_name);
                $cacheLifetime = (int) $template->getCacheLifetime();
                $cacheService->save($cacheKey, $cacheLifetime);
            }
        }

        if ($expired != false) {
            $content = $cache_content;
            $mtime = $expired - $cacheLifetime;
        }
    }

    /**
     * Save content to cache
     *
     * @param  string       $id             unique cache content identifier
     * @param  string       $tpl_name       template name
     * @param  string       $cache_id       cache id
     * @param  string       $compile_id     compile id
     * @param  integer|null $exp_time       seconds till expiration or null
     * @param  string       $content        content to cache
     * @param  array        $campsiteVector Newscoop's CampsiteVector for defining which page is being cached
     * @return boolean      success
     */
    protected function save($id, $tpl_name, $cache_id, $compile_id, $exp_time, $content, $campsiteVector = array())
    {
        $handler = $this->cacheClass;

        return $handler::handler('write', $content, $tpl_name, null, null, $exp_time);
    }

    /**
     * Delete content from cache
     *
     * @param  string       $tpl_name   template name
     * @param  string       $cache_id   cache id
     * @param  string       $compile_id compile id
     * @param  integer|null $exp_time   seconds till expiration time in seconds or null
     * @return integer      number of deleted caches
     */
    protected function delete($tpl_name, $cache_id, $compile_id, $exp_time)
    {
        $handler = $this->cacheClass;

        return $handler::handler('clean', null, $tpl_name, null, null, null);
    }

    /**
     * Write the rendered template output to cache
     *
     * @param  Smarty_Internal_Template $_template template object
     * @param  string                   $content   content to cache
     * @return boolean                  success
     */
    public function writeCachedContent(Smarty_Internal_Template $_template, $content)
    {
        return $this->save(
            $_template->cached->filepath,
            $_template->source->name,
            $_template->cache_id,
            $_template->compile_id,
            $_template->properties['cache_lifetime'],
            $content,
            $_template->smarty->campsiteVector
        );
    }

    /**
     * populate Cached Object with timestamp and exists from Resource
     *
     * @return void
     */
    public function populateTimestamp(Smarty_Template_Cached $cached)
    {
        $mtime = $this->fetchTimestamp($cached->filepath, $cached->source->name, $cached->cache_id, $cached->compile_id, $cached->source->smarty->cache_lifetime);
        if ($mtime !== null) {
            $cached->timestamp = $mtime;
            $cached->exists = !!$cached->timestamp;

            return;
        }
        $timestamp = null;
        $this->fetch($cached->filepath, $cached->source->name, $cached->cache_id, $cached->compile_id, $cached->content, $timestamp, $cached->source->smarty->cache_lifetime);
        $cached->timestamp = isset($timestamp) ? $timestamp : false;
        $cached->exists = !!$cached->timestamp;
    }
}
