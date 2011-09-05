<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Image;

/**
 * @Acl(ignore=1)
 */
class Admin_ImageController extends Zend_Controller_Action
{
    /**
     */
    public function init()
    {
        $this->_helper->layout->setLayout('iframe');

        camp_load_translation_strings('api');
        camp_load_translation_strings('users');
    }

    public function changeAction()
    {
        $form = new Admin_Form_Image();
        $user = $this->_helper->service('user')->find($this->_getParam('user'));

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();

            try {
                $imageInfo = array_pop($form->image->getFileInfo());
                $values['image'] = $this->_helper->service('image')->save($imageInfo);
                $this->_helper->service('user')->save($values, $user);
                $this->view->image = $this->_helper->service('image')->getSrc($values['image'], $this->_getParam('width', 80), $this->_getParam('height', 80));
            } catch (\InvalidArgumentException $e) {
                $form->image->addError($e->getMessage());
            }
        }

        $this->view->form = $form;
    }
}
