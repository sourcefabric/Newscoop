<?php
/**
 * @package Newscoop
 */

/**
 * Form Text Field
 *
 * @param array $params
 * @param object $smarty
 * @return string
 */
function smarty_function_form_text($params, $smarty)
{
    if (empty($params['name'])) {
        return;
    }

    $view = $smarty->getTemplateVars('view');
    return $view->formText(
        $params['name'],
        isset($params['value']) ? $params['value'] : null,
        $params
    );
}
