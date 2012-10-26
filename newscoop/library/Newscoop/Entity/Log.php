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
    private $id;

    /**
     * @ORM\Column(type="datetime", name="time_created")
     * @var DateTime
     */
    private $created;

    /**
     * @ORM\Column(type="integer", name="fk_event_id")
     * @var int
     */
    private $eventId;

    /**
     * @ORM\Column(type="integer", name="fk_user_id")
     * @var int
     */
    private $userId;

    /**
     * @ORM\Column(name="text")
     * @var int
     */
    private $message;

    /**
     * @ORM\Column(name="user_ip")
     * @var string
     */
    private $userIp;
}
