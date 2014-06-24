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
    $cache_statusBak = $smarty->caching;
    $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

    if ($preferencesService->TemplateCacheHandler) {
        $campsiteVector = $smarty->campsiteVector;
        foreach ($campsiteVector as $key => $value) {
            if (isset($p_params[$key])) {
                if (empty($p_params[$key]) || (is_string($p_params[$key]) && strtolower($p_params[$key]) == 'off')) {
                    $campsiteVector[$key] = null;
                }
                if (is_int($p_params[$key])) {
                    $campsiteVector[$key] = $p_params[$key];
                }
            }
        }
        if (isset($p_params['params'])) {
            if (is_array($p_params['params'])) {
                $campsiteVector['params'] = '';
                foreach ($p_params['params'] as $key => $value) {
                    $campsiteVector['params'] .= $key .'__'. $value;
                }
            } else {
                $campsiteVector['params'] = $p_params['params'];
            }
        } else {
            $campsiteVector['params'] = null;
        }
        $smarty->campsiteVector = $campsiteVector;

        if (empty($p_params['cache'])) {
            $template = new Template(CampSite::GetURIInstance()->getThemePath() . $p_params['file']);
            $smarty->cache_lifetime = (int)$template->getCacheLifetime();
        } elseif ($p_params['cache'] == 'off') {
           $smarty->caching = 0;
        } else {
            $smarty->cache_lifetime = (int)$p_params['cache'];
        }
    }

    // add parameters as variables in rendered file
    foreach ($p_params as $key => $value) {
        if ($key != 'params' && $key != 'file') {
            $smarty->assign($key, $value);
        }
    }

    $smarty->display($p_params['file']);

    // clear assigned variables
    foreach ($p_params as $key => $value) {
        if ($key != 'params' && $key != 'file') {
            $smarty->clearAssign($key);
        }
    }
    $smarty->cache_lifetime = $cache_lifetimeBak;
    $smarty->campsiteVector = $campsiteVectorBak;
    $smarty->caching = $cache_statusBak;
}

?>
