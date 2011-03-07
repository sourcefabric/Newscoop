<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once LIBS_DIR . '/ArticleList/ArticleList.php';

/**
 * Articles widget base class
 */
abstract class ArticlesWidget extends Widget
{
    const LIMIT = 20; // articles limit

    protected $title = NULL;

    protected $items = array();

    public function render()
    {
        $articlelist = new ArticleList();
        $articlelist->setItems($this->items);
        if (!$this->isFullscreen()) {
            $articlelist->setHidden('Comments');
            $articlelist->setHidden('Reads');
            $articlelist->setHidden('UseMap');
            $articlelist->setHidden('Locations');
            $articlelist->setHidden('CreateDate');
            $articlelist->setHidden('PublishDate');
        }
        $articlelist->render();
    }
}
