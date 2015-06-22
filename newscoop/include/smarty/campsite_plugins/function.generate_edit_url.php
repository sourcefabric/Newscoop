<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2015 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Generate edit url for article (to current assigned to user editor)
 * 
 * Result of that that function should be cached in template (as it fetch article object for every call).
 *
 * examle:
 *     {{ generate_edit_url articleNumber="1" language="en" }}
 *
 * Type:     function
 * Name:     generate_edit_url
 * Purpose:  Get article edit url (for backend editor)
 *
 * @param array
 *     $params Parameters
 * @param object
 *     $smarty The Smarty object
 */
function smarty_function_generate_edit_url($params, &$smarty)
{
    $editorService = \Zend_Registry::get('container')->get('newscoop.editor');
    $em = \Zend_Registry::get('container')->get('em');

    $article = $em->getRepository('Newscoop\Entity\Article')
        ->getArticle($params['articleNumber'], $params['language'])
        ->getOneOrNullResult();

    if (!$article) {
        return null;
    }

    return $editorService->getLink($article);
}
