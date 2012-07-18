<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\UserPoints;
use Newscoop\Services\UserService;

/**
 * User service
 */
class UserPointsService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /**
     * @var Newscoop\Services\UserService
     */
    private $userService;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Newscoop\Services\UserService $userService;
     */
    public function __construct(EntityManager $em, UserService $userService)
    {
        $this->em = $em;
        $this->userService = $userService;
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
        switch ($event->getName()) {
            case 'articleauthor.preCreate':
                $user = $this->getEventUser($event);
                $this->addUserPoints($user, 1);
                break;

            case 'articleauthor.preRemove':
                $user = $this->getEventUser($event);
                $this->addUserPoints($user, -1);
                break;
        }
    }

    /**
     * Get user related to author
     *
     * @param sfEvent $event
     * @return Newscoop\Entity\User
     */
    private function getEventUser($event)
    {
        $params = $event->getParameters();
        return $this->userService->findByAuthor($params['id']['authorId']);
    }

    /**
     * Add points to user
     *
     * @param Newscoop\Entity\User $user
     * @param int $points
     * @return void
     */
    private function addUserPoints($user = null, $points = 1)
    {
        if ($user !== null) {
            $user->setPoints($user->getPoints() + (int) $points);
        }
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
            if (isset($values[$entry->getAction()])) {
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
