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
        $uri = CampSite::GetURIInstance();
        $handler = $this->cacheClass;
        $expired = $handler::handler('read', $cache_content, $tpl_name, null, null, null);
        $template = new Template($uri->getThemePath() . $tpl_name);

        if ($expired != false) {
            $content = $cache_content;
            $mtime = $expired - (int)$template->getCacheLifetime();
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
     * @param  string       $tpl_name       template name
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
}