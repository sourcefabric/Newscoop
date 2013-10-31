<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

/**
 */
class UserController extends Zend_Controller_Action
{
    const LIMIT = 8;

    /** @var Newscoop\Services\ListUserService */
    private $service;

    /** @var int */
    private $page;

    public function init()
    {
        $this->service = $this->_helper->service('user.list');

        $this->_helper->contextSwitch()
            ->addActionContext('send-email', 'json')
            ->initContext();

        $this->page = $this->_getParam('page', 1);
        if ($this->page < 1) {
            $this->page = 1;
        }
    }

    public function indexAction()
    {
        $count = $this->service->getActiveUsers(true);
        $users = $this->service->getActiveUsers(false, $this->page, self::LIMIT);

        $this->setViewUsers($users);
        $this->setViewPaginator($count, self::LIMIT);
    }

    public function searchAction()
    {
        $query = $this->_getParam('q');
        $users = $this->service->findUsersBySearch($query);
        $this->setViewUsers($users);
        $this->setViewPaginator(0, self::LIMIT);
        $this->render('index');
    }

    public function filterAction()
    {
        list($from, $to) = array_map('strtolower', explode('-', $this->_getParam('f', 'a-z')));
        $letters = range($from, $to);
        $count = $this->service->findUsersLastNameInRange($letters, true);
        $users = $this->service->findUsersLastNameInRange($letters, false, $this->page, self::LIMIT);
        $this->setViewUsers($users);
        $this->setViewPaginator($count, self::LIMIT);
        $this->render('index');
    }

    public function editorsAction()
    {
        $count = $this->service->getEditorsCount();
        $users = $this->service->findEditors(self::LIMIT, ($this->page - 1) * self::LIMIT);
        $this->setViewUsers($users);
        $this->setViewPaginator($count, self::LIMIT);
        $this->view->active = 'editors';
        $this->render('index');
    }

    /**
     * Set view users
     *
     * @param array $users
     * @return void
     */
    private function setViewUsers($users)
    {
        $this->view->users = array_map(function($user) {
            return new \MetaUser($user);
        }, $users);
    }

    /**
     * Set view paginator
     *
     * @param int $count
     * @param int $perPage
     * @return void
     */
    private function setViewPaginator($count, $perPage)
    {
        $adapter = new Zend_Paginator_Adapter_Null($count);
        $paginator = new Zend_Paginator($adapter);
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        $paginator->setItemCountPerPage($perPage);
        $paginator->setCurrentPageNumber($this->page);
        $this->view->paginator = $paginator->getPages();
    }

    public function profileAction()
    {
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('/js/jquery/fancybox/jquery.fancybox-1.3.4.css'));

        $this->view->headScript()->appendFile($this->view->baseUrl('/js/jquery/fancybox/jquery.fancybox-1.3.4.pack.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/public/js/user_profile.js'));

        $user = $this->getUser($this->_getParam('username'));

        $this->view->user = new MetaUser($user);
        $this->view->profile = $user->getAttributes();
    }

    public function sendEmailAction()
    {   
        $translator = Zend_Registry::get('container')->getService('translator');
        $to = $this->getUser($this->_getParam('username'));
        if (!$to->getAttribute('email_public')) {
            $this->view->error = $translator->trans("User has no public email.");
            return;
        }

        $from = $this->_helper->service('user')->getCurrentUser();
        if (!$from) {
            $this->view->message = $translator->trans("You have to be logged in to send an email.");
            return;
        }

        if ($to->getId() == $from->getId()) {
            $this->view->message = $translator->trans("You can't send email to yourself.");
            return;
        }

        $form = new Application_Form_SendEmail();
        $request = $this->getRequest();
        if ($form->isValid($request->getParams())) {
            $values = $form->getValues();
            $this->_helper->service('email')->sendUserEmail($from->getEmail(), $to->getEmail(), $values['subject'], $values['message']);
            $this->view->status = 1;
        }
    }

    /**
     * Get user by username
     *
     * @param string $username
     * @return Newscoop\Entity\User
     */
    private function getUser($username)
    {
        if (empty($username)) {
            $this->_helper->flashMessenger(array('error', "User '$username' not found"));
            $this->_helper->redirector('index');
        }

        $user = $this->service->findOneBy(array(
            'username' => $username,
        ));

        if (!$user || !$user->isActive() || !$user->isPublic()) {
            $this->_helper->flashMessenger(array('error', "User '$username' not found"));
            $this->_helper->redirector('index');
        }

        return $user;
    }
}
