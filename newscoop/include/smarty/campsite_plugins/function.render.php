<?php

/* Newscoop render function plugin
 *
 * Newscoop caching save cached template file content with special vector parameters.
 * By default vector is filled with 5 parameters:
 *  * language
 *  * publication
 *  * issue
 *  * section
 *  * article
 *
 * There is also "params" parameter where we can save  array of custom parameters (serialized into string) or string.
 *
 * To ignore one or more parameters (to make cached template this same for many articles, sections, issues etc) just set it value to "off"
 *
 * You can also provide custom cache lifetime (or set it in admin themes management) - use "cache" parameter. Setting "cache" to off will not cache this rendered file.
 *
 * Examples:
 *
 * {{ render file="_tpl/_html-head.tpl" cache="3200" }} - cache "_tpl/_html-head.tpl" file for 3200 seconds with current context vector
 *
 * {{ render file="_tpl/_html-head.tpl" publication="2" }} - change default publication value in vector to 2
 *
 * {{ render file="_tpl/_html-head.tpl" article="off" cache="3200" }} - cache "_tpl/_html-head.tpl" file for 3200 seconds for all articles in current vector (section, issue, publication and language)
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

    if ($smarty->templateCacheHandler) {
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
            $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
            $themesService = \Zend_Registry::get('container')->getService('newscoop_newscoop.themes_service');
            $cacheKey = $cacheService->getCacheKey(array('template', $themesService->getThemePath(), $p_params['file']), 'template');
            if ($cacheService->contains($cacheKey)) {
                $smarty->cache_lifetime = $cacheService->fetch($cacheKey);
            } else {
                $template = new Template($themesService->getThemePath() . $p_params['file']);
                $smarty->cache_lifetime = (int) $template->getCacheLifetime();
                $cacheService->save($cacheKey, $smarty->cache_lifetime);
            }
        } elseif ($p_params['cache'] == 'off') {
           $smarty->caching = 0;
        } else {
            $smarty->cache_lifetime = (int) $p_params['cache'];
        }
    }

    // add parameters as variables in rendered file
    foreach ($p_params as $key => $value) {
        if ($key != 'params' && $key != 'file') {
            $smarty->assign($key, $value);
        }
    }

    // make sure that every diffirent set of parameters will get his own cache instance
    $smarty->display($p_params['file'], sha1(serialize($smarty->campsiteVector)));

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
