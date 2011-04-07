<?php

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{
    /** @var Zend_View */
    private $view = NULL;

    /**
     * Init doctype & view - first function using it
     */
    protected function _initDoctype()
    {
        global $Campsite;

        $this->bootstrap('view');
        $this->view = $this->getResource('view');

        $this->view->doctype('HTML5');

        // set help url
        $this->view->helpUrl = $Campsite['site']['help_url'];

        // set locale
        $locale = $_COOKIE['TOL_Language'] ?: 'en';
        $locale_fix = array(
            'cz' => 'cs',
        );
        $this->view->locale = $locale_fix[$locale] ?: $locale;
    }

    /**
     * Init page title
     */
    protected function _initHeadTitle()
    {
        global $Campsite;

        $title = !empty($Campsite['site']['title']) ? htmlspecialchars($Campsite['site']['title']) : getGS('Newscoop') . $Campsite['VERSION'];

        $this->view->headTitle($title . ' (powered by Zend)')
            ->setSeparator(' - ');
    }

    /**
     * Init auth
     */
    protected function _initAuth()
    {
        global $g_user;

        $this->bootstrap('session');
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Newscoop\Auth\Plugin);

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $doctrine = Zend_Registry::get('doctrine');
            $user = $doctrine->getEntityManager()
                ->find('Newscoop\Entity\User\Staff', $auth->getIdentity());

            $g_user = $user;
            Zend_Registry::set('user', $user);

            // set user to view
            $this->bootstrap('view');
            $this->view->user = $user;
        }
    }

    /**
     * Add flash messages to view if any
     */
    protected function _initFlashMessenger()
    {
        $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        if ($flashMessenger->hasMessages()) {
            $this->view->messages = $flashMessenger->getMessages();
        }
    }
}
