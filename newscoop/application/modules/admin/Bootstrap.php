<?php

use Newscoop\Log\Writer;

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{
    /**
     * Legacy admin bootstrap
     */
    protected function _initNewscoop()
    {
        global $ADMIN_DIR, $ADMIN, $g_user, $prefix;

        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Content-Type: text/html; charset=UTF-8");

        define('WWW_DIR', realpath(APPLICATION_PATH . '/../'));
        define('LIBS_DIR', WWW_DIR . '/admin-files/libs');
        $GLOBALS['g_campsiteDir'] = WWW_DIR;

        require_once $GLOBALS['g_campsiteDir'] . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'campsite_constants.php';
        require_once CS_PATH_CONFIG . DIR_SEP . 'install_conf.php';

        // goes to install process if configuration files does not exist yet
        if (!file_exists(CS_PATH_CONFIG . DIR_SEP . 'configuration.php')
            || !file_exists(CS_PATH_CONFIG . DIR_SEP . 'database_conf.php')) {
            header('Location: '.$Campsite['SUBDIR'].'/install/');
            exit;
        }

        require_once CS_PATH_CONFIG . DIR_SEP . 'database_conf.php';
        $this->bootstrap('auth');

        require_once CS_PATH_SITE . DIR_SEP . 'include' . DIR_SEP . 'campsite_init.php';
        require_once CS_PATH_SITE . DIR_SEP . 'classes' . DIR_SEP . 'CampTemplateCache.php';

        // check for upgrade
        if (file_exists(CS_PATH_SITE . DIR_SEP . 'upgrade.php')) {
            camp_display_message("Site is down for upgrade. Please initiate upgrade process.");
            echo '<meta http-equiv="Refresh" content="10" />';
            exit;
        }

        // detect extended login/logout files
        $prefix = file_exists(CS_PATH_SITE . DIR_SEP . 'admin-files' . DIR_SEP . 'ext_login.php') ? '/ext_' : '/';

        require_once CS_PATH_SITE . DIR_SEP . $ADMIN_DIR . DIR_SEP . 'camp_html.php';
        require_once CS_PATH_CLASSES . DIR_SEP . 'SecurityToken.php';

        // load if possible before setting camp_report_bug error handler
        // to prevent error messages
        include_once 'HTML/QuickForm.php';
        include_once 'HTML/QuickForm/RuleRegistry.php';
        include_once 'HTML/QuickForm/group.php';

        set_error_handler(function($p_number, $p_string, $p_file, $p_line) {
            global $ADMIN_DIR, $Campsite;

            require_once $Campsite['HTML_DIR'] . "/$ADMIN_DIR/bugreporter/bug_handler_main.php";
            camp_bug_handler_main($p_number, $p_string, $p_file, $p_line);
        }, E_ALL);

        camp_load_translation_strings("api");
        $plugins = CampPlugin::GetEnabled(true);
        foreach ($plugins as $plugin) {
            camp_load_translation_strings("plugin_".$plugin->getName());
        }

        // Load common translation strings
        camp_load_translation_strings('globals');

        require_once $Campsite['HTML_DIR'] . "/$ADMIN_DIR/init_content.php";

        if (file_exists($Campsite['HTML_DIR'] . '/reset_cache')) {
            CampCache::singleton()->clear('user');
            unlink($GLOBALS['g_campsiteDir'] . '/reset_cache');
        }
    }

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
        $front->registerPlugin(new Admin_Controller_Plugin_Auth);
        $front->registerPlugin(new Admin_Controller_Plugin_Acl);

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) { // get current user
            $this->bootstrap('doctrine');
            $doctrine = $this->getResource('doctrine');
            $user = $doctrine->getEntityManager()
                ->find('Newscoop\Entity\User\Staff', $auth->getIdentity());

            // set user for application
            $g_user = $user;
            Zend_Registry::set('user', $user);

            // set view user
            $this->bootstrap('view');
            $view = $this->getResource('view');
            $view->user = $user;

            // set view navigation acl
            $this->bootstrap('acl');
            $acl = $this->getResource('acl')->getAcl();
            $view->navigation()->setAcl($acl);
            $view->navigation()->setRole($user->getRole());
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

    /**
     * Init Log
     */
    protected function _initLog()
    {
        // get entity manager
        $this->bootstrap('doctrine');
        $em = $this->getResource('doctrine')
            ->getEntityManager();

        // create logger
        $writer = new Writer($em);
        return new Zend_Log($writer);
    }

    /**
     * Init view placeholders
     */
    protected function _initPlaceholders()
    {
        $this->bootstrap('view');

        // content title
        $this->view->placeholder('title')
            ->setPrefix('<h1>')
            ->setPostfix('</h1>');

        // content sidebar
        // not using prefix/postfix to detect if is empty
        $this->view->placeholder('sidebar')
            ->setSeparator('</div><div class="sidebar">' . "\n");
    }
}
