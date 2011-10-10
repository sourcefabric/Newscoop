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
    /** @var Newscoop\Services\ListUserService */
    private $service;

    public function init()
    {
        $this->service = $this->_helper->service('user.list');

        $this->_helper->contextSwitch()
            ->addActionContext('send-email', 'json')
            ->initContext();
    }

    public function indexAction()
    {
        $range = $this->_getParam('user-listing', NULL);
        $search = $this->_getParam('users_search', NULL);
        $page = $this->_getParam('page', 1);

        $active = false;
        if (is_null($range) && is_null($search)) {
            $active = true;
        }

        $items_per_page = 25;
        $count = null;

        if($active === true) {
            $items_per_page = 8;

            $count = $this->service->getActiveUsers(true);
            $users = $this->service->getActiveUsers(false, $page, $items_per_page);

            $link_name = "user-active";
            $link_data = array();
        }
        else if (isset($search)) {
            $users = $this->service->findUsersBySearch($search);

            $link_name = "user-search";
            $link_data = array('search' => $search);
        }
        //order users by lastname A-D
        else {
            $array_range = explode("-", $range);
            $letters = range(strtolower($array_range[0]), strtolower($array_range[1]));

            $count = $this->service->findUsersLastNameInRange($letters, true);
            $users = $this->service->findUsersLastNameInRange($letters, false, $page, $items_per_page);

            $link_name = "user-list";
            $link_data = array('user-listing' => $range);
        }

        $this->view->users = array();
        foreach ($users as $user) {
            $this->view->users[] = new MetaUser($user);
        }


        //test the paginator with our views.
        $adapter = new Zend_Paginator_Adapter_Null($count);
        $paginator = new Zend_Paginator($adapter);

        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        $paginator->setItemCountPerPage($items_per_page);
        //$paginator->setItemCountPerPage(1);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator->getPages();
        $this->view->link_name = $link_name;
        $this->view->link_data = $link_data;
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
        $to = $this->getUser($this->_getParam('username'));
        if (!$to->getAttribute('email_public')) {
            $this->view->error = $this->view->translate("User has no public email.");
            return;
        }

        $from = $this->_helper->service('user')->getCurrentUser();
        if (!$from) {
            $this->view->message = $this->view->translate("You have to be logged in to send an email.");
            return;
        }

        if ($to->getId() == $from->getId()) {
            $this->view->message = $this->view->translate("You can't send email to yourself.");
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
