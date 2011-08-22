<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * User controller
 *
 * @Acl(action="manage")
 */
class Admin_UserController extends Zend_Controller_Action
{
    /** @var Newscoop\Services\UserService */
    private $userService;

    /** @var Newscoop\Services\UserTypeService */
    private $userTypeService;

    /**
     */
    public function init()
    {
        camp_load_translation_strings('api');
        camp_load_translation_strings('users');

        $this->userService = $this->_helper->service('user');
        $this->userTypeService = $this->_helper->service('user_type');
    }

    public function indexAction()
    {
        $this->view->users = $this->userService->findAll();
        $this->view->actions = array(
            array(
                'label' => getGS('Create new account'),
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'create',
                'class' => 'add',
                'resource' => 'user',
                'privilege' => 'manage',
            ),
        );
    }

    public function createAction()
    {
        $form = new Admin_Form_User();
        $form->user_type->setMultioptions($this->userTypeService->getOptions());

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            try {
                $user = $this->userService->create($form->getValues());
                $this->_helper->flashMessenger(getGS("User '$1' created", $user->getUsername()));
                $this->_helper->redirector('update', 'user', 'admin', array(
                    'user' => $user->getId(),
                ));
            } catch (\InvalidArgumentException $e) {
                switch ($e->getMessage()) {
                    case 'username_conflict':
                        $form->username->addError(getGS('Username is used already'));
                        break;

                    case 'email_conflict':
                        $form->email->addError(getGS('Email is used already'));
                        break;
                }
            }
        }

        $this->view->form = $form;
    }

    public function updateAction()
    {
        $user = $this->getUser();
        $this->view->user = $user;
    }

    public function deleteAction()
    {
        try {
            $user = $this->getUser();
            $this->userService->delete($user);
            $this->_helper->flashMessenger(getGS("User '$1' deleted", $user->getUsername()));
        } catch (InvalidArgumentException $e) {
            $this->_helper->flashMessenger(array('error', getGS("You can't delete yourself")));
        }
        $this->_helper->redirector('index');
    }

    /**
     * Get user for given id
     *
     * @return Newscoop\Entity\User
     */
    protected function getUser()
    {
        $id = $this->_getParam('user', false);
        if (!$id) {
            $this->_helper->flashMessenger(array('error', getGS('User id not specified')));
            $this->_helper->redirector('index');
        }

        $user = $this->userService->find($id);
        if (empty($user)) {
            $this->_helper->flashMessenger(array('error', getGS("User with id '$1' not found", $id)));
            $this->_helper->redirector('index');
        }

        return $user;
    }
}
