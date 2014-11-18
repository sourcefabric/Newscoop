<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\UserPointsRepository")
 * @ORM\Table(name="user_points_index")
 */
class UserPoints
{
    /**
     * @ORM\Id 
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer",unique=true,name="id")
     */
    protected $id;

    /**
     * @ORM\Column(type="string",unique=true,name="action")
     */
    protected $action;

    /**
     * @ORM\Column(type="string",unique=true,name="name")
     */
    protected $name;

    /**
     * @ORM\Column(type="integer",unique=false,name="points")
     */
    protected $points;


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
     * @return UserPoints
     */
    public function setAction($action) {
        $this->action = (string) $action;
        return $this;
    }

    /**
     * Get human readable action name
     *
     * @return string
     */
    public function getName() {
        return (string) $this->name;
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
     * @return UserPoints
     */
    public function setPoints($points) {
        $this->points = $points;
        return $this;
    }
}