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
    const LIMIT = 14;

    /** @var Newscoop\Services\UserService */
    private $service;

    /** @var int */
    private $page;

    /** @var int */
    private $offset;

    public function init()
    {
        $this->service = $this->_helper->service('user');

        $this->_helper->contextSwitch()
            ->addActionContext('send-email', 'json')
            ->initContext();

        $this->page = $this->_getParam('page', 1);
        if ($this->page < 1) {
            $this->page = 1;
        }

	$this->offset = ($this->page - 1) * self::LIMIT;
    }

    public function indexAction()
    {
        $count = $this->service->countPublicUsers();
        $users = $this->service->findPublicUsers(self::LIMIT, $this->offset);

        $this->setViewUsers($users);
        $this->setViewPaginator($count, self::LIMIT);
	$this->view->current = $this->_getParam('action');
    }

    public function activeAction()
    {
        $count = $this->service->getActiveUsers(true);
        $users = $this->service->getActiveUsers(false, $this->page, self::LIMIT);

        $this->setViewUsers($users);
        $this->setViewPaginator($count, self::LIMIT);
        $this->view->current = $this->_getParam('action');
        $this->render('index');
    }

    public function verifiedAction()
    {
        $count = $this->service->getVerifiedUsers(true);
        $users = $this->service->getVerifiedUsers(false, $this->page, self::LIMIT);

        $this->setViewUsers($users);
        $this->setViewPaginator($count, self::LIMIT);
        $this->view->current = $this->_getParam('action');
        $this->render('index');
    }

    public function searchAction()
    {
        $query = $this->_getParam('q');
        $count = $this->service->findUsersBySearch($query, true);
        $users = $this->service->findUsersBySearch($query, false, $this->page, self::LIMIT);

        $this->setViewUsers($users);
        $this->setViewPaginator($count, self::LIMIT);
	    $this->view->current = $this->_getParam('action');
        $this->render('index');
    }

    public function filterAction()
    {
        $character = $this->_getParam('f', 'a');
        if (empty($character) || strlen($character) > 1) {
            $this->_helper->redirector('index');
        }

        $count = $this->service->countByUsernameFirstCharacter($character);
        $users = $this->service->findByUsernameFirstCharacter($character, self::LIMIT, $this->offset);
        $this->setViewUsers($users);
        $this->setViewPaginator($count, self::LIMIT);
	$this->view->current = $this->_getParam('action');
	$this->view->currentCharacter = $this->_getParam('f');
        $this->render('index');
    }

    public function editorsAction()
    {
        $count = $this->service->getEditorsCount();
        $users = $this->service->findEditors(self::LIMIT, $this->offset);
        $this->setViewUsers($users);
        $this->setViewPaginator($count, self::LIMIT);
	$this->view->current = $this->_getParam('action');
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

        if ($this->view->user->is('politician')) {
            $this->view->questions = $this->getPoliticianQuestions();
            $this->view->labels = $this->getFormLabels();
        }
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

    /**
     * Get questions for politicians
     *
     * @return array
     */
    private function getPoliticianQuestions()
    {
        $questions = array();
        $config = new Zend_Config_Xml($this->_helper->theme->getFilePath('_forms/questionnaire.xml'));
        foreach ($config->form->elements as $name => $element) {
            $questions[$name] = $element->options->label;
        }

        return $questions;
    }

    /**
     * Get form labels
     *
     * @return array
     */
    private function getFormLabels()
    {
        $labels = array();
        $config = new Zend_Config_Xml($this->_helper->theme->getFilePath('_forms/profile.xml'), 'politician');
        foreach ($config->profile->elements as $name => $element) {
            if (isset($element->options) && isset($element->options->multioptions)) {
                $labels[$name] = $element->options->multioptions->toArray();
            }
        }

        return $labels;
    }
}
