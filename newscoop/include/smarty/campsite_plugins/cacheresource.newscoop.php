<?php

class Smarty_CacheResource_Newscoop {

    function __construct(&$smarty)
    {
        $this->smarty = $smarty;
        $this->cacheClass = 'TemplateCacheHandler_' . SystemPref::Get('TemplateCacheHandler');
    }

    public static function content($template, $content = null)
    {
        static $cache_content = array();
        if ($content) {
            $cache_content[$template] = $content;
        } elseif (isset($cache_content[$template])) {
            return $cache_content[$template];
        }
    }

    /**
     * Returns the timpestamp of the cached template output
     *
     * @param object $_template current template
     * @return integer |booelan the template timestamp or false if the file does not exist
     */
    public function getCachedTimestamp($_template)
    {
        //return time() - 100;
        //return true;
        $template = $_template->template_resource;
        $handler = $this->cacheClass;
        $cache_content = null;
        $expired = $handler::handler('read', $this->smarty, $cache_content, $template, null, null, null);
        Smarty_CacheResource_Newscoop::content($template, $cache_content);
        if (!$expired) {
            return false;
        } else {
            return $expired - $this->smarty->cache_lifetime;
        }

    }

    /**
     * Returns the cached template output
     *
     * @param object $_template current template
     * @return string |booelan the template content or false if the file does not exist
     */
    public function getCachedContents($_template, $no_render = false)
    {
        $_smarty_tpl = $_template;
        $template = $_template->template_resource;
        $cache_content = Smarty_CacheResource_Newscoop::content($template);

        ob_start();
        eval("?>" . $cache_content);
        return ob_get_clean();
    }

    /**
     * Writes the rendered template output to table
     *
     * @param object $_template current template
     * @return boolean status
     */
    public function writeCachedContent(Smarty_Internal_Template $_template, $content)
    {
        $template = $_template->template_resource;
        $handler = $this->cacheClass;
        return $handler::handler('write', $this->smarty, $content
        , $template, null, null, $this->smarty->cache_lifetime);
    }

    /**
     * Empty cache table
     *
     * @param integer $exp_time expiration time
     * @return integer number of cache files deleted
     */
    public function clearAll(Smarty $smarty, $exp_time = null)
    {
        $handler = $this->cacheClass;
        return $handler::handler('clean', $this->smarty, null, null, null, null, null);
    }

    /**
     * Empty cache for a specific template
     *
     * @param string $resource_name template name
     * @param string $cache_id cache id
     * @param string $compile_id compile id
     * @param integer $exp_time expiration time
     * @return integer number of cache files deleted
     */
    public function clear(Smarty $smarty, $resource_name, $cache_id, $compile_id, $exp_time)
    {
        $handler = $this->cacheClass;
        return $handler::handler('clean', $this->smarty, null, $resource_name, null, null, null);
    }

    /** These functions are direct copies of the Smarty source. We have to properly extend their class **/

    /**
     * populate Cached Object with meta data from Resource
     *
     * @param Smarty_Template_Cached   $cached    cached object
     * @param Smarty_Internal_Template $_template template object
     * @return void
     */
    public function populate(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template)
    {
        $_cache_id = isset($cached->cache_id) ? preg_replace('![^\w\|]+!', '_', $cached->cache_id) : null;
        $_compile_id = isset($cached->compile_id) ? preg_replace('![^\w\|]+!', '_', $cached->compile_id) : null;

        $cached->filepath = sha1($cached->source->filepath . $_cache_id . $_compile_id);
        $this->populateTimestamp($cached);
    }

    /**
     * populate Cached Object with timestamp and exists from Resource
     *
     * @param Smarty_Template_Cached $source cached object
     * @return void
     */
    public function populateTimestamp(Smarty_Template_Cached $cached)
    {
        $mtime = $this->fetchTimestamp($cached->filepath, $cached->source->name, $cached->cache_id, $cached->compile_id);
        if ($mtime !== null) {
            $cached->timestamp = $mtime;
            $cached->exists = !!$cached->timestamp;
            return;
        }
        $timestamp = null;
        // I personally don't know what is being fetched here and what for.
        // $this->fetch($cached->filepath, $cached->source->name, $cached->cache_id, $cached->compile_id, $cached->content, $timestamp);
        $cached->timestamp = isset($timestamp) ? $timestamp : false;
        $cached->exists = !!$cached->timestamp;
    }

    /**
     * Fetch cached content's modification timestamp from data source
     *
     * {@internal implementing this method is optional.
     *  Only implement it if modification times can be accessed faster than loading the complete cached content.}}
     *
     * @param string $id         unique cache content identifier
     * @param string $name       template name
     * @param string $cache_id   cache id
     * @param string $compile_id compile id
     * @return integer|boolean timestamp (epoch) the template was modified, or false if not found
     */
    protected function fetchTimestamp($id, $name, $cache_id, $compile_id)
    {
        return null;
    }
}

?>
