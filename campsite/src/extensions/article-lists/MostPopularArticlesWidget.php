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

class MostPopularArticlesWidget extends Widget
{
    public function getTitle()
    {
        return getGS('Most Popular Articles');
    }

    public function render()
    {
        $articlelist = new ArticleList();
        $count = 0;
        $popularArticlesParams = array(
            new ComparisonOperation('published', new Operator('is'), 'true'),
            new ComparisonOperation('reads', new Operator('greater'), '0'),
        );
        $articlelist->setItems(Article::GetList($popularArticlesParams,
            array(array('field'=>'bypopularity', 'dir'=>'desc')),
            NULL, $NumDisplayArticles, $count));
        $articlelist->render();
    }
}
