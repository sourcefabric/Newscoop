<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\AfterEvent;

class PaginationExtraDataSubscriber implements EventSubscriberInterface
{
    private $paginatorService;

    public function __construct($paginatorService)
    {
        $this->paginatorService = $paginatorService;
    }

    public function after(AfterEvent $event)
    {
        $paginatorData = $event->getPaginationView()->getPaginationData();
        $usedRoute = $event->getPaginationView()->getRoute();
        $this->paginatorService
            ->setUsedRoute($usedRoute)
            ->setPaginationData($paginatorData);
    }

    public static function getSubscribedEvents()
    {
        return array(
            'knp_pager.after' => array('after', 1)
        );
    }
}