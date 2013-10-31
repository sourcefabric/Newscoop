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
    public function __construct()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->title = $translator->trans('Images');
    }

    public function render()
    {
        $list = new ImageList;

        $list->setHidden('Id');
        if (!$this->isFullscreen()) {
            $list->setHidden('TimeCreated');
            $list->setHidden('LastModified');
            $list->setHidden('InUse');
        }

        $list->render();
    }
}
