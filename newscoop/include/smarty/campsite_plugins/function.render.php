<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite render function plugin
 *
 * Type:     function
 * Name:     render
 * Purpose:  template rendering
 *
 * @param array
 *     $p_params
 * @param object
 *     $p_smarty The Smarty object
 *
 * @return
 *     rendered content
 */
function smarty_function_render($p_params, &$p_smarty)
{
    if (empty($p_params['file'])) {
        return null;
    }
    $smarty = clone $p_smarty;
    if (SystemPref::Get('TemplateCacheHandler')) {
        $campsiteVector = $smarty->campsiteVector;
        foreach ($campsiteVector as $key => $value) {
            if (isset($p_params[$key])) {
                if (empty($p_params[$key])) {
                    $campsiteVector[$key] = null;
                }
                if (is_int($p_params[$key])) {
                    $campsiteVector[$key] = $p_params[$key];
                }
            }
            if (isset($p_params['params'])) {
                $campsiteVector['params'] = $p_params['params'];
            }
        }
        $smarty->campsiteVector = $campsiteVector;
        if (empty($p_params['cache'])) {
            $template = new Template($p_params['file']);
            $smarty->cache_lifetime = (int)$template->getCacheLifetime();
        } else {
            $smarty->cache_lifetime = (int)$p_params['cache'];
        }
    }
    return $smarty->display($p_params['file']);

} // fn smarty_function_render

?>