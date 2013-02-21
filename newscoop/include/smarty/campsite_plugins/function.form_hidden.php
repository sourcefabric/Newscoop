<?php
/**
 * @package Newscoop
 */

/**
 * Form Hidden Field
 *
 * @param array $params
 * @param object $smarty
 * @return string
 */
function smarty_function_form_hidden($params, $smarty)
{
    if (empty($params['name'])) {
        return;
    }

    $view = $smarty->getTemplateVars('view');

    return $view->formHidden(
        $params['name'],
        isset($params['value']) ? $params['value'] : null,
        $params
    );
}
