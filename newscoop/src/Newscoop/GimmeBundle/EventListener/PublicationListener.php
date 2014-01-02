<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\EventListener;

use Newscoop\Gimme\PublicationService;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Run publication resolver on request
 */
class PublicationListener
{
    /**
     * Publication service
     * @var PublicationService
     */
    private $publicationService;

    /**
     * Contruct PublicationListener object
     * @param PublicationService $publicationService Publication service
     */
    public function __construct(PublicationService $publicationService)
    {
        $this->publicationService = $publicationService;
    }

    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $this->publicationService->poblicationResolver($event->getRequest());
    }
}