<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once LIBS_DIR . '/ImageList/ImageList.php';

/**
 * Image list widget
 * @title Images
 */
class ImageListWidget extends Widget
{
    public function render()
    {
        $list = new ImageList;

        $list->setHidden(0);
        if (!$this->isFullscreen()) {
            $list->setHidden(6);
            $list->setHidden(7);
        }

        $list->render();
    }
}
