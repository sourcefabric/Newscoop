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
                if ($this->isCountableAuthorEvent($event)) {
                    $user = $this->getAuthorEventUser($event);
                    $this->addUserPoints($user, 1);
                }
                break;

            case 'articleauthor.preRemove':
                if ($this->isCountableAuthorEvent($event)) {
                    $user = $this->getAuthorEventUser($event);
                    $this->addUserPoints($user, -1);
                }
                break;

            case 'comment.preCreate':
                $user = $this->getCommentEventUser($event);
                $this->addUserPoints($user, 1);
                break;

            case 'comment.preRemove':
                $user = $this->getCommentEventUser($event);
                $this->addUserPoints($user, -1);
                break;
        }
    }

    /**
     * Test if event is related to article type which is being counted
     *
     * @param sfEvent $event
     * @return bool
     */
    private function isCountableAuthorEvent($event)
    {
        $params = $event->getParameters();
        $query = $this->em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('a.type')
            ->where('a.number = :number')
            ->andWhere('a.language = :language')
            ->setMaxResults(1)
            ->getQuery();

        $query->setParameters(array(
            'number' => $params['id']['articleNumber'],
            'language' => $params['id']['languageId'],
        ));

        $type = $query->getSingleScalarResult();
        return in_array($type, array('news', 'blog'));
    }

    /**
     * Get user related to author
     *
     * @param sfEvent $event
     * @return Newscoop\Entity\User
     */
    private function getAuthorEventUser($event)
    {
        $params = $event->getParameters();
        return $this->userService->findByAuthor($params['id']['authorId']);
    }

    /**
     * Get user related to comment
     *
     * @param sfEvent $event
     * @return Newscoop\Entity\User
     */
    private function getCommentEventUser($event)
    {
        $params = $event->getParameters();
        return $params['entity']->getCommenter()->getUser();
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
