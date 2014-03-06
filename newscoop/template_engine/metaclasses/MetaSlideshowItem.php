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
        global $Campsite;

        $this->item = $item;
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
                'id' => $image->getId(),
                'caption' => $item->getCaption() ?: $item->getImage()->getCaption(),
                'photographer' => $image->getPhotographer(),
                'photographer_url' => $image->getPhotographerUrl(),
                'original' => $image->isLocal() ? $Campsite['IMAGE_BASE_URL'] . str_replace('images/', '', $image->getPath()) : $image->getPath()
            );
        } else {
            $this->video = (object) array(
                'url' => $item->getVideoUrl(),
                'width' => $item->getRendition()->getWidth(),
                'height' => $item->getRendition()->getHeight(),
            );
        }
    }

    /**
     * Get preview
     *
     * @return object
     */
    public function preview($width, $height)
    {
        $preview = $this->item->getRendition()->getPreview($width, $height);
        $thumbnail = $preview->getThumbnail($this->item->getImage(), Zend_Registry::get('container')->getService('image'));
        return (object) array(
            'src' => Zend_Registry::get('view')->url(array(
                'src' => $thumbnail->src,
            ), 'image', true, false),
            'width' => $thumbnail->width,
            'height' => $thumbnail->height,
        );
    }
}
