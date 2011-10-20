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
        $this->title = getGS('Recently Published Articles');
    }

    public function beforeRender()
    {
        $this->items = Article::GetList(array(
            new ComparisonOperation('published', new Operator('is'), 'true'),
            new ComparisonOperation('type', new Operator('is'), 'news'),
            new ComparisonOperation('type', new Operator('is'), 'newswire'),
            new ComparisonOperation('type', new Operator('is'), 'blog'),
            new ComparisonOperation('type', new Operator('is'), 'dossier'),
            ), array(
                array(
                    'field' => 'bypublishdate',
                    'dir' => 'desc',
                )
            ), 0, self::LIMIT, $count = 0);
    }
}
