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
    protected $items = array();

    public function render()
    {
        $articlelist = new ArticleList();
        $articlelist->setItems($this->items);
        if (!$this->isFullscreen()) {
            $articlelist->setHidden(12);
            $articlelist->setHidden(13);
            $articlelist->setHidden(15);
        }
        $articlelist->render();
    }
}
