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
        global $Campsite, $g_user;

        $this->bootstrap('View');
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

        // set user
        $this->view->user = $g_user;
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
     * Add user to view if any
     */
    protected function _initAuth()
    {
        $this->bootstrap('session');
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->view->user = $auth->getIdentity();
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
