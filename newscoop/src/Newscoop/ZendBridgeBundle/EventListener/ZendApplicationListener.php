<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ZendBridgeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Run zend legacy code (zend router, acl etc...)
 */
class ZendApplicationListener
{
    private $container;

    public function __construct($container) {
        $this->container = $container;
        \Zend_Registry::set('container', $this->container);
    }

    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $pos = strpos($request->server->get('REQUEST_URI'), '_profiler');

        // don't call Zend Application for profiler.
        if (false === $pos) {
            // init adodb
            require_once __DIR__ . '/../../../../db_connect.php';

            // Fill zend application options
            $config = $this->container->getParameterBag()->all();
            $application = new \Zend_Application(APPLICATION_ENV);
            $iniConfig = APPLICATION_PATH . '/configs/application.ini';
            if (file_exists($iniConfig)) {
                $userConfig = new \Zend_Config_Ini($iniConfig, APPLICATION_ENV);
                $config = $application->mergeOptions($config, $userConfig->toArray());
            }

            $application->setOptions($config);
            $application->bootstrap();

            \Zend_Registry::set('zend_application', $application);
        }
    }
}
