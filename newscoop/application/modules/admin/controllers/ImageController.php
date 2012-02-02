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
        camp_load_translation_strings('article_images');

        $this->renditions = $this->_helper->service('image.rendition')->getRenditions();

        $this->_helper->contextSwitch()
            ->addActionContext('edit', 'json')
            ->addActionContext('set-rendition', 'json')
            ->addActionContext('remove-rendition', 'json')
            ->initContext();

        $this->view->previewWidth = 100;
        $this->view->previewHeight = 100;
    }

    public function articleAction()
    {
        $this->_helper->layout->setLayout('iframe');
        $this->view->renditions = $this->renditions;
        $this->view->images = $this->_helper->service('image')->findByArticle($this->_getParam('article_number'));
        $this->view->articleRenditions = $this->_helper->service('image.rendition')->getArticleRenditions($this->_getParam('article_number'));
    }

    public function setRenditionAction()
    {
        $this->_helper->layout->disableLayout();

        try {
            $rendition = $this->_helper->service('image.rendition')->getRendition($this->_getParam('rendition'));
            $image = $this->_helper->service('image')->getArticleImage($this->_getParam('article_number'), array_pop(explode('-', $this->_getParam('image'))));
            $articleRendition = $this->_helper->service('image.rendition')->setArticleRendition($this->_getParam('article_number'), $rendition, $image->getImage());
            $this->view->rendition = $this->view->rendition($rendition, $this->view->previewWidth, $this->view->previewHeight, $articleRendition);
        } catch (\InvalidArgumentException $e) {
            $this->view->exception= $e->getMessage();
        }
    }

    public function removeRenditionAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->service('image.rendition')->unsetArticleRendition($this->_getParam('article_number'), $this->_getParam('rendition'));
        $rendition = $this->renditions[$this->_getParam('rendition')];
        $renditions = $this->_helper->service('image.rendition')->getArticleRenditions($this->_getParam('article_number'));
        $this->view->rendition = $this->view->rendition($rendition, $this->view->previewWidth, $this->view->previewHeight, $renditions[$rendition]);
    }

    public function editAction()
    {
        $this->_helper->layout->setLayout('iframe');
        $rendition = $this->_helper->service('image.rendition')->getRendition($this->_getParam('rendition'));
        $renditions = $this->_helper->service('image.rendition')->getArticleRenditions($this->_getParam('article_number'));
        $image = $renditions[$rendition]->getImage();

        if ($this->getRequest()->isPost()) {
            $this->_helper->service('image.rendition')
                ->setArticleRendition($this->_getParam('article_number'), $rendition, $image, $this->getRequest()->getPost('coords'));
            $this->_helper->redirector('edit', 'image', 'admin', array(
                'article_number' => $this->_getParam('article_number'),
                'rendition' => $this->_getParam('rendition'),
            ));
        }

        $this->view->rendition = $renditions[$rendition]->getRendition();
        $this->view->image = $renditions[$rendition]->getImage();
        $this->view->renditions = $this->renditions;
        $this->view->articleRenditions = $this->_helper->service('image.rendition')->getArticleRenditions($this->_getParam('article_number'));
    }

    public function setDefaultImageAction()
    {
        $image = $this->_helper->service('image')->getArticleImage($this->_getParam('article_number'), $this->_getParam('default-image'));
        $this->_helper->service('image')->setDefaultArticleImage($this->_getParam('article_number'), $image);
        $this->_helper->redirector('article', 'image', 'admin', array(
            'article_number' => $this->_getParam('article_number'),
        ));
    }
}
