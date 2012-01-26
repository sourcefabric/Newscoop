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
class Admin_SlideshowController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->contextSwitch()
            ->addActionContext('add-item', 'json')
            ->addActionContext('set-order', 'json')
            ->addActionContext('remove-item', 'json')
            ->initContext();

        $this->view->previewWidth = 100;
        $this->view->previewHeight = 100;
    }

    public function boxAction()
    {
        $this->view->articleNumber = $this->_getParam('article_number');
        $this->view->slideshows = $this->_helper->service('package')->findByArticle($this->_getParam('article_number'));
    }

    public function createAction()
    {
        $form = new Admin_Form_SlideshowCreate();
        $form->rendition->setMultiOptions($this->_helper->service('image.rendition')->getOptions());

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();
            $values['rendition'] = $this->_helper->service('image.rendition')->getRendition($values['rendition']);
            $values['article'] = $this->_getParam('article_number');
            $slideshow = $this->_helper->service('package')->save($values);
            $this->_helper->redirector('edit', 'slideshow', 'admin', array(
                'slideshow' => $slideshow->getId(),
                'article_number' => $this->_getParam('article_number'),
            ));
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        $this->_helper->layout->setLayout('iframe');

        $slideshow = $this->getSlideshow();
        $form = new Admin_Form_Slideshow();
        $form->setDefaultsFromEntity($slideshow);

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $this->_helper->service('package')->save($form->getValues(), $slideshow);
        }

        $this->view->form = $form;
        $this->view->images = $this->_helper->service('image')->findByArticle($slideshow->getArticleNumber());
        $this->view->slideshow = $slideshow;
    }

    public function addItemAction()
    {
        $slideshow = $this->getSlideshow();
        $image = $this->_helper->service('image')->find(array_pop(explode('-', $this->_getParam('image'))));
        $item = $this->_helper->service('package')->addItem($slideshow, $image);
        $this->view->item = $this->view->slideshowItem($item);
    }

    public function addVideoItemAction()
    {
        $form = new Admin_Form_SlideshowVideoItem();
        $form->setMethod('POST')->setAction($this->view->url(array(
            'action' => 'add-video-item',
        )));

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $slideshow = $this->getSlideshow();
            $this->_helper->service('package')->addItem($slideshow, new \Newscoop\Package\RemoteVideo($form->url->getValue()));
            $this->_helper->redirector('edit', 'slideshow', 'admin', array(
                'slideshow' => $slideshow->getId(),
            ));
        }

        $this->view->form = $form;
    }

    public function removeItemAction()
    {
        $slideshow = $this->getSlideshow();
        $this->_helper->service('package')->removeItem($slideshow, $this->_getParam('item'));
    }

    public function setOrderAction()
    {
        $slideshow = $this->getSlideshow();
        $this->_helper->service('package')->setOrder($slideshow, $this->_getParam('order'));
    }

    public function editItemAction()
    {
        $this->_helper->layout->setLayout('iframe');

        $slideshow = $this->getSlideshow();
        $item = $this->_helper->service('package')->findItem($this->_getParam('item'));

        $form = new Admin_Form_SlideshowItem();
        $form->setMethod('POST');
        $form->setDefaultsFromEntity($item);

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $this->_helper->service('package')->saveItem($form->getValues(), $item);
            $this->_helper->json(array(
                'status' => 'ok',
            ));
        }

        $this->view->item = $item;
        $this->view->form = $form;
        $this->view->image = $item->getImage();
        $this->view->rendition = $item->isImage() ? $item->getRendition() : $slideshow->getRendition();
    }

    /**
     * Get slideshow by param
     *
     * @return Newscoop\Package\Package
     */
    private function getSlideshow()
    {
        return $this->_helper->service('package')->find($this->_getParam('slideshow'));
    }
}
