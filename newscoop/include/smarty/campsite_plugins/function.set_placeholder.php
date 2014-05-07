<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Sets placeholder in templates
 *
 * Type:     function
 * Name:     set_placeholder
 * Purpose:  Sets placeholders
 *
 * @param array
 *     $params Parameters
 * @param object
 *     $smarty The Smarty object
 */
function smarty_function_set_placeholder($params, &$smarty)
{
    $placeholdersService = \Zend_Registry::get('container')->get('newscoop.placeholders.service');

    foreach ($params as $key => $value) {
        $placeholdersService->set($key, $value);
    }
}
?>
