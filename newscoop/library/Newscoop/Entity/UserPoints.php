<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\Common\Collections\ArrayCollection,
    Newscoop\Utils\Validation;

/**
 * @Entity(repositoryClass="Newscoop\Entity\Repository\UserRepository")
 * @Table(name="user_points_index")
 */
class UserPoints
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer",unique=true,name="id")
     */
    private $id;

    /**
     * @Column(type="string",unique=true,name="action")
     */
    private $action;

    /**
     * @Column(type="integer",unique=false,name="points")
     */
    private $points;


    /**
     * Get action
     *
     * @return string
     */
    public function getAction() {
        return (string) $this->action;
    }

    /**
     * Set action
     *
     * @param int $action
     * @return Newscoop\Entity\UserPoints
     */
    public function setAction($action) {
        $this->action = (string) $action;
        return $this;
    }

    /**
     * Get point value of action
     *
     * @return int
     */
    public function getPoints() {
        return (int) $this->points;
    }

    /**
     * Set point value
     *
     * @param int $points
     * @return Newscoop\Entity\UserPoints
     */
    public function setPoints($points) {
        $this->points = $points;
        return $this;
    }
}