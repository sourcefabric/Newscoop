<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\Package\PackageService;

/**
 * @Acl(ignore=True)
 */
class Admin_SlideshowController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->contextSwitch()
            ->addActionContext('add-item', 'json')
            ->addActionContext('add-multiple-items', 'json')
            ->addActionContext('set-order', 'json')
            ->addActionContext('remove-item', 'json')
            ->addActionContext('index', 'json')
            ->initContext();

        $this->view->previewWidth = 100;
        $this->view->previewHeight = 100;

        $this->_helper->layout->setLayout('iframe');
    }

    public function boxAction()
    {
        $this->_helper->json($this->view->slideshowsJson($this->_helper->service('package')->findByArticle($this->_getParam('article_number'))));
    }

    public function createAction()
    {
        $form = new Admin_Form_SlideshowCreate();
        $form->rendition->setMultiOptions($this->_helper->service('image.rendition')->getOptions());

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();
            $values['rendition'] = $this->_helper->service('image.rendition')->getRendition($values['rendition']);
            $slideshow = $this->_helper->service('package')->save($values);
            if ($this->_getParam('article_number', false)) {
                $slideshows = $this->_helper->service('package')->findByArticle($this->_getParam('article_number'));
                $slideshows[] = $slideshow;
                $this->_helper->service('package')->saveArticle(array(
                    'id' => $this->_getParam('article_number'),
                    'slideshows' => array_map(function($slideshow) { return array('id' => $slideshow->getId()); }, $slideshows),
                ));
            }
            $this->_helper->redirector('edit', 'slideshow', 'admin', array(
                'article_number' => $this->_getParam('article_number'),
                'slideshow' => $slideshow->getId(),
            ));
        }

        $this->view->form = $form;
    }

    public function editAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $slideshow = $this->getSlideshow();
        $form = new Admin_Form_Slideshow();
        $form->setDefaultsFromEntity($slideshow);

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            try {
                $this->_helper->service('package')->save($form->getValues(), $slideshow);
            } catch (\InvalidArgumentException $e) {
                switch ($e->getCode()) {
                    case PackageService::CODE_UNIQUE_SLUG:
                        $form->slug->addError($translator->trans('Slug must be unique', array(), 'article_images'));
                        break;
                }
            }
        }

        $this->view->form = $form;
        $this->view->images = $this->_helper->service('image')->findByArticle($this->_getParam('article_number'));
        $this->view->slideshow = $slideshow;
    }

    public function addItemAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $slideshow = $this->getSlideshow();
        $image = $this->_helper->service('image')->find(array_pop(explode('-', $this->_getParam('image'))));
        try {
            $item = $this->_helper->service('package')->addItem($slideshow, $image);
            $this->_helper->json(array(
                'item' => $this->view->slideshowItem($item),
            ));
        } catch (\InvalidArgumentException $e) {
            $this->_helper->json(array(
                'error_message' => sprintf($translator->trans('Sorry that image is too small. Image needs to be at least %dx%d.', array(), 'article_images'), $slideshow->getRendition()->getWidth(), $slideshow->getRendition()->getHeight()),
            ));
        }
    }

    public function addMultipleItemsAction()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
        $slideshow = $this->getSlideshow();

        $items = array();
        foreach($this->_getParam('images') as $key => $value) {
            $image = $this->_helper->service('image')->find($value);
            try {
                $item = $this->_helper->service('package')->addItem($slideshow, $image);
                $items[] = $this->view->slideshowItem($item);
            } catch (\InvalidArgumentException $e) {}
        }
        
        $this->_helper->json($items);
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
                'article_number' => $this->_getParam('article_number'),
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
        $slideshow = $this->getSlideshow();
        $item = $this->_helper->service('package')->findItem($this->_getParam('item'));

        $form = new Admin_Form_SlideshowItem();
        $form->setMethod('POST');
        $form->setDefaultsFromEntity($item);

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $this->_helper->service('package')->saveItem($form->getValues(), $item);
            $this->_helper->redirector('edit-item', 'slideshow', 'admin', array(
                'article_number' => $this->_getParam('article_number'),
                'slideshow' => $slideshow->getId(),
                'item' => $item->getId(),
            ));
        }

        $this->view->item = $item;
        $this->view->form = $form;
        $this->view->image = $item->getImage();
        $this->view->rendition = $item->isImage() ? $item->getRendition() : $slideshow->getRendition();
        $this->view->package = $slideshow;
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

    /**
     * Set slideshow renditions
     *
     * @param Zend_Form $form
     * @return void
     */
    private function setSlideshowRenditions(\Zend_Form $form)
    {
        $renditions = $this->_helper->service('image.rendition')->getOptions();
        if (array_key_exists(self::SLIDESHOW_RENDITION, $renditions)) {
            $renditions = array(
                self::SLIDESHOW_RENDITION => $renditions[self::SLIDESHOW_RENDITION],
            );
        }

        if (count($renditions) === 1) {
            $form->removeElement('rendition');
            $form->addElement('hidden', 'rendition', array(
                'value' => array_pop(array_keys($renditions)),
            ));
        } else {
            $form->rendition->setMultiOptions($renditions);
        }
    }

    public function attachAction()
    {
        $this->_helper->layout->setLayout('modal');

        $limit = 25;
        if ($this->_getParam('format') === 'json') {
            $this->_helper->json($this->view->slideshowsJson($this->_helper->service('package')->findBy(array(), array('id' => 'desc'), $limit, ($this->_getParam('page', 1) - 1) * $limit)));
        }

        $paginator = Zend_Paginator::factory($this->_helper->service('package')->getCountBy(array()));
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber(1);

        $this->view->q = '';
        if ($this->_getParam('q', false)) {
            $this->view->slideshows = $this->_helper->service('package.search')->find($this->_getParam('q'));
            $this->view->q = $this->_getParam('q');
            $this->view->article_number = $this->_getParam('article_number');
        } else {
            $this->view->slideshows = $this->_helper->service('package')->findBy(array(), array('id' => 'desc'), $limit, 0);
        }
        $this->view->pages = $paginator->count();

        $this->view->article = array(
            'id' => $this->_getParam('article_number'),
            'slideshows' => $this->view->slideshowsJson($this->_helper->service('package')->findByArticle($this->_getParam('article_number'))),
        );
    }

    public function articleAction()
    {
        $request = \Zend_Registry::get('container')->get('request');
        $article = json_decode(utf8_encode($request->getContent()), true);
        $this->_helper->service('package')->saveArticle($article);
        $this->_helper->json(array());
    }
}
