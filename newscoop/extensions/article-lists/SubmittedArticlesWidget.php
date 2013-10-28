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
 * @title Submitted Articles
 */
class SubmittedArticlesWidget extends ArticlesWidget
{
    public function __construct()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->title = $translator->trans('Submitted Articles', array(), 'extensions');
    }

    public function beforeRender()
    {
        $this->items = Article::GetSubmittedArticles();
    }

    public function render()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        if ($this->getUser()->hasPermission('ChangeArticle') || $this->getUser()->hasPermission('Publish')) {
            parent::render();
        } else {
            echo '<p>', $translator->trans('Access Denied', array(), 'extensions'), '</p>';
        }
    }
}
