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
     * @var bool
     */
    public $is_video;

    /**
     * @var object
     */
    public $video;

    /**
     * @var object
     */
    public $image;

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
            $image = $item->getImage();
            $thumbnail = $item->getRendition()->getThumbnail($image, Zend_Registry::get('container')->getService('image'));
            $this->image = (object) array(
                'src' => Zend_Registry::get('view')->url(array(
                    'src' => $thumbnail->src,
                ), 'image', true, false),
                'width' => $thumbnail->width,
                'height' => $thumbnail->height,
                'original' => $item->getImage()->getPath(),
                'id' => $image->getId()
            );
        } else {
            $this->video = (object) array(
                'url' => $item->getVideoUrl(),
                'width' => $item->getRendition()->getWidth(),
                'height' => $item->getRendition()->getHeight(),
            );
        }
    }
}
