<?php

use Newscoop\DoctrineEventDispatcherProxy,
    Newscoop\Service;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Init autoloader
     */
    protected function _initAutoloader()
    {
        global $g_campsiteDir;

        $g_campsiteDir = realpath(APPLICATION_PATH . '/../');

        set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../classes/'),
            realpath(APPLICATION_PATH . '/../classes/Extension/'),
            realpath(APPLICATION_PATH . '/../template_engine/classes/'),
            realpath(APPLICATION_PATH . '/../template_engine/metaclasses/'),
            realpath(APPLICATION_PATH . '/../library/Service/'),
        )) . PATH_SEPARATOR . get_include_path());

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(TRUE);

        // fix adodb loading error
        $autoloader->pushAutoloader(function($file) {
            return;
        }, 'ADO');

        // init session before loading plugins to prevent session start errors
        $this->bootstrap('session');

        return $autoloader;
    }

    /**
     * Init session
     */
    protected function _initSession()
    {
        $options = $this->getOptions();
        $name = isset($options['session']['name']) ? $options['session']['name'] : session_name();

        Zend_Session::setOptions(array(
            'name' => $name,
        ));

        Zend_Session::start();
    }

    protected function _initEventDispatcher()
    {
        require_once APPLICATION_PATH . '/../library/fabpot-event-dispatcher-782a5ef/lib/sfEventDispatcher.php';

        $options = $this->getOptions();
        $this->bootstrap('doctrine');
        $doctrine = $this->getResource('doctrine');

        $dispatcher = new sfEventDispatcher();
        $dispatcherProxy = new DoctrineEventDispatcherProxy($dispatcher);
        $doctrine->getEntityManager()
            ->getEventManager()
            ->addEventSubscriber($dispatcherProxy);

        DatabaseObject::setEventDispatcher($dispatcher);
        DatabaseObject::setResourceNames($options['resourceNames']);
        Zend_Registry::set('eventDispatcher', $dispatcher);

        return $dispatcher;
    }

    protected function _initUserService()
    {
        $this->bootstrap('doctrine');
        $doctrine = $this->getResource('doctrine');
        $userRepository = $doctrine->getEntityManager()->getRepository('Newscoop\Entity\User');
        $userService = new Service\User($userRepository, Zend_Auth::getInstance());
        return $userService;
    }

    protected function _initAuditService()
    {
        $this->bootstrap('userService');
        $this->bootstrap('eventDispatcher');

        $options = $this->getOptions();

        $httpClient = new Zend_Http_Client('http://localhost:8080/Audit/', array(
            'maxredirects' => 0,
            'timeout' => 30,
        ));

        $userService = $this->getResource('userService');
        $auditService = new Service\Audit($httpClient, $userService);

        $eventDispatcher = $this->getResource('eventDispatcher');
        foreach ($options['service']['audit']['events'] as $event) {
            $eventDispatcher->connect($event, array($auditService, 'update'));
        }

        return $auditService;
    }

    protected function _initPlugins()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Application_Plugin_ContentType());
        $front->registerPlugin(new Application_Plugin_Upgrade());
        $front->registerPlugin(new Application_Plugin_CampPluginAutoload());
        $front->registerPlugin(new Application_Plugin_Bootstrap($this->getOptions()));
    }
}
