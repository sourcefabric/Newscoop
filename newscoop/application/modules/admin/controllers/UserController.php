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
        $this->userService = $this->_helper->service('user');
        $this->userTypeService = $this->_helper->service('user_type');

        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');

        $this->_helper->contextSwitch
            ->addActionContext('index', 'json')
            ->initContext();

        $this->_helper->contextSwitch
            ->addActionContext('table', 'json')
            ->initContext();

    }

    public function tableAction()
    {
        $users = $this->_helper->service('user')->getCollection(
            array('q' => null, 'status' => null, 'groups' => null),
            array('username' => 'asc', 'email' => 'asc'),
            50000,
            null
        );
        $this->view->users = array();
        foreach ($users as $user) {
            $userView = $user->getDataTableView($this->view);
            $this->view->users[] = $userView;
        }
        return;
    }

    public function indexAction()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
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
            $userView = $user->getEditView($this->view);
            $userView->links[] = array(
                'rel' => 'rename',
                'href' => $this->view->url(array(
                    'module' => 'admin',
                    'controller' => 'user',
                    'action' => 'rename',
                    'user' => $userView->id,
                ), 'default', true),
            );
            $this->view->users[] = $userView;
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
                'label' => $translator->trans('Create new account', array(), 'users'),
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'create',
                'class' => 'add',
            ),
        );

        $this->view->activeCount = $this->_helper->service('user')->countBy(array('status' => User::STATUS_ACTIVE));
        $this->view->pendingCount = $this->_helper->service('user')->countBy(array('status' => User::STATUS_INACTIVE));
        $this->view->inactiveCount = $this->_helper->service('user')->countBy(array('status' => User::STATUS_DELETED));
        $this->view->filter = $this->_getParam('filter', '');
    }

    public function listAction()
    {
        $this->_helper->layout->disableLayout();

        $filters = array(
            'active' => User::STATUS_ACTIVE,
            'pending' => User::STATUS_INACTIVE,
            'inactive' => User::STATUS_DELETED,
        );

        $filter = $this->_getParam('filter', 'active');
        if (!array_key_exists($filter, $filters)) {
            $filter = 'active';
        }

        $page = $this->_getParam('page', 1);
        $count = $this->_helper->service('user')->countBy(array('status' => $filters[$filter]));
        $paginator = Zend_Paginator::factory($count);
        $paginator->setItemCountPerPage(self::LIMIT);
        $paginator->setCurrentPageNumber($page);
        $paginator->setView($this->view);
        $paginator->setDefaultScrollingStyle('Sliding');
        $this->view->paginator = $paginator;

        $this->view->users = $this->_helper->service('user')->findBy(array(
            'status' => $filters[$filter],
        ), array(
            'username' => 'asc',
            'email' => 'asc',
        ), self::LIMIT, ($paginator->getCurrentPageNumber() - 1) * self::LIMIT);

        $this->render("list-$filter");
    }

    public function searchAction()
    {
        $this->_helper->layout->disableLayout();

        $q = $this->_getParam('q', null);
        $this->view->users = $this->_helper->service('user.search')->find($q);
    }

    public function createAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $form = new Admin_Form_User();
        $form->user_type->setMultioptions($this->userTypeService->getOptions());
        $form->author->setMultioptions(array('' => $translator->trans('None', array(), 'users')) + $this->_helper->service('author')->getOptions());
        $form->setDefaults(array(
            'is_admin' => $this->_getParam('is_admin', 0),
            'is_public' => $this->_getParam('is_public', 0),
        ));

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            try {
                $user = $this->userService->save($form->getValues());
                $this->_helper->flashMessenger($translator->trans("User $1 created", array('$1' => $user->getUsername()), 'users'));
                $this->_helper->redirector('edit', 'user', 'admin', array(
                    'user' => $user->getId(),
                ));
            } catch (\InvalidArgumentException $e) {
                switch ($e->getMessage()) {
                    case 'username_conflict':
                        $form->username->addError($translator->trans('Username is used already', array(), 'users'));
                        break;

                    case 'email_conflict':
                        $form->email->addError($translator->trans('Email is used already', array(), 'users'));
                        break;
                }
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $form = new Admin_Form_User();
        $form->user_type->setMultioptions($this->userTypeService->getOptions());
        $form->author->setMultioptions(array('' => $translator->trans('None', array(), 'users')) + $this->_helper->service('author')->getOptions());

        $user = $this->getUser();
        $form->setDefaultsFromEntity($user);

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            try {
                $values = $form->getValues();
                $values['attributes'] = array(
                    'is_verified' => $values['is_verified'],
                );
                unset($values['is_verified']);

                $this->userService->save($values, $user);

                $cacheService = \Zend_Registry::get('container')->get('newscoop.cache');
                $cacheService->getCacheDriver()->deleteAll();

                $this->_helper->flashMessenger($translator->trans("User saved", array(), 'users'));
                $this->_helper->redirector('edit', 'user', 'admin', array(
                    'user' => $user->getId(),
                ));
            } catch (\InvalidArgumentException $e) {
                switch ($e->getMessage()) {
                    case 'username_conflict':
                        $form->username->addError($translator->trans('Username is used already', array(), 'users'));
                        break;

                    case 'email_conflict':
                        $form->email->addError($translator->trans('Email is used already', array(), 'users'));
                        break;
                }
            }
        }

        $requestSymfony = \Zend_Registry::get('container')->getService('request');
        $zendRouter = \Zend_Registry::get('container')->getService('zend_router');
        $publicationMetadata = $requestSymfony->attributes->get('_newscoop_publication_metadata');
        $baseUrl = $requestSymfony->getScheme().'://'.$publicationMetadata['alias']['name'].$zendRouter->assemble(array('controller' => 'images'), 'default', true);
        $this->view->form = $form;
        $this->view->user = $user;
        $this->view->image = $this->_helper->service('image')->getSrc('images/' . $user->getImage(), 80, 80, 'crop');
        $this->view->baseImage = $baseUrl.'/'.$user->getImage();
        $this->view->originalImage = $user->getImage();
        $this->view->actions = array(
            array(
                'label' => $translator->trans('Edit permissions', array(), 'users'),
                'module' => 'admin',
                'controller' => 'acl',
                'action' => 'edit',
                'params' => array(
                    'user' => $user->getId(),
                    'role' => $user->getRoleId(),
                ),
            ),
        );
    }

    public function renameAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $user = $this->getUser()->render();
        $form = new Admin_Form_RenameUser();
        $form->setDefaults(array('username', $user->username));

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = (object) $form->getValues();
            $values->userId = $user->id;

            try {
                $this->_helper->service('user')->renameUser($values);
                $this->_helper->flashMessenger->addMessage($translator->trans("User renamed.", array(), 'users'));
                $this->_helper->redirector('rename', 'user', 'admin', array(
                    'user' => $user->id,
                    'filter' => $this->_getParam('filter'),
                ));
            } catch (InvalidArgumentException $e) {
                $form->username->addError($translator->trans("Username is used already", array(), 'users'));
            }
        }

        $this->view->form = $form;
        $this->view->user = $user;
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
        $this->addUserAttributesSubForm($form, $user);

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
        $translator = \Zend_Registry::get('container')->getService('translator');
        $user = $this->_helper->service('user')->getCurrentUser();
        $form = new Admin_Form_EditPassword();
        $form->setMethod('POST');

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $this->_helper->service('user')->save($form->getValues(), $user);
            $this->_helper->flashMessenger($translator->trans('Password updated', array(), 'users'));
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
        $translator = \Zend_Registry::get('container')->getService('translator');
        $id = (int) $this->_getParam('user', false);
        if (!$id) {
            $this->_helper->flashMessenger(array('error', $translator->trans('User id not specified', array(), 'users')));
            $this->_helper->redirector('index');
        }

        $user = $this->userService->find($id);
        if (empty($user)) {
            $this->_helper->flashMessenger(array('error', $translator->trans("User with id $1 not found", array('$1' => $id), 'users')));
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
        $translator = \Zend_Registry::get('container')->getService('translator');
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
                
                $this->_helper->flashMessenger($translator->trans('Ban for user $1 saved.', array('$1' => $p_user->getName()), 'users'));
                
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

    /**
     * Add user attributes subform to form
     *
     * @param Admin_Form_Profile $form
     * @param Newscoop\Entity\User $user
     * @return void
     */
    private function addUserAttributesSubForm(Zend_Form $form, User $user)
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $subForm = new Zend_Form_SubForm();
        $subForm->setLegend($translator->trans('User attributes', array(), 'users'));

        foreach ($user->getRawAttributes() as $key => $val) {
            $subForm->addElement('text', $key, array(
                'label' => $key,
            ));
        }

        $subForm->setDefaults($user->getAttributes());
        $form->addSubForm($subForm, 'attributes');
    }
}
