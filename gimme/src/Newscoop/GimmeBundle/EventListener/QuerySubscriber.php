<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;
use Knp\Component\Pager\Event\Subscriber\Paginate\Doctrine\ORM\Query\Helper as QueryHelper;
use Doctrine\ORM\Query;

class QuerySubscriber implements EventSubscriberInterface
{
    private $paginationService;

    public function __construct($paginationService)
    {
        $this->paginationService = $paginationService;
    }

    public function sortable(ItemsEvent $event)
    {
        $pagination = $this->paginationService->getPagination();
        if ($event->target instanceof Query) {
            if ($pagination->getSort()) {
                $event->target
                    ->setHint('newscoop.api.sort', $pagination->getSort());

                QueryHelper::addCustomTreeWalker($event->target, 'Newscoop\GimmeBundle\EventListener\Sortable\Doctrine\ORM\Query\OrderByWalker');
            }
        }
    }

    public function selectable(ItemsEvent $event)
    {
        $parialResponse = $this->paginationService->getPartialResponse();
        if ($event->target instanceof Query) {
            if ($parialResponse->getFields()) {
                $event->target
                    ->setHint('newscoop.api.fields', $parialResponse->getFields());

                QueryHelper::addCustomTreeWalker($event->target, 'Newscoop\GimmeBundle\EventListener\Selectable\Doctrine\ORM\Query\SelectWalker');
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'knp_pager.items' => array('sortable', 2),
            'knp_pager.items' => array('selectable', 3)
        );
    }
}