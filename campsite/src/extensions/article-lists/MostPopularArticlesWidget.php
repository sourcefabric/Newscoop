<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/ArticlesWidget.php';

/**
 * @title Most Popular Articles
 */
class MostPopularArticlesWidget extends ArticlesWidget
{
    public function beforeRender()
    {
        $count = 0;
        $popularArticlesParams = array(
            new ComparisonOperation('published', new Operator('is'), 'true'),
            new ComparisonOperation('reads', new Operator('greater'), '0'),
        );
        $this->items = Article::GetList($popularArticlesParams,
            array(array('field'=>'bypopularity', 'dir'=>'desc')),
            NULL, $NumDisplayArticles, $count);
    }

    public function render()
    {
        $articlelist = new ArticleList();
        $articlelist->setItems($this->items);
        if (!$this->isFullscreen()) {
            $articlelist->setHidden(7);
            $articlelist->setHidden(12);
            $articlelist->setHidden(15);
        }
        $articlelist->render();
    }
}
