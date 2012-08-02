<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;

/**
 */
class Admin_ArticleController extends Zend_Controller_Action
{
    /**
     * @Acl(resource="image", action="edit")
     */
    public function editImageAction()
    {
        $this->_helper->layout->setLayout('iframe');

        camp_load_translation_strings('article_images');
        require_once ADMIN_PATH . '/articles/article_common.php';

        $articleImage = $this->_helper->service('image')->getArticleImage($this->_getParam('article'), $this->_getParam('image'));

        $form = new Admin_Form_ArticleImageForm();
        $form->setDefaultsFromEntity($articleImage, $this->_getParam('language'));
        $form->article->language->setValue($this->_getParam('language'));

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $this->_helper->service('image')->saveImage($values['image'], $articleImage->getImage());
            $this->_helper->service('image')->saveArticleImage($values['article'], $articleImage);
            $this->_helper->flashMessenger(getGS('Image data saved.'));
            $this->_helper->redirector->gotoRoute();
        }

        $this->view->articleImage = $articleImage;
        $this->view->image = $articleImage->getImage();
        $this->view->form = $form;
    }
}
