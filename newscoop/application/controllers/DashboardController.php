<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

/**
 */
class DashboardController extends Zend_Controller_Action
{
    /** @var Newscoop\Services\UserService */
    private $service;

    /** @var Newscoop\Entity\User */
    private $user;

    public function init()
    {
        $GLOBALS['controller'] = $this;
        $this->_helper->layout->disableLayout();

        $this->service = $this->_helper->service('user');
        $this->user = $this->service->getCurrentUser();
    }

    public function preDispatch()
    {
        if (empty($this->user)) {
            $this->_helper->redirector('index', 'auth');
        }
    }

    public function indexAction()
    {
        $form = new Application_Form_Profile();
        $form->setMethod('POST');

        $form->setDefaultsFromEntity($this->user);

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();

            $imageInfo = array_pop($form->image->getFileInfo());
            if (!in_array($imageInfo['type'], array('image/jpeg'))) {
                $form->image->addError("Unsupported image type '$imageInfo[type]'");
            } else {
                $newname = sha1_file($imageInfo['tmp_name']) . '.' . array_pop(explode('.', $imageInfo['name']));
                if (!file_exists(APPLICATION_PATH . "/../images/$newname")) {
                    rename($imageInfo['tmp_name'], APPLICATION_PATH . "/../images/$newname");
                }
                $values['image'] = $newname;
            }

            $this->service->update($this->user, $values);
            $this->_helper->redirector('index');
        }

        $this->view->form = $form;
        $this->view->user = new MetaUser($this->user);
    }

    public function followTopicAction()
    {
        $service = $this->_helper->service('user.topic');
        $topic = $service->findTopic($this->_getParam('topic'));
        if (!$topic) {
            $this->_helper->flashMessenger(array('error', "No topic to follow"));
            $this->_helper->redirector('index', 'index', 'default');
        }

        $service = $this->_helper->service('user.topic');
        $service->followTopic($this->user, $topic);

        $this->_helper->flashMessenger("Topic added to followed");
        $this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
    }
}
