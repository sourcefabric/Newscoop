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
 * @title Pending Articles
 */
class PendingArticlesWidget extends ArticlesWidget
{
    public function __construct()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->title = $translator->trans('Pending Articles', array(), 'extensions');
    }

    public function beforeRender()
    {
        $this->items = Article::GetUnplacedArticles();
    }
}
