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
    public function articleAction()
    {
        $this->view->renditions = array(
            new Rendition('thumbnail', 150, 150, 'fit'),
            new Rendition('square', 200, 200, 'crop'),
            new Rendition('main', 400, 300, 'crop'),
        );

        $this->view->images = array_map(function($articleImage) {
            return $articleImage->getImage();
        }, \ArticleImage::GetImagesByArticleNumber($this->_getParam('article_number')));
    }
}
