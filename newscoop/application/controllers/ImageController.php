<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Nette\Image;

require_once APPLICATION_PATH . '/../library/Nette/exceptions.php';

/**
 */
class ImageController extends Zend_Controller_Action
{
    public function cacheAction()
    {
        // @todo add token for generating to prevent DoS attacks
        $width = $this->_getParam('width');
        $height = $this->_getParam('height');
        $image = $this->_getParam('image');

        // @todo make some path maps according to type (user/author/etc)
        $src = APPLICATION_PATH . '/../images/' . $image;
        $dest = APPLICATION_PATH . '/../images/cache/' . "{$width}_{$height}_{$image}";
        if (!file_exists(APPLICATION_PATH . '/../images/cache/')) {
            mkdir(APPLICATION_PATH . '/../images/cache/');
        }


        $placeholder = Image::fromBlank($width, $height, Image::rgb(255, 255, 255));
        $image = Image::fromFile($src);
        $image->resize($width, $height);
        $placeholder->place($image, '50%', '50%');
        $placeholder->save($dest);
        $placeholder->send();
        exit;
    }
}
