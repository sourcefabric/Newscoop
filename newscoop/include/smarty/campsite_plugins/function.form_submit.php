<?php
/**
 * @package Newscoop
 */

/**
 * Form Submit Field
 *
 * @param array $params
 * @param object $smarty
 * @return string
 */
function smarty_function_form_submit($params, $smarty)
{
    if (empty($params['value'])) {
        return;
    }

    $view = $smarty->getTemplateVars('view');
    return $view->formSubmit(
        isset($param['name']) ? $params['name'] : '',
        $params['value'],
        $params
    );
}
