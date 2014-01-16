<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Thumbnail
 */
class Thumbnail
{
    /**
     * @var string
     */
    public $src;

    /**
     * @var int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    /**
     * @param string $src
     * @param int $width
     * @param int $height
     */
    public function __construct($src, $width, $height)
    {
        $this->src = (string) $src;
        $this->width = (int) $width;
        $this->height = (int) $height;
    }

    /**
     * Get img
     *
     * @param Zend_View $view
     * @return string
     */
    public function getImg(\Zend_View $view)
    {
        return sprintf(
            '<img src="%s" width="%d" height="%d" alt="" />',
            $view->url(array('src' => $this->src), 'image', true, false),
            $this->width,
            $this->height
        );
    }
}
