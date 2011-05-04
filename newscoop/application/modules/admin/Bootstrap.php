<?php

use Newscoop\Entity\User\Staff;
use Newscoop\Log\Writer;

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{
    /**
     * Legacy admin bootstrap
     */
    protected function _initNewscoop()
    {
        global $ADMIN_DIR, $ADMIN, $g_user, $prefix;

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

        $this->bootstrap('session');
        require_once CS_PATH_CONFIG . DIR_SEP . 'database_conf.php';
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
        $view = $this->getResource('view');
        Zend_Registry::set('view', $view);

        $view->doctype('HTML5');

        // set help url
        $view->helpUrl = $Campsite['site']['help_url'];

        // set locale
        $locale = $_COOKIE['TOL_Language'] ?: 'en';
        $locale_fix = array(
            'cz' => 'cs',
        );
        $view->locale = $locale_fix[$locale] ?: $locale;
    }

    /**
     * Init page title
     */
    protected function _initHeadTitle()
    {
        global $Campsite;

        $title = !empty($Campsite['site']['title']) ? htmlspecialchars($Campsite['site']['title']) : getGS('Newscoop') . $Campsite['VERSION'];

        $view = $this->getResource('view');
        $view->headTitle($title . ' (powered by Zend)')
            ->setSeparator(' - ');
    }

    /**
     * Add flash messages to view if any
     */
    protected function _initFlashMessenger()
    {
        $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        if ($flashMessenger->hasMessages()) {
            $view = $this->getResource('view');
            $view->messages = $flashMessenger->getMessages();
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
        $view = $this->getResource('view');

        // content title
        $view->placeholder('title')
            ->setPrefix('<h1>')
            ->setPostfix('</h1>');

        // content sidebar
        // not using prefix/postfix to detect if is empty
        $view->placeholder('sidebar')
            ->setSeparator('</div><div class="sidebar">' . "\n");
    }

    /**
     * Init acl storage
     */
    protected function _initAclStorage()
    {
        $this->bootstrap('doctrine');
        $doctrine = $this->getResource('doctrine');

        $this->bootstrap('acl');
        $acl = $this->getResource('acl');

        $storage = new Newscoop\Acl\Storage($doctrine);
        $acl->setStorage($storage);
    }

    /**
     * Init forms translator
     */
    protected function _initForm()
    {
        $translate = new Zend_Translate(array(
            'adapter' => 'array',
            'disableNotices' => TRUE,
            'content' => array(
                "Value is required and can't be empty" => getGS("Value is required and can't be empty"),
                "'%value%' is less than %min% characters long" => getGS("'%value%' is less than %min% characters long"),
                "'%value%' is more than %max% characters long" => getGS("'%value%' is more than %max% characters long"),
            ),
        ));

        Zend_Form::setDefaultTranslator($translate);
    }
}
