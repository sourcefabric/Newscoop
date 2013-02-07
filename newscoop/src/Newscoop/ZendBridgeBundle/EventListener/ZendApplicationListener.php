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
 * Run publication resolver on request
 */
class ZendApplicationListener
{   
    
    public function onRequest(GetResponseEvent $event)
    {
        // boot zend app
        require_once __DIR__ . '/../../../../application.php';
        $application->bootstrap();
    }
}