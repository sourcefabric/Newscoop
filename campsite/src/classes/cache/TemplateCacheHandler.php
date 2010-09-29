<?php

abstract class TemplateCacheHandler
{
    /**
     * Returns true if the handler was supported in PHP, false otherwise.
     * @return boolean
     */
    abstract public function isSupported();

    /**
     * Returns a short description of the cache handler.
     * @return string
     */
    abstract public function description();

    /**
     * Smarty template cache handler implementation.
     * @return boolean
     */
    abstract static function handler($action, &$smarty_obj, &$cache_content, $tpl_file = null, $cache_id = null,
        $compile_id = null, $exp_time = null);

    /**
     * Clears template cache storage.
     * @param $p_campsiteVector
     * @return boolean
     */
    abstract public function clean();

    /**
     * Updates template cache storage by given campsite vector.
     * @return boolean
     */
    abstract public function update($camspiteVector);
}

?>