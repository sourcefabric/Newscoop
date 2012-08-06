<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

/**
 */
class RegisterController extends Zend_Controller_Action
{
    const PLACEHOLDER_COUNT = 6;

    /** @var Newscoop\Services\UserService */
    private $service;

    /** @var Zend_Session_Namespace */
    private $session;

    /** @var Newscoop\Services\UserTokenService */
    private $tokenService;

    /**
     * @var array
     */
    private $allowedRoles = array();

    public function init()
    {
        $this->_helper->contextSwitch
            ->addActionContext('generate-username', 'json')
            ->addActionContext('check-username', 'json')
            ->addActionContext('check-email', 'json')
            ->addActionContext('pending', 'json')
            ->addActionContext('create-user', 'json')
            ->initContext();

        $rolesConfig = $this->_helper->config('role')->allowedRoles;
        $this->allowedRoles = $rolesConfig instanceof Zend_Config ? $rolesConfig->toArray() : array();
    }

    public function indexAction()
    {
        $form = new Application_Form_Register();
        $form->setMethod('POST');

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();
            $users = $this->_helper->service('user')->findBy(array(
                'email' => $values['email'],
            ));

            if (count($users) > 0) {
                $user = array_pop($users);
            } else {
                $user = $this->_helper->service('user')->createPending($values['email']);
            }

            if (!$user->isPending()) {
                $form->email->addError("User with email '$values[email]' is registered already.");
            } else {
                $this->_helper->service('email')->sendConfirmationToken($user);
                $this->_helper->redirector('after');
            }
        }

        $this->view->form = $form;
    }
    
    public function registerAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $values = $request->getParams();
            $users = $this->_helper->service('user')->findBy(array(
                'email' => $values['email'],
            ));

            if (count($users) > 0) {
                $user = array_pop($users);
            } else {
                $user = $this->_helper->service('user')->createPending($values['email']);
            }

            if (isset($values['role']) && in_array($values['role'], $this->allowedRoles)) {
                $this->_helper->service('user')->addUserGroup($user, $values['role']);
            }

            if (!$user->isPending()) {
                $this->_helper->json("Die E-Mail-Adresse '$values[email]' ist bei uns bereits registriert.");
            } else {
                $this->_helper->service('email')->sendConfirmationToken($user);
                $this->_helper->json("OK");
            }
        }
    }
    
    public function createUserAction()
    {
        $parameters = $this->getRequest()->getParams();
        
        $user = $this->_helper->service('user')->findBy(array(
            'email' => $parameters['email'],
        ));
        
        if ($user) {
            echo '0';
            exit;
        } else {
            $user = $this->_helper->service('user')->createPending($parameters['email'], $parameters['first_name'], $parameters['last_name'], $parameters['subscriber_id']);
            
            $this->_helper->service('email')->sendConfirmationToken($user);
            echo '1';
            exit;
        }
    }

    public function afterAction()
    {
    }

    public function confirmAction()
    {
        $user = $this->findPendingUser();
        $token = $this->findUserToken($user);
        if (!$this->_helper->service('user.token')->checkToken($user, $token, 'email.confirm')) { // expired
            $this->_helper->service('email')->sendConfirmationToken($user);
            $this->render('expired');
            return;
        }

        $form = new Application_Form_Confirm();
        $form->setMethod('POST');
        $form->setDefaults(array(
            'first_name' => $user->getFirstName() ?: null,
            'last_name' => $user->getLastName() ?: null,
        ));

        $this->handleConfirmForm($form, $user);

        $this->view->form = $form;
        $this->view->img = $this->getUserImageSrc($user);
    }

    public function generateUsernameAction()
    {
        $this->view->username = $this->_helper->service('user')
            ->generateUsername($this->_getParam('first_name'), $this->_getParam('last_name'));
    }

    /**
     * Test if username is available
     */
    public function checkUsernameAction()
    {
        $this->view->status = $this->_helper->service('user')
            ->checkUsername($this->_getParam('username'));
    }

    /**
     * Test if email is available
     */
    public function checkEmailAction()
    {
        $users = $this->_helper->service('user')->findBy(array(
            'email' => $this->_getParam('email'),
        ));

        if (sizeof($users) > 0) {
            $user = array_pop($users);
            if (!$user->isPending()) {
                $this->view->status = false;
                return;
            }
        }

        $this->view->status = true;
    }

    public function socialAction()
    {
        $userData = $this->_getParam('userData');
        if (!empty($userData->email)) { // try to find user by email
            $user = $this->_helper->service('user')->findOneBy(array('email' => $userData->email));
            if (!empty($user)) { // we have user for given email, add him login
                $this->authSocial($user, $userData);
                $this->_helper->redirector('index', 'dashboard');
            }
        }

        $dummyEmail = sha1($userData->email);
        $user = $this->_helper->service('user')->findOneBy(array('email' => $dummyEmail));
        if (empty($user)) {
            $user = $this->_helper->service('user')->createPending($dummyEmail);
        }

        $form = new Application_Form_Confirm();
        $form->setMethod('POST');
        $form->setDefaults(array(
            'first_name' => $userData->firstName,
            'last_name' => $userData->lastName,
            'username' => $this->_helper->service('user')->generateUsername($userData->firstName, $userData->lastName),
            'email' => $userData->email,
        ));

        $form->removeElement('password');
        $form->removeElement('password_confirm');

        $this->handleConfirmForm($form, $user, $userData);

        $this->view->form = $form;
        $this->view->img = $this->getUserImageSrc($user);
        $this->view->social = true;
        $this->render('confirm');
    }
    
    public function pendingAction()
    {
        if ($this->_getParam('email')) {
            $user = $this->_helper->service('user')->findBy(array('email' => $this->_getParam('email')));
            
            if ($user) {
                $this->view->result = '0';
            }
            else {
                $user = $this->_helper->service('user')->createPending($this->_getParam('email'));
                $this->_helper->service('email')->sendConfirmationToken($user);
                $this->view->result = '1';
            }
        }
        $this->view->result = '0';
    }

    public function expiredAction()
    {
    }

    /**
     * Notify event dispatcher about new user
     *
     * @param Newscoop\Entity\User $user
     * @return void
     */
    private function notifyDispatcher(User $user)
    {
        $this->_helper->service('dispatcher')
            ->notify(new sfEvent($this, 'user.register', array(
            'user' => $user,
        )));
    }

    /**
     * Find pending user
     *
     * @return Newscoop\Entity\User $user
     */
    private function findPendingUser()
    {
        $user = $this->_helper->service('user')->find($this->_getParam('user'));
        if (empty($user)) {
            $this->_helper->flashMessenger(array('error', "User not found"));
            $this->_helper->redirector('index', 'index', 'default');
        }

        if (!$user->isPending()) {
            $this->_helper->flashMessenger(array('error', "User has been activated"));
            $this->_helper->redirector('index', 'index', 'default');
        }

        return $user;
    }

    /**
     * Find token
     *
     * @param Newscoop\Entity\User $user
     * @return string $token
     */
    private function findUserToken(User $user)
    {
        $token = $this->_getParam('token', false);
        if (!$token || !$this->_helper->service('user.token')->hasToken($user, $token, 'email.confirm')) {
            $this->_helper->flashMessenger(array('error', "Invalid token"));
            $this->_helper->redirector('index', 'index', 'default');
        }

        return $token;
    }

    /**
     * Get user image filename
     *
     * @param Newscoop\Entity\User $user
     * @return string
     */
    private function getUserImageFilename(User $user)
    {
        $num = $user->getId() % self::PLACEHOLDER_COUNT;
        return "user_placeholder_{$num}.png";
    }

    /**
     * Get user image src
     *
     * @param Newscoop\Entity\User $user
     * @return string
     */
    private function getUserImageSrc(User $user)
    {
        return $this->view->url(array(
            'src' => $this->getHelper('service')->getService('image')->getSrc('images/' . $this->getUserImageFilename($user), 125, 125, 'fit'),
        ), 'image', false, false);
    }

    /**
     * Handle confirm form
     *
     * @param Application_Form_Confirm $form
     * @param Newscoop\Entity\User $user
     * @param object $userData
     * @return void
     */
    private function handleConfirmForm(Application_Form_Confirm $form, User $user, $userData = null)
    {
        if (!empty($_POST) && empty($_FILES)) {
            $form->removeElement('image');
            unset($_POST['MAX_FILE_SIZE']);
        }

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            try {
                $values = $form->getValues();
                if (!empty($values['image'])) {
                    $imageInfo = array_pop($form->image->getFileInfo());
                    $values['image'] = $this->_helper->service('image')->save($imageInfo);
                } else {
                    $values['image'] = $this->getUserImageFilename($user);
                }

                if ($userData !== null) {
                    $values['email'] = $userData->email;
                }

                $this->_helper->service('user')->savePending($values, $user);
                $this->_helper->service('user.token')->invalidateTokens($user, 'email.confirm');
                $this->notifyDispatcher($user);

                if ($userData !== null) {
                    $this->authSocial($user, $userData);
                }

                $auth = \Zend_Auth::getInstance();
                if ($auth->hasIdentity()) { // show index
                    $this->_helper->flashMessenger('User registered successfully.');
                    $this->_helper->redirector('index', 'index', 'default');
                } else {
                    $adapter = $this->_helper->service('auth.adapter');
                    $adapter->setEmail($user->getEmail())->setPassword($values['password']);
                    $result = $auth->authenticate($adapter);
                    $this->_helper->redirector('index', 'dashboard', 'default', array('first' => 1));
                }
            } catch (\Exception $e) {
                switch ($e->getMessage()) {
                    case 'username_conflict':
                        $form->username->addError('Username is used. Please use another one.');
                        break;

                    default:
                        var_dump($e->getMessage(), $e->getTraceAsString());
                        exit;
                }
            }
        }
    }

    /**
     * Auth via social adapter
     *
     * @param Newscoop\Entity\User $user
     * @param object $userData
     * @return void
     */
    private function authSocial($user, $userData)
    {
        $this->_helper->service('auth.adapter.social')->addIdentity($user, $this->_getParam('provider'), $userData->identifier);
        $adapter = $this->_helper->service('auth.adapter.social');
        $adapter->setProvider($this->_getParam('provider'))->setProviderUserId($userData->identifier);
        Zend_Auth::getInstance()->authenticate($adapter);
    }
}
