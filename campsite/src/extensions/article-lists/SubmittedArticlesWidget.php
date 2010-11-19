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
 * @title Submitted Articles
 */
class SubmittedArticlesWidget extends Widget
{
    public function render()
    {
        if ($this->getUser()->hasPermission('ChangeArticle') || $this->getUser()->hasPermission('Publish')) {
            $articlelist = new ArticleList();
            $articlelist->setItems(Article::GetSubmittedArticles());
            $articlelist->render();
        } else {
            echo '<p>', getGS('Access Denied'), '</p>';
        }
    }
}
