<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoloader()
    {
        return;
    }

    protected function _initSession()
    {
        $options = $this->getOptions();
        if (!empty($options['session'])) {
            Zend_Session::setOptions($options['session']);
        }

        Zend_Session::start();

        foreach ($_COOKIE as $name => $value) { // remove unused cookies
            if ($name[0] == 'w' && strrpos('_height', $name) !== FALSE) {
                setcookie($name, '', time() - 3600);
            }
        }
    }

    /**
     * Name must be _initContainer because bootstrap create resource named "container", 
     * and if we change function name, then resource name will be also changed.
     */
    protected function _initContainer()
    {
        $this->bootstrap('autoloader');
        return Zend_Registry::get('container');
    }

    protected function _initDatabaseObject() 
    {
        $container = $this->getResource('container');
        DatabaseObject::setEventDispatcher($container->getService('event_dispatcher'));
        DatabaseObject::setResourceNames($container->getParameter('resourceNames'));
    }

    protected function _initPlugins()
    {
        $options = $this->getOptions();
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Application_Plugin_ContentType());
        $front->registerPlugin(new Application_Plugin_Upgrade());
        $front->registerPlugin(new Application_Plugin_Auth($options['auth']));
        $front->registerPlugin(new Application_Plugin_Acl($options['acl']));
    }

    protected function _initRouter()
    {
        $this->bootstrap('container');
        $routerFactory = new \Newscoop\Router\RouterFactory();
        $routerFactory->initRouter(\Zend_Registry::get('container'));
    }

    protected function _initActionHelpers()
    {
        require_once APPLICATION_PATH . '/controllers/helpers/Smarty.php';
        Zend_Controller_Action_HelperBroker::addHelper(new Action_Helper_Smarty());
    }

    protected function _initOdm()
    {
        return null;
    }

    /**
     */
    protected function _initLog()
    {
        $writer = new Zend_Log_Writer_Syslog(array('application' => 'Newscoop'));
        $log = new Zend_Log($writer);
        \Zend_Registry::set('log', $log);
        return $log;
    }

    protected function _initAuthStorage()
    {
        $storage = new Zend_Auth_Storage_Session('Zend_Auth_Storage');
        Zend_Auth::getInstance()->setStorage($storage);
    }

    protected function _initAdoDb()
    {
        require_once __DIR__ . '/../db_connect.php';
    }
}
