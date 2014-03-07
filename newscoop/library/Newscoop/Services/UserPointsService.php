<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\UserPoints;
use Newscoop\EventDispatcher\Events\GenericEvent;

/**
 * User service
 */
class UserPointsService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Find UserPoints entity object for this action.
     *
     * @param string $action
     *
     * @return Newscoop\Entity\UserPoints
     */
    public function find($action)
    {
        return $this->getRepository()->findOneBy(array(
            'action' => $action,
        ));
    }

    /**
     * Find all userpoints
     *
     * @return array
     */
    public function findAll()
    {
        return $this->getRepository()
            ->findAll();
    }

    /**
     * Receives notifications of points events.
     *
     * @param GenericEvent $event
     * @return void
     */
    public function update(GenericEvent $event)
    {
        $params = $event->getArguments();

        $action =  str_replace('.', '_', $event->getName());
        $user = $params['user'];

        if (is_int($user)) {
            $user_repo = $this->em->getRepository('Newscoop\Entity\User');
            $user = $user_repo->find($user);
        }

        if (empty($user)) {
            return;
        }

        $points = $user->getPoints();
        $points_action = $this->getRepository()->getPointValueForAction($action);

        $user->setPoints($points+$points_action);

        $this->em->flush();
    }

    /**
     * Update a points entry
     *
     * @param array $values
     * @return void
     */
    public function updateEntries($values)
    {
        $entries = $this->findAll();

        foreach ($entries as $entry) {
            if(isset($values[$entry->getAction()])) {
                $points = $values[$entry->getAction()];
                $entry->setPoints($points);
            }
        }

        $this->em->flush();
    }

    /**
     * Get repository for userpoints entity
     *
     * @return Newscoop\Entity\Repository\UserPointsRepository
     */
    protected function getRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\UserPoints');
    }
}