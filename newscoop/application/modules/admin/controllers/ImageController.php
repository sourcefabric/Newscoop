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
            'thumbnail' => new Rendition('thumbnail', 150, 150, 'fit'),
            'square' => new Rendition('square', 200, 200, 'crop'),
            'main' => new Rendition('main', 400, 300, 'crop'),
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
        $rendition = $this->renditions[$this->_getParam('rendition')];
        $image = $this->_helper->service('image')->find(array_pop(explode('-', $this->_getParam('image'))));

        $this->_helper->service('image.rendition')->setArticleRendition($this->_getParam('article_number'), $rendition, $image);

        $this->_helper->layout->disableLayout();
        $this->view->image = $image;
        $this->view->rendition = $rendition;
    }
}
