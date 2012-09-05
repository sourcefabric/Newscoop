<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 * @Entity
 * @Table(name="Log")
 */
class Log
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @Column(type="datetime", name="time_created")
     * @var DateTime
     */
    private $created;

    /**
     * @Column(type="integer", name="fk_event_id")
     * @var int
     */
    private $eventId;

    /**
     * @Column(type="integer", name="fk_user_id")
     * @var int
     */
    private $userId;

    /**
     * @Column(name="text")
     * @var int
     */
    private $message;

    /**
     * @Column(name="user_ip")
     * @var string
     */
    private $userIp;
}
