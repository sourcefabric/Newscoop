<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/article_common.php");

$article = new Article();
foreach ($f_order as $order => $item) {
    list($prefix, $articleId) = explode('_', $item);
    $article->Article($f_language, $articleId);
    $article->setProperty('ArticleOrder', $order + 1);
}

return TRUE;
