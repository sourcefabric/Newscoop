<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Newscoop\Entity\Event,
    Newscoop\Entity\User;

/**
 * @entity(repositoryClass="Newscoop\Entity\Repository\LogRepository")
 * @table(name="Log")
 */
class Log
{
    /**
     * @id
     * @column(type="string")
     * @var string
     */
    private $time_created;

    /**
     * @id
     * @manyToOne(targetEntity="Event")
     * @joinColumn(name="fk_event_id", referencedColumnName="Id")
     * @var \Newscoop\Entity\Event
     */
    private $event;

    /**
     * @manyToOne(targetEntity="User")
     * @joinColumn(name="fk_user_id", referencedColumnName="Id")
     * @var \Newscoop\Entity\User
     */
    private $user;

    /**
     * @id
     * @column(length=255)
     * @var string
     */
    private $text;

    /**
     * @column(type="integer")
     * @var int
     */
    private $user_ip;

    /**
     * Get creation time.
     *
     * @return string
     */
    public function getTimeCreated()
    {
        return $this->time_created;
    }

    /**
     * Get log text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get client ip.
     *
     * @return string
     */
    public function getClientIP()
    {
        $ip = $this->user_ip;
        $parts = array();

        for ($i = 0; $i < 4; $i++) {
            $parts[] = $ip % 256;
            $ip = $ip / 256;
        }

        return implode('.', array_reverse($parts));
    }

    /**
     * Get log Event
     *
     * @return \Newscoop\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Get log user.
     *
     * @return \Newscoop\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
