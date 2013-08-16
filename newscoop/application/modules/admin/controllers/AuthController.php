<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;

/**
 * @Acl(ignore="1")
 */
class Admin_AuthController extends Zend_Controller_Action
{
    public function init()
    {
        camp_load_translation_strings('users');
    }

    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            Article::UnlockByUser((int) $auth->getIdentity());
            $auth->clearIdentity();
            setcookie('NO_CACHE', 'NO', time()-3600, '/');
            unset($_SESSION['statDisplayed']);
        }

        $this->_helper->FlashMessenger(getGS('You were logged out.'));
        $this->_helper->redirector('index', 'index');
    }
}
