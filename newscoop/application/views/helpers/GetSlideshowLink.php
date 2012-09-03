<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class View_Helper_GetSlideshowLink extends Zend_View_Helper_Abstract
{
    /**
     * Get slideshow link
     *
     * @param array $items
     * @param int $index
     * @return string
     */
    public function GetSlideshowLink(array $items, $index)
    {
        $data = array(
            'start_index' => $index,
            'media' => $items,
        );

        return base64_encode(json_encode($data));
    }
}
