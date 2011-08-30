<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\DoctrineEventDispatcherProxy;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoloader()
    {
        $options = $this->getOptions();
        set_include_path(implode(PATH_SEPARATOR, array_map('realpath', $options['autoloader']['dirs'])) . PATH_SEPARATOR . get_include_path());
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

        $autoloader->pushAutoloader(function($class) {
            require_once 'smarty3/sysplugins/' . strtolower($class) . '.php';
        }, 'Smarty');

        $GLOBALS['g_campsiteDir'] = realpath(APPLICATION_PATH . '/../');

        return $autoloader;
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

    protected function _initContainer()
    {
        $this->bootstrap('autoloader'); $container = new sfServiceContainerBuilder($this->getOptions());

        $this->bootstrap('doctrine');
        $doctrine = $this->getResource('doctrine');
        $container->setService('em', $doctrine->getEntityManager());

        $container->register('user', 'Newscoop\Services\UserService')
            ->addArgument(new sfServiceReference('em'))
            ->addArgument(Zend_Auth::getInstance());

        $container->register('user.list', 'Newscoop\Services\ListUserService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('user_type', 'Newscoop\Services\UserTypeService')
            ->addArgument(new sfServiceReference('em'));

	$container->register('user_points', 'Newscoop\Services\UserPointsService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('audit', 'Newscoop\Services\AuditService')
            ->addArgument(new sfServiceReference('em'))
            ->addArgument(new sfServiceReference('user'));

        $container->register('community_feed', 'Newscoop\Services\CommunityFeedService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('dispatcher', 'sfEventDispatcher')
            ->setConfigurator(function($service) use ($container) {
                foreach ($container->getParameter('listener') as $listener) {
                    $listenerService = $container->getService($listener);
                    $listenerParams = $container->getParameter($listener);
                    foreach ((array) $listenerParams['events'] as $event) {
                        $service->connect($event, array($listenerService, 'update'));
                    }
                }
            });

        $container->register('auth.adapter', 'Newscoop\Services\Auth\DoctrineAuthService')
            ->addArgument(new sfServiceReference('em'));

        Zend_Registry::set('container', $container);
        return $container;
    }

    /**
     * @todo pass container to allow lazy dispatcher loading
     */
    protected function _initEventDispatcher()
    {
        $this->bootstrap('container');
        $container = $this->getResource('container');

        DatabaseObject::setEventDispatcher($container->getService('dispatcher'));
        DatabaseObject::setResourceNames($container->getParameter('resourceNames'));

        $container->getService('em')
            ->getEventManager()
            ->addEventSubscriber(new DoctrineEventDispatcherProxy($container->getService('dispatcher')));
    }

    protected function _initPlugins()
    {
        $options = $this->getOptions();
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Application_Plugin_ContentType());
        $front->registerPlugin(new Application_Plugin_Upgrade());
        $front->registerPlugin(new Application_Plugin_CampPluginAutoload());
        $front->registerPlugin(new Application_Plugin_Auth($options['auth']));
        $front->registerPlugin(new Application_Plugin_Acl($options['acl']));
        $front->registerPlugin(new Application_Plugin_Smarty());
    }

    protected function _initRouter()
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        $router->addRoute(
            'article',
            new Zend_Controller_Router_Route(':language/:issue/:section/:articleNo/:articleUrl', array(
                'module' => 'default',
                'controller' => 'index',
                'action' => 'index',
            )));

        $router->addRoute(
            'section',
            new Zend_Controller_Router_Route(':language/:issue/:section', array(
                'module' => 'default',
                'controller' => 'index',
                'action' => 'index',
            )));

        $router->addRoute(
            'issue',
            new Zend_Controller_Router_Route(':language/:issue', array(
                'module' => 'default',
                'controller' => 'index',
                'action' => 'index',
                'language' => null,
                'issue' => null,
            )));

        $router->addRoute(
            'user',
            new Zend_Controller_Router_Route('user/:username', array(
                'module' => 'default',
                'controller' => 'user',
                'action' => 'profile',
            )));

        $router->addRoute(
            'register',
            new Zend_Controller_Router_Route('register/:action', array(
                'module' => 'default',
                'controller' => 'register',
                'action' => 'index',
            )));

        $router->addRoute(
            'auth',
            new Zend_Controller_Router_Route('auth/:action', array(
                'module' => 'default',
                'controller' => 'auth',
                'action' => 'index',
            )));

        $router->addRoute(
            'admin',
            new Zend_Controller_Router_Route('admin/:controller/:action/*', array(
                'module' => 'admin',
                'controller' => 'index',
                'action' => 'index',
            )));
    }
}
