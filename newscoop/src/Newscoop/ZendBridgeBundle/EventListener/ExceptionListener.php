<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Newscoop\ZendBridgeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ExceptionListener.
 *
 */
class ExceptionListener
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof NotFoundHttpException && strpos($_SERVER['REQUEST_URI'], '/api') === false) {
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
            $application->run();

            // don't return to symfony
            die;
        }
    }
}
