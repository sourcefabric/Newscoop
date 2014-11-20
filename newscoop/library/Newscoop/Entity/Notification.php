<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification entity
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\NotificationRepository")
 */
class Notification
{

    /**
     * Comment structure
     */
    const COMMENT_NAME      = 0;
    const COMMENT_EMAIL     = 1;
    const COMMENT_IP        = 2;
    const COMMENT_SUBJECT   = 3;
    const COMMENT_MESSAGE   = 4;

    /**
     * Constants for status
     */
    const STATUS_PENDING    = 0;
    const STATUS_WATING     = 1;
    const STATUS_PROCESSED  = 2;
    const STATUS_DELETED    = 3;

    /**
     * @var string to code mapper for status
     */
    static $status_enum = array(
        self::STATUS_PROCESSED,
        self::STATUS_PENDING,
        self::STATUS_WATING,
        self::STATUS_DELETED,
    );

    /**
     * Constants for status
     */
    const TYPE_COMMENT      = 0;

    /**
     * @var string to code mapper for status
     */
    static $type_enum = array(
        self::TYPE_COMMENT,
    );

    /**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="content")
     * @var string
     */
    protected $content;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $type;
    /**
     * @ORM\Column(type="integer")
     * @var string
     */
    protected $status;

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
     * @return Notification
     */
    public function setContent($p_content)
    {
        $this->content = $p_content;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get the content of the notification
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set status
     *
     * @param int $p_status
     * @return Notification
     */
    public function setStatus($p_status)
    {
        $this->status = $p_status;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set Type of the notification
     * for now only comment type
     *
     * @param int $p_type
     * @return Notification
     */
    public function setType($p_type)
    {
        $this->type = $p_type;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get Type of the notification
     * for now only comment type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

}

