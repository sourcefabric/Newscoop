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
 * @title Recently Modified Articles
 */
class RecentlyModifiedArticlesWidget extends ArticlesWidget
{
    /** @setting */
    protected $count = 20;

    public function __construct()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->title = $translator->trans('Recently Modified Articles', array(), 'extensions');
    }

    public function beforeRender()
    {
        $this->items = Article::GetRecentlyModifiedArticles(self::LIMIT);
    }
}
