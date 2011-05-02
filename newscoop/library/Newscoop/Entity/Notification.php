<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

/**
 * Notification entity
 * @Entity(repositoryClass="Newscoop\Entity\Repository\NotificationRepository")
 */
class Notification
{
    /**
     * @var string to code mapper for status
     */
    static $status_enum = array(
        'processed',
        'pending',
        'waiting',
        'deleted'
    );

    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @Column(name="content")
     * @var string
     */
    private $content;

    /**
     * @Column(type="integer")
     * @var string
     */
    private $status;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the content of the notification
     *
     * @param string $p_content
     * @return
     */
    public function setContent($p_content)
    {
        $this->content = $p_content;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Set status string
     *
     * @return Newscoop\Entity\Comment
     */
    public function setStatus($p_status)
    {
        $status_enum = array_flip(self::$status_enum);
        $this->status = $status_enum[$p_status];
        // return this for chaining mechanism
        return $this;
    }
}

