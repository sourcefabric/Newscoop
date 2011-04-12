<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use DateTime,
    InvalidArgumentException,
    Newscoop\Entity\CommentsUsers,
    Newscoop\Entity\Languages,
    Newscoop\Entity\Publications;

/**
 * Comments entity
 * @entity
 * @table(name="Comments")
 * @entity(repositoryClass="Newscoop\Entity\Repository\CommentsRepository")
 */
class Comments
{
    /**
     * @var string to code mapper for status
     */
    static $status_enum = array(
        'approved',
        'pending',
        'hidden',
        'deleted'
    );

    /**
     * @id @generatedValue
     * @column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @manyToOne(targetEntity="CommentsUsers")
     * @joinColumn(name="fk_comments_user_id", referencedColumnName="id")
     * @var Newscoop\Entity\CommentsUsers
     */
    private $user;

    /**
     * @manyToOne(targetEntity="Publications")
     * @joinColumn(name="fk_forum_id", referencedColumnName="Id")
     * @var Newscoop\Entity\Publications
     */
    private $forum;

    /**
     * @manyToOne(targetEntity="Comments")
     * @joinColumn(name="fk_parent_id", referencedColumnName="id")
     * @var Newscoop\Entity\Comments
     */
    private $parent;

    /**
     * @manyToOne(targetEntity="Articles")
     * @joinColumn(name="fk_thread_id", referencedColumnName="Number")
     * @var Newscoop\Entity\Articles
     */
    private $thread;

    /**
     * @manyToOne(targetEntity="Languages")
     * @joinColumn(name="fk_language_id", referencedColumnName="Id")
     * @var Newscoop\Entity\Languages
     */
    private $language;

    /**
     * @column(length=140)
     * @var string
     */
    private $subject;

    /**
     * @column
     * @var text
     */
    private $message;

    /**
     * @column(length=4)
     * @var int
     */
    private $thread_level;

    /**
     * @column(length=4)
     * @var int
     */
    private $thread_order;

    /**
     * @column(length=2)
     * @var int
     */
    private $status;

    /**
     * @column(length=39)
     * @var int
     */
    private $ip;

    /**
     * @column(type="datetime")
     * @var DateTime
     */
    private $time_created;

    /**
     * @column(length=4)
     * @var int
     */
//    private $likes;

    /**
     * @column(length=4)
     * @var int
     */
//    private $dislikes;

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
     * Set timecreated
     *
     * @param DateTime $p_datetime
     * @return Newscoop\Entity\Comments
     */
    public function setTimeCreated(DateTime $p_datetime)
    {
        $this->time_created = $p_datetime;
        // return this for chaining mechanism
        return $this;
    }


    /**
     * Get creation time.
     *
     * @return DateTime
     */
    public function getTimeCreated()
    {
        return $this->time_created;
    }


    /**
     * Set comment subject.
     *
     * @param string $p_subject
     * @return Newscoop\Entity\Comments
     */
    public function setSubject($p_subject)
    {
        $this->subject = (string) $p_subject;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get comment subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set comment message.
     *
     * @param string $p_message
     * @return Newscoop\Entity\Comments
     */
    public function setMessage($p_message)
    {
        $this->message = (string) $p_message;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get comment message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set comment ip address
     *
     * @param string $p_ip
     * @return Newscoop\Entity\Comments
     */
    public function setIp($p_ip){
        // remove subnet & limit to IP_LENGTH
        $ip_array = explode('/', (string) $p_ip);
        $this->ip = substr($ip_array[0], 0, 39);
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get comment ip address
     *
     * @return string
     */
    public function getIp()
    {
        if (is_numeric($this->ip)) { // try to use old format
            static $max = 0xffffffff; // 2^32
            if($this->ip > 0 && $this->ip < $max)
            {
                return long2ip($this->ip);
            }
        }
        return (string)$this->ip;
    }

    /**
     * Set user
     *
     * @param Newscoop\Entity\User $user
     * @return Newscoop\Entity\Comments
     */
    public function setUser(CommentsUsers $p_user)
    {
        $this->user = $p_user;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get user
     *
     * @return Newscoop\Entity\CommentsUsers
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get user name
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->getUser()->getName();
    }

    /**
     * Set status string
     *
     * @return Newscoop\Entity\Comments
     */
    public function setStatus($p_status)
    {
        $status_enum = array_flip(self::$status_enum);
        $this->status = $status_enum[$p_status];
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get status string
     *
     * @return string
     */
    public function getStatus()
    {
        return self::$status_enum[$this->status];
    }

    /**
     * Set forum
     *
     * @return Newscoop\Entity\Comments
     */
    public function setForum(Publications $p_forum)
    {
        $this->forum = $p_forum;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get thread
     *
     * @return Newscoop\Entity\Publications
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * Set thread
     *
     * @return Newscoop\Entity\Comments
     */
    public function setThread(Articles $p_thread)
    {
        $this->thread = $p_thread;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get thread
     *
     * @return Newscoop\Entity\Articles
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set thread level
     *
     * @return Newscoop\Entity\Comments
     */
    public function setThreadLevel($p_level)
    {
        $this->thread_level = $p_level;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get thread level
     *
     * @return integer
     */
    public function getThreadLevel()
    {
        return $this->thread_level;
    }

    /**
     * Set thread order
     *
     * @return Newscoop\Entity\Comments
     */
    public function setThreadOrder($p_order)
    {
        $this->thread_order = $p_order;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get thread level
     *
     * @return integer
     */
    public function getThreadOrder()
    {
        return $this->thread_order;
    }

    /**
     * Set Language
     *
     * @return Newscoop\Entity\Comments
     */
    public function setLanguage(Languages $p_language)
    {
        $this->language = $p_language;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get Language
     *
     * @return integer
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set Parent
     *
     * @return Newscoop\Entity\Comments
     */
    public function setParent(Comments $p_parent)
    {
        $this->parent = $p_parent;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get Parent
     *
     * @return Newscoop\Entity\Comments
     */
    public function getParent()
    {
        return $this->parent;
    }


}
