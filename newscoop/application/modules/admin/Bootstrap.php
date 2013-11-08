<?php

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{

    /**
     * @param Zend_View
     */
    protected $_view;


    /**
     * Legacy admin bootstrap
     */
    protected function _initNewscoop()
    {
        global $ADMIN, $g_user, $prefix, $Campsite;

        defined('WWW_DIR')
            || define('WWW_DIR', realpath(APPLICATION_PATH . '/../'));

        defined('LIBS_DIR')
            || define('LIBS_DIR', WWW_DIR . '/admin-files/libs');

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
        require_once CS_PATH_SITE . DIR_SEP . 'include' . DIR_SEP . 'campsite_init.php';
        require_once CS_PATH_SITE . DIR_SEP . 'classes' . DIR_SEP . 'CampTemplateCache.php';

        // detect extended login/logout files
        $prefix = file_exists(CS_PATH_SITE . DIR_SEP . 'admin-files' . DIR_SEP . 'ext_login.php') ? '/ext_' : '/';

        require_once CS_PATH_SITE .'/admin-files/camp_html.php';
        require_once CS_PATH_CLASSES . DIR_SEP . 'SecurityToken.php';

        // load if possible before setting camp_report_bug error handler
        // to prevent error messages
        include_once 'HTML/QuickForm.php';
        include_once 'HTML/QuickForm/RuleRegistry.php';
        include_once 'HTML/QuickForm/group.php';

        if (php_sapi_name() !== 'cli') {
            set_error_handler(function($p_number, $p_string, $p_file, $p_line) {
                error_log(sprintf('Newscoop error: %s in %s:%d', $p_string, $p_file, $p_line));

                global $Campsite;
                require_once $Campsite['HTML_DIR'] . "/admin-files/bugreporter/bug_handler_main.php";
                camp_bug_handler_main($p_number, $p_string, $p_file, $p_line);
            }, error_reporting());
        }

        require_once APPLICATION_PATH . "/../admin-files/init_content.php";

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

        // @todo http://framework.zend.com/manual/en/zend.application.available-resources.html
        $view->doctype('HTML5');

        // set help url
        $view->helpUrl = $Campsite['site']['help_url'];

        // set locale
        $locale = isset($_COOKIE['TOL_Language']) ? $_COOKIE['TOL_Language'] : 'en';
        $locale_fix = array(
            'cz' => 'cs',
        );
        $view->locale = isset($locale_fix[$locale]) ? $locale_fix[$locale] : $locale;
    }

    /**
     * Init page title
     */
    protected function _initHeadTitle()
    {
        global $Campsite;

        $title = !empty($Campsite['site']['title']) ? htmlspecialchars($Campsite['site']['title']) : $translator->trans('Newscoop', array(), 'home') . $Campsite['VERSION'];

        $view = $this->getResource('view');
        $view->headTitle($title)
            ->setSeparator(' - ');
    }

    /**
     * Add flash messages to view if any
     */
    protected function _initFlashMessenger()
    {
        $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        if ($flashMessenger->hasMessages())
        {
            $view = $this->getResource('view');
            $view->messages = $flashMessenger->getMessages();
        }

        //$view->getHelper( 'FlashMsg' )->setAdapter( Zend_Controller_Action_HelperBroker::getStaticHelper( 'FlashMessenger' ) );
    }

    /**
     * Init view placeholders
     */
    protected function _initPlaceholders()
    {
        $this->bootstrap('view');
        $this->_view = $view = $this->getResource('view');

        // content title
        $view->placeholder('title')
            ->setPrefix('<h1>')
            ->setPostfix('</h1>');

        // content sidebar
        // not using prefix/postfix to detect if is empty
        $view->placeholder('sidebar')
            ->setSeparator('</div><div class="sidebar">' . "\n");

        Zend_Controller_Front::getInstance()->registerPlugin( new \Newscoop\Controller\Plugin\Js( $this->getOptions() ) );

        $view->addHelperPath(APPLICATION_PATH . '/modules/admin/views/helpers', 'Admin_View_Helper');
        $jsPlaceholder = $view->getHelper('JQueryReady');
        $view->getHelper('JQueryUtils')->setPlaceholder($jsPlaceholder);

    }

    /**
     * Init acl storage
     */
    protected function _initAclStorage()
    {
        $doctrine = Zend_Registry::get('container')->getService('doctrine');

        $this->bootstrap('acl');
        $acl = $this->getResource('acl');

        $storage = new Newscoop\Acl\Storage($doctrine);
        $acl->setStorage($storage);

        Zend_Registry::set('acl', $acl);
    }

    protected function _initNewscoopViewHelpers()
    {
        $this->bootstrap('view');
        $this->getResource('view')->registerHelper(Zend_Registry::get('container')->getService('view.helper.thumbnail'), 'thumbnail');
        $this->getResource('view')->registerHelper(Zend_Registry::get('container')->getService('view.helper.rendition'), 'rendition');
    }
}
