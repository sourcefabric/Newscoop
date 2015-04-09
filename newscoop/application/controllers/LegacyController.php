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

    public function preDispatch()
    {
        if ($this->getRequest()->getParam('logout') === 'true') {
            $this->_forward('logout', 'auth', 'default', array(
                'url' => $this->getRequest()->getPathInfo(),
            ));
        }
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

        // initializes the campsite object
        $campsite = new CampSite();

        // loads site configuration settings
        $campsite->loadConfiguration(CS_PATH_CONFIG.DIR_SEP.'configuration.php');

        // starts the session
        $campsite->initSession();

        // initiates the context
        $campsite->init();

        // dispatches campsite
        $campsite->dispatch();

        if (APPLICATION_ENV !== 'development' || APPLICATION_ENV !== 'dev') {
            set_error_handler(create_function('', 'return true;'));
        }

        // renders the site
        $campsite->render();

        // triggers an event after displaying
        $campsite->event('afterRender');
    }

    public function postDispatch()
    {}
}
