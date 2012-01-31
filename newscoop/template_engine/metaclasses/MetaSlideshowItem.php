<?php
/**
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
final class MetaSlideshowItem
{
    /**
     * @var string
     */
    public $caption;

    /**
     * @var bool
     */
    public $is_image;

    /**
     * @var string
     */
    public $src;

    /**
     * @var bool
     */
    public $is_video;

    /**
     * @var string
     */
    public $video_url;

    /**
     * @var int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    /**
     * @var Newscoop\Package\Item
     */
    private $item;

    /**
     * @param Newscoop\Package\Item $item
     */
    public function __construct(\Newscoop\Package\Item $item)
    {
        $this->caption = $item->getCaption();
        $this->is_image = $item->isImage();
        $this->is_video = $item->isVideo();

        if ($item->isImage()) {
            $thumbnail = $item->getRendition()->getThumbnail($item->getImage(), Zend_Registry::get('container')->getService('image'));
            $this->src = Zend_Registry::get('view')->url(array(
                'src' => $thumbnail->src,
            ), 'image', true, false);
            $this->width = $thumbnail->width;
            $this->height = $thumbnail->height;
        } else {
            $this->width = $item->getRendition()->getWidth();
            $this->height = $item->getRendition()->getHeight();
            $this->video_url = $item->getVideoUrl();
        }
    }
}
