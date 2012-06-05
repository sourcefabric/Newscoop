<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

require_once APPLICATION_PATH . '/../library/Newscoop/Webcode.php';
require_once APPLICATION_PATH . '/../library/Newscoop/Webcode/Mapper.php';

$em = Zend_Registry::get('container')->getService('em');
foreach ($em->getRepository('Newscoop\Entity\Article')->findAll() as $article) {
    try {
        Zend_Registry::get('container')->getService('webcode')->setArticleWebcode($article, trim(Newscoop\Webcode\Mapper::encode($article->getNumber()), '@'));
    } catch (\InvalidArgumentException $e) { // generate new on duplicate
        Zend_Registry::get('container')->getService('webcode')->setArticleWebcode($article);
    }
}
