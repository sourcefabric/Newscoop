<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\UserPoints;

/**
 * User repository
 */
class UserPointsRepository extends EntityRepository
{

    /**
     * Save user_points
     *
     * @param Newscoop\Entity\UserPoints $user_points
     * @param array $values
     * @return void
     */
    public function save(UserPoints $user_points, array $values)
    {

        if (!isset($values["action"])) {
            throw new \InvalidArgumentException("action undefined");
        }

        if (!isset($values["points"])) {
            throw new \InvalidArgumentException("points undefined");
        }

        $user_points->setAction($values["action"]);
        $user_points->setPoints($values["points"]);

        $this->getEntityManager()->persist($user_points);
    }

}