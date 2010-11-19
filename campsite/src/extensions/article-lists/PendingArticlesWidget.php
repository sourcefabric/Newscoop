<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * @title Pending Articles
 */
class PendingArticlesWidget extends Widget
{
    public function render()
    {
        $articlelist = new ArticleList();
        $articlelist->setItems(Article::GetUnplacedArticles());
        $articlelist->render();
    }
}
