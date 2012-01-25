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
        $slideshow = $this->_helper->service('package')->save(array(
            'article' => $this->_getParam('article_number'),
        ));

        $this->_helper->redirector('edit', 'slideshow', 'admin', array(
            'slideshow' => $slideshow->getId(),
            'article_number' => $this->_getParam('article_number'),
        ));
    }

    public function editAction()
    {
        $this->_helper->layout->setLayout('iframe');

        $slideshow = $this->getSlideshow();
        $form = new Admin_Form_Slideshow();

        $this->view->form = $form;
        $this->view->images = $this->_helper->service('image')->findByArticle($slideshow->getArticleNumber());
        $this->view->slideshow = $slideshow;
    }

    public function addItemAction()
    {
        $slideshow = $this->getSlideshow();
        $image = $this->_helper->service('image')->find(array_pop(explode('-', $this->_getParam('image'))));
        $item = $this->_helper->service('package')->addItem($slideshow, $image);
        $this->view->item = $this->view->packageItem($item);
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
     * Get offset for image
     *
     * @return int
     */
    private function getOffset()
    {
        foreach ($this->_getParam('offset') as $offset => $item) {
            if ($item === $this->_getParam('image')) {
                return $offset;
            }
        }

        return 0;
    }
}
