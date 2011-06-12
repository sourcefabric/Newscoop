<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Legacy controller
 */
class LegacyController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->disableLayout();
    }

    public function indexAction()
    {
        global $controller;
        $controller = $this;
	
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'include'
            .DIRECTORY_SEPARATOR.'campsite_constants.php');
        require_once(CS_PATH_CONFIG.DIR_SEP.'install_conf.php');

        $local_path = dirname(__FILE__) . '/include';
        set_include_path($local_path . PATH_SEPARATOR . get_include_path());

        require_once(CS_PATH_INCLUDES.DIR_SEP.'campsite_init.php');

        if (file_exists(CS_PATH_SITE . DIR_SEP . 'reset_cache')) {
            CampCache::singleton()->clear('user');
            @unlink(CS_PATH_SITE . DIR_SEP . 'reset_cache');
        }

        // initializes the campsite object
        $campsite = new CampSite();

        // loads site configuration settings
        $campsite->loadConfiguration(CS_PATH_CONFIG.DIR_SEP.'configuration.php');

        // starts the session
        $campsite->initSession();

        if (file_exists(CS_PATH_SITE.DIR_SEP.'upgrade.php')) {
            $this->upgrade();
            exit(0);
        }

        // initiates the context
        $campsite->init();

        // dispatches campsite
        $campsite->dispatch();

        // triggers an event before render the page.
        // looks for preview language if any.
        $previewLang = $campsite->event('beforeRender');
        if (!is_null($previewLang)) {
            require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/SyntaxError.php');
            set_error_handler('templateErrorHandler');

            // loads translations strings in the proper language for the error messages display
            camp_load_translation_strings('preview', $previewLang);
        } else {
	        set_error_handler(create_function('', 'return true;'));
        }

        // renders the site
        $campsite->render();

        // triggers an event after displaying
        $campsite->event('afterRender');
    }

    public function postDispatch()
    {
        // run internal cron scheduler
        if (SystemPref::Get('ExternalCronManagement') == 'N') {
            camp_cron();
        }
    }

    private function upgrade()
    {
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");

        camp_display_message("The website you are trying to view is currently down for maintenance.
            <br>Normal service will resume shortly.");
        echo '<META HTTP-EQUIV="Refresh" content="10">';
    }
}
