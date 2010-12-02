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
 * @title Your Articles
 */
class YourArticlesWidget extends ArticlesWidget
{
    public function beforeRender()
    {
        $this->items = Article::GetArticlesByUser($this->getUser()->getUserId());
    }
}
