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
     * Find all userpoints entries in form array("action" => points)
     *
     * @return array
     */
    public function getPointOptions()
    {
        $options = array();
        foreach ($this->getRepository()->findAll() as $userPoint) {
            $options[$userPoint->getAction()] = $userPoint->getPoints();
        }

        return $options;
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