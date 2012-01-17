<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Image\Rendition;

/**
 * @Acl(ignore=True)
 */
class Admin_ImageController extends Zend_Controller_Action
{
    protected $renditions = array();

    public function init()
    {
        $this->renditions = array(
            'thumbnail' => new Rendition('thumbnail', 75, 75, 'fill'),
            'square' => new Rendition('square', 150, 150, 'fill'),
            'landscape' => new Rendition('landscape', 400, 300, 'fill'),
            'portrait' => new Rendition('portrait', 300, 400, 'fill'),
        );
    }

    public function articleAction()
    {
        $this->_helper->layout->setLayout('iframe');

        $this->view->renditions = $this->renditions;
        $this->view->images = array_map(function($articleImage) {
            return $articleImage->getImage();
        }, \ArticleImage::GetImagesByArticleNumber($this->_getParam('article_number')));

        $this->view->articleRenditions = $this->_helper->service('image.rendition')->getArticleRenditions($this->_getParam('article_number'));
    }

    public function setRenditionAction()
    {
        $rendition = $this->renditions[array_shift(explode(' ', $this->_getParam('rendition')))];
        $image = $this->_helper->service('image')->find(array_pop(explode('-', $this->_getParam('image'))));

        $this->_helper->service('image.rendition')->setArticleRendition($this->_getParam('article_number'), $rendition, $image);

        $this->_helper->layout->disableLayout();
        $this->view->image = $image;
        $this->view->rendition = $rendition;
    }
}
