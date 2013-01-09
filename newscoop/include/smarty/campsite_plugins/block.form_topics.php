<?php
/**
 * @package Newscoop
 */

/**
 * Follow topics form
 *
 * @param string $params
 * @param string $content
 * @param object $smarty
 * @return string
 */
function smarty_block_form_topics($params, $content, $smarty)
{
    $view = $smarty->getTemplateVars('view');

    $params += array(
        'method' => 'POST',
        'action' => $view->url(array('controller' => 'dashboard', 'action' => 'save-topics'), 'default'),
    );

    return $view->form('follow_topics', $params, $content);
}
