<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Newscoop translate modifier plugin
 *
 * Type:     modifier
 * Name:     translate
 * Purpose:  Translates given string
 *
 * @param String to translate
 *     $string
 * @param Translation domain
 *     $domain
  * @param params
 *     $params
 *
 * @return string
 *
 */

function smarty_modifier_translate($string, $domain = null, $params = array())
{
    if (!isset($string)) {
        return '';
    }

    if (!is_null($domain)) {
        $domain = 'theme_translation';
    }

    $translator = \Zend_Registry::get('container')->getService('translator');

    return $translator->trans($string, $params, $domain);
}
