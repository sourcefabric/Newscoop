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

    $smarty = CampTemplate::singleton();
    $cache_lifetimeBak = $smarty->cache_lifetime;
    $campsiteVectorBak = $smarty->campsiteVector;
    $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

    if ($preferencesService->TemplateCacheHandler) {
        $campsiteVector = $smarty->campsiteVector;
        foreach ($campsiteVector as $key => $value) {
            if (isset($p_params[$key])) {
                if (empty($p_params[$key]) || strtolower($p_params[$key]) == 'off') {
                    $campsiteVector[$key] = null;
                }
                if (is_int($p_params[$key])) {
                    $campsiteVector[$key] = $p_params[$key];
                }
            }
        }
        if (isset($p_params['params'])) {
            $campsiteVector['params'] = $p_params['params'];
        }
        $smarty->campsiteVector = $campsiteVector;

        if (empty($p_params['cache'])) {
            $template = new Template(CampSite::GetURIInstance()->getThemePath() . $p_params['file']);
            $smarty->cache_lifetime = (int)$template->getCacheLifetime();
        } else {
            $smarty->cache_lifetime = (int)$p_params['cache'];
        }
    }

    $smarty->display($p_params['file']);
    $smarty->cache_lifetime = $cache_lifetimeBak;
    $smarty->campsiteVector = $campsiteVectorBak;

} // fn smarty_function_render

?>