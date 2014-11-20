<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * User repository
 */
class UserPointsRepository extends EntityRepository
{

    /**
     * Find point value for action
     *
     * @param string $action
     *
     * @return int
     */
    public function getPointValueForAction($action)
    {
        $user_points = $this->findOneBy(array(
            'action' => $action,
        ));

        if (is_null($user_points)) {
            return 0;
        }

        return $user_points->getPoints();
    }

}