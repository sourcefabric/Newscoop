<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class View_Helper_GetVideoUrl extends Zend_View_Helper_Abstract
{
    /**
     * Get video url
     *
     * @param object $item
     * @return string
     */
    public function GetVideoUrl($item)
    {
        if (is_numeric($item->video->url)) {
            return sprintf('http://vimeo.com/%d', $item->video->url);
        } else {
            return sprintf('http://youtu.be/%s', $item->video->url);
        }
    }
}
