<?php

use Doctrine\ORM\Mapping\ClassMetadataFactory,
    Doctrine\ORM\Tools\SchemaTool,
    Newscoop\DoctrineEventDispatcherProxy,
    Newscoop\Services\UserService,
    Newscoop\Services\AuditService;

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

        // autoload symfony service container
        $autoloader->pushAutoloader(function($class) {
            require_once APPLICATION_PATH . "/../library/fabpot-dependency-injection-07ff9ba/lib/{$class}.php";
        }, 'sfService');

        // autoload symfony event dispatcher
        $autoloader->pushAutoloader(function($class) {
            require_once APPLICATION_PATH . "/../library/fabpot-event-dispatcher-782a5ef/lib/{$class}.php";
        }, 'sfEvent');

        // fix adodb loading error
        $autoloader->pushAutoloader(function($class) {
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

    protected function _initContainer()
    {
        $container = new sfServiceContainerBuilder($this->getOptions());

        $this->bootstrap('doctrine');
        $doctrine = $this->getResource('doctrine');
        $container->setService('em', $doctrine->getEntityManager());

        $container->register('user', 'Newscoop\Services\UserService')
            ->addArgument(new sfServiceReference('em'))
            ->addArgument(Zend_Auth::getInstance());

        $container->register('audit', 'Newscoop\Services\AuditService')
            ->addArgument(new sfServiceReference('em'))
            ->addArgument(new sfServiceReference('user'));

        $container->register('dispatcher', 'sfEventDispatcher')
            ->setConfigurator(function($service) use ($container) {
                DatabaseObject::setEventDispatcher($service);
                DatabaseObject::setResourceNames($container->getParameter('resourceNames'));

                $container->getService('em')
                    ->getEventManager()
                    ->addEventSubscriber(new DoctrineEventDispatcherProxy($service));
            });

        $container->register('auth.adapter', 'Newscoop\Services\Auth\DoctrineAuthService')
            ->addArgument(new sfServiceReference('em'));

        return $container;
    }

    protected function _initAuditService()
    {
        $this->bootstrap('container');
        $container = $this->getResource('container');

        $config = $container->getParameter('audit');
        foreach ((array) $config['events'] as $event) {
            $container->getService('dispatcher')
                ->connect($event, array($container->getService('audit'), 'update'));
        }

        return $container->getService('audit');
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
