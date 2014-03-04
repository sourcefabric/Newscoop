<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Log")
 */
class Log
{
    /**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", name="time_created")
     * @var DateTime
     */
    protected $created;

    /**
     * @ORM\Column(type="integer", name="fk_event_id")
     * @var int
     */
    protected $eventId;

    /**
     * @ORM\Column(type="integer", name="fk_user_id")
     * @var int
     */
    protected $userId;

    /**
     * @ORM\Column(name="text")
     * @var int
     */
    protected $message;

    /**
     * @ORM\Column(name="user_ip")
     * @var string
     */
    protected $userIp;
}
