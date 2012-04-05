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
    public function writeCachedContent($_template, $content)
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
    public function clearAll($exp_time = null)
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
    public function clear($resource_name, $cache_id, $compile_id, $exp_time)
    {
        $handler = $this->cacheClass;
        return $handler::handler('clean', $this->smarty, null, $resource_name, null, null, null);
    }
}

?>
