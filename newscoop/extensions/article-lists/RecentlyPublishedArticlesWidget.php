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
 * @title Recently Published Articles
 */
class RecentlyPublishedArticlesWidget extends ArticlesWidget
{
    /** @setting */
    protected $count = 20;

    public function __construct()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->title = $translator->trans('Recently Published Articles', array(), 'extensions');
    }

    public function beforeRender()
    {
        $articlesParams = array(new ComparisonOperation('published', new Operator('is'), 'true'));
        $count = 0;

        foreach((array) \ArticleType::GetArticleTypes(true) as $one_art_type_name) {
            $one_art_type = new \ArticleType($one_art_type_name);
            if ($one_art_type->getFilterStatus()) {
                $articlesParams[] = new ComparisonOperation('type', new Operator('not', 'string'), $one_art_type->getTypeName());
            }
        }

        $articlesOrders = array(
                array(
                    'field' => 'bypublishdate',
                    'dir' => 'desc',
                )
        );

        $this->items = Article::GetList($articlesParams, $articlesOrders, 0, self::LIMIT, $count);
    }
}
