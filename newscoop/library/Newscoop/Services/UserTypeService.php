<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;

/**
 * User type service
 */
class UserTypeService
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
     * Get user type options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = array();
        foreach ($this->getRepository()->findAll() as $userType) {
            $options[$userType->getId()] = $userType->getName();
        }

        return $options;
    }

    /**
     * Get repository for user entity
     *
     * @return Newscoop\Entity\Repository\UserRepository
     */
    protected function getRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\User\Group');
    }
}
