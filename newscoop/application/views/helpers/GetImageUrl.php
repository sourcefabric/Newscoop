<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class View_Helper_GetImageUrl extends Zend_View_Helper_Abstract
{
    /**
     * Get image url
     *
     * @param object $item
     * @param int $max
     * @return string
     */
    public function GetImageUrl($item, $max = 2048)
    {
        $src = Zend_Registry::get('container')->getService('image')->getSrc(
            $item->image->original,
            $max,
            $max,
            'fit'
        );

        return $this->view->serverUrl(
            $this->view->url(array('src' => $src), 'image', true, false)
        );
    }
}
