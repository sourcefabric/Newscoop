<?php
/**
 * @package Newscoop
 */

/**
 * Login Form
 *
 * @param string $params
 * @param string $content
 * @param object $smarty
 * @return string
 */
function smarty_block_form_login($params, $content, $smarty)
{
    $form = new Application_Form_Login();
    $smarty->assign('form_login', $form);
    $view = $smarty->getTemplateVars('view');
    $params = array_merge($params, array(
        'method' => 'POST',
        'action' => $view->url(array('controller' => 'auth', 'action' => 'index'), 'default'),
    ));

    return $view->form('form_login', $params, $content);
}
