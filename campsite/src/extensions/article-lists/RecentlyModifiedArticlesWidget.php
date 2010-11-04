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

class RecentlyModifiedArticlesWidget extends Widget
{
    public function getTitle()
    {
        return getGS('Recently Modified Articles');
    }

    public function render()
    {
        $articlelist = new ArticleList();
        $articlelist->setItems(Article::GetRecentlyModifiedArticles(20));
        $articlelist->render();
    }
}
