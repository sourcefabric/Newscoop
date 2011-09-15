<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\UserPoints;

/**
 * User service
 */
class UserPointsService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Find point value for action
     *
     * @param string $action
     *
     * @return int
     */
    public function getPointValueForAction($action)
    {
        $user_points = $this->find($action);

        if (is_null($user_points)) {
            return 0;
        }

        return $user_points->getPoints();
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
     * @param sfEvent $event
     * @return void
     */
    public function update(\sfEvent $event)
    {
        $params = $event->getParameters();

        $action =  str_replace(".", "_", $event->getName());
        $user = $params['user'];

        $points = $user->getPoints();
        $points_action = $this->getPointValueForAction($action);

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