<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\Entity\User;

/**
 * User controller
 *
 * @Acl(action="manage")
 */
class Admin_UserController extends Zend_Controller_Action
{
    const LIMIT = 25;

    /** @var Newscoop\Services\UserService */
    private $userService;

    /** @var Newscoop\Services\UserTypeService */
    private $userTypeService;

    /**
     */
    public function init()
    {
        camp_load_translation_strings('users');

        $this->userService = $this->_helper->service('user');
        $this->userTypeService = $this->_helper->service('user_type');

        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');

        $this->_helper->contextSwitch
            ->addActionContext('index', 'json')
            ->initContext();
    }

    public function indexAction()
    {
        $form = new Admin_Form_UserCriteria();
        $form->groups->addMultiOptions($this->_helper->service('user')->getGroupOptions());
        $form->isValid($this->getRequest()->getParams());

        $criteria = $form->getValues();
        if ($criteria['status'] === null) {
            $criteria['status'] = 1;
        }

        $users = $this->_helper->service('user')->getCollection(
            $criteria,
            array('username' => 'asc', 'email' => 'asc'),
            self::LIMIT,
            $this->_getParam('start')
        );

        $this->view->pagination = $users;
        $this->view->criteria = (object) array_filter($criteria, function ($value) { return $value !== null; });
        $this->view->users = array();
        foreach ($users as $user) {
            $this->view->users[] = $user->getEditView($this->view);
        }

        if ($this->_helper->contextSwitch->getCurrentContext() === 'json') {
            return;
        }

        $this->view->counts = array();
        foreach ($form->status->getMultiOptions() as $status => $label) {
            $this->view->counts[$status] = $this->_helper->service('user')->countBy(array('status' => $status));
        }

        $this->view->form = $form;
        $this->view->actions = array(
            array(
                'label' => getGS('Create new account'),
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'create',
                'class' => 'add',
            ),
        );
    }

    public function createAction()
    {
        $form = new Admin_Form_User();
        $form->user_type->setMultioptions($this->userTypeService->getOptions());
        $form->author->setMultioptions(array('' => getGS('None')) + $this->_helper->service('author')->getOptions());
        $form->setDefaults(array(
            'is_admin' => $this->_getParam('is_admin', 0),
            'is_public' => $this->_getParam('is_public', 0),
        ));

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            try {
                $user = $this->userService->save($form->getValues());
                $this->_helper->flashMessenger(getGS("User '$1' created", $user->getUsername()));
                $this->_helper->redirector('edit', 'user', 'admin', array(
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

    public function editAction()
    {
        $form = new Admin_Form_User();
        $form->user_type->setMultioptions($this->userTypeService->getOptions());
        $form->author->setMultioptions(array('' => getGS('None')) + $this->_helper->service('author')->getOptions());

        $user = $this->getUser();
        $form->setDefaultsFromEntity($user);

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            try {
                $this->userService->save($form->getValues(), $user);
                $this->_helper->flashMessenger(getGS("User saved"));
                $this->_helper->redirector('edit', 'user', 'admin', array(
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
        $this->view->user = $user;
        $this->view->image = $this->_helper->service('image')->getSrc('images/' . $user->getImage(), 80, 80, 'crop');
        $this->view->actions = array(
            array(
                'label' => getGS('Edit permissions'),
                'module' => 'admin',
                'controller' => 'acl',
                'action' => 'edit',
                'params' => array(
                    'user' => $user->getId(),
                    'role' => $user->getRoleId(),
                ),
            ),
            array(
                'label' => getGS('Edit subscriptions'),
                'module' => 'admin',
                'controller' => 'subscription',
                'action' => 'index',
                'class' => 'iframe',
                'params' => array(
                    'user' => $user->getId(),
                ),
            ),
        );
    }

    public function deleteAction()
    {
        $this->_helper->contextSwitch->addActionContext($this->_getParam('action'), 'json')->initContext();
        try {
            $user = $this->_helper->service('user')->find($this->_getParam('user', null));
            $this->userService->delete($user);
        } catch (Exception $e) {
            $this->view->message = $e->getMessage();
        }
    }

    public function profileAction()
    {
        $this->_helper->layout->setLayout('iframe');

        $form = new Admin_Form_Profile();
        $user = $this->getUser();

        $formProfile = new Application_Form_Profile();
        $formProfile->setDefaultsFromEntity($user);
        $form->addSubform($formProfile->getSubform('attributes'), 'attributes');

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();

            try {
                if (!empty($values['image'])) {
                    $imageInfo = array_pop($form->image->getFileInfo());
                    $values['image'] = $this->_helper->service('image')->save($imageInfo);
                    $this->view->image = $this->_helper->service('image')->getSrc($values['image'], $this->_getParam('width', 80), $this->_getParam('height', 80));
                } else {
                    unset($values['image']);
                }
                $this->_helper->service('user')->save($values, $user);
                $this->view->close = true;
            } catch (\InvalidArgumentException $e) {
                $form->image->addError($e->getMessage());
            }
        }

        $this->view->form = $form;
    }

    /**
     * @Acl(ignore=true)
     */
    public function editPasswordAction()
    {
        $user = $this->_helper->service('user')->getCurrentUser();
        $form = new Admin_Form_EditPassword();
        $form->setMethod('POST');

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $this->_helper->service('user')->save($form->getValues(), $user);
            $this->_helper->flashMessenger(getGS('Password updated'));
            $this->_helper->redirector('edit-password', 'user', 'admin');
        }

        $this->view->form = $form;
    }

    /**
     * Get user for given id
     *
     * @return Newscoop\Entity\User
     */
    protected function getUser()
    {
        $id = (int) $this->_getParam('user', false);
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
    
    public function toggleBanAction()
    {
        $parameters = $this->getRequest()->getParams();
        
        $userRepository = $this->_helper->entity->getRepository('Newscoop\Entity\User');
        $publicationRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Publication');
        $acceptanceRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Comment\Acceptance');
        
        if (!isset($parameters['user']) && !isset($parameters['publication'])) {
            throw new InvalidArgumentException;
        }
        
        $user = $userRepository->find($parameters['user']);
        $publication = $publicationRepository->find($parameters['publication']);
            
        $form = new Admin_Form_BanUser;
        $this->handleBanForm($form, $user, $publication);
        
        $banned = $acceptanceRepository->checkBanned(array('name' => $user->getName(), 'email' => $user->getEmail(), 'ip' => ''), $publication);
        
        $form->setValues($user, $banned);
        $this->view->form = $form;
    }

    public function sendConfirmEmailAction()
    {
        $this->_helper->contextSwitch->addActionContext($this->_getParam('action'), 'json')->initContext();
        $user = $this->_helper->service('user')->find($this->_getParam('user', null));
        if ($user && $user->isPending()) {
            $this->_helper->service('email')->sendConfirmationToken($user);
        }
    }
    
    /**
     * Method for saving a banned
     *
     * @param ZendForm $p_form
     * @param Newscoop\Entity\User $p_user
     */
    private function handleBanForm(Admin_Form_BanUser $p_form, $p_user, $p_publication)
    {
        if ($this->getRequest()->isPost() && $p_form->isValid($_POST)) {
            if ($p_form->getSubmit()->isChecked()) {
                $parameters = $p_form->getValues();
                $banValues = array();
                $unbanValues = array();
                if ($parameters['name'] == 1) $banValues['name'] = $p_user->getName();
                else $unbanValues['name'] = $p_user->getName();
                if ($parameters['email'] == 1) $banValues['email'] = $p_user->getEmail();
                else $unbanValues['email'] = $p_user->getEmail();
                
                $acceptanceRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Comment\Acceptance');
                $acceptanceRepository->ban($p_publication, $banValues);
                $acceptanceRepository->flush();
                $acceptanceRepository->unban($p_publication, $unbanValues);
                $acceptanceRepository->flush();
                
                $this->_helper->flashMessenger(getGS('Ban for user "$1" saved.', $p_user->getName()));
                
                if ($parameters['delete_messages'] == 1) {
					$feedbackRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Feedback');
					$feedbacks = $feedbackRepository->getByUser($p_user->getId());
					
					$feedbackRepository->setStatus($feedbacks, 'deleted');
					$feedbackRepository->flush();
				}
            }
            $this->_helper->redirector->gotoSimple('index', 'feedback');
        }
    }
}
