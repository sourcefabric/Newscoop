<?php
/**
 * @package Campsite
 *
 * @author Martin Saturka <martin.saturka@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once LIBS_DIR . '/ImageList/NewsfeedImageList.php';

/**
 * Newsfeed Image list widget
 * @title Newsfeed Images
 */
class NewsfeedImageListWidget extends Widget
{
    public function __construct()
    {
        $this->title = getGS('Newsfeed Images');
    }

    public function render()
    {
        $list = new NewsfeedImageList();

        $list->setHidden('Id');
        if (!$this->isFullscreen()) {
            $list->setHidden('TimeCreated');
            $list->setHidden('LastModified');
            $list->setHidden('InUse');
        }

        $list->render();
    }
}
