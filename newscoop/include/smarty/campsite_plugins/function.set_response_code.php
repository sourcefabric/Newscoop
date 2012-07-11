<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Response code setter
 *
 * @param array $params
 * @param object $smarty
 * @return void
 */
function smarty_function_set_response_code($params, $smarty)
{
    if (!isset($params['code']) || !is_numeric($params['code'])) {
        return;
    }

    $front = Zend_Controller_Front::getInstance();
    $front->getResponse()->setHttpResponseCode((int) $params['code']);
}
