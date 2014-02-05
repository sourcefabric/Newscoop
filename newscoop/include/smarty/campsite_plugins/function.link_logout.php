<?php

/**
 * Newscoop logout link
 *
 * @param array $params
 * @param object $smarty
 * @return string
 */
function smarty_function_link_logout($params, $smarty)
{
    $view = Zend_Registry::get('view');
    return $view->url(array(
        'controller' => 'auth',
        'action' => 'logout',
        'url' => !empty($params['next']) ? $params['next'] : null,
    ), 'default');
}
