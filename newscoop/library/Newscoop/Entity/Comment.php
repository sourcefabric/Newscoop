<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;
use Newscoop\Entity\Comment\Commenter;
use Newscoop\Search\DocumentInterface;
use DateTime;
use Newscoop\Entity\Hierarchable;

/**
 * Comment entity
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\CommentRepository")
 */
class Comment implements DocumentInterface, Hierarchable
{
    protected $allowedEmpty = array( 'br', 'input', 'image' );

    protected $allowedTags =
    array(
        'a' => array('title', 'href'),
        'abbr' => array('title'),
        'acronym' => array('title'),
        'b' => array(),
        'blockquote' => array('cite'),
        'cite' => array(),
        'code' => array(),
        'del' => array('datetime'),
        'em' => array(),
        'i' => array(),
        'q' => array('cite'),
        'p' => array(),
        'br' => array(),
        'strike' => array(),
        'strong' => array(), );

    /**
     * Constants for status
     */
    const STATUS_APPROVED   = 0;
    const STATUS_PENDING    = 1;
    const STATUS_HIDDEN     = 2;
    const STATUS_DELETED    = 3;

    /**
     * @var string to code mapper for status
     */
    static $status_enum = array('approved', 'pending', 'hidden', 'deleted');

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Comment\Commenter", inversedBy="comments", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_comment_commenter_id", referencedColumnName="id")
     * @var Newscoop\Entity\Comment\Commenter
     */
    protected $commenter;

    /**
     * @ORM\ManyToOne(targetEntity="Publication")
     * @ORM\JoinColumn(name="fk_forum_id", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    protected $forum;

    /**
     * @ORM\ManyToOne(targetEntity="Comment")
     * @ORM\JoinColumn(name="fk_parent_id", referencedColumnName="id", onDelete="SET NULL")
     * @var Newscoop\Entity\Comment
     */
    protected $parent;

    /**
     * @ORM\Column(type="integer", name="fk_thread_id")
     * @var int
     */
    protected $thread;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Language")
     * @ORM\JoinColumn(name="fk_language_id", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    protected $language;

    /**
     * @ORM\Column(length=140)
     * @var string
     */
    protected $subject;

    /**
     * @ORM\Column()
     * @var text
     */
    protected $message;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    protected $thread_level;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    protected $thread_order;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    protected $status;

    /**
     * @ORM\Column(length=39)
     * @var int
     */
    protected $ip;

    /**
     * @ORM\Column(type="datetime", name="time_created")
     * @var DateTime
     */
    protected $time_created;

    /**
     * @ORM\Column(type="datetime", name="time_updated")
     * @var DateTime
     */
    protected $time_updated;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    protected $likes = 0;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    protected $dislikes = 0;

    /**
     * @ORM\Column(type="boolean")
     * @var int
     */
    protected $recommended = 0;

    public function __construct()
    {
        $this->setTimeCreated(new \DateTime());
        $this->setTimeUpdated(new \DateTime());
    }

    /**
     * @ORM\Column(type="datetime", nullable=True)
     * @var DateTime
     */
    protected $indexed;

    /**
     * @ORM\Column(type="string", length=60, name="source", nullable=true)
     * @var string
     */
    protected $source;

    /**
     * Set id
     *
     * @param int $id
     *
     * @return Newscoop\Entity\Comment
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
     * @param DateTime $datetime
     *
     * @return Newscoop\Entity\Comment
     */
    public function setTimeCreated(\DateTime $datetime)
    {
        $this->time_created = $datetime;
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
     * Set time updated
     *
     * @param DateTime $datetime
     *
     * @return Newscoop\Entity\Comment
     */
    public function setTimeUpdated(\DateTime $datetime)
    {
        $this->time_updated = $datetime;

        return $this;
    }

    /**
     * Get creation time.
     *
     * @return DateTime
     */
    public function getTimeUpdated()
    {
        return $this->time_updated;
    }

    /**
     * Set comment subject.
     *
     * @param string $subject
     *
     * @return Newscoop\Entity\Comment
     */
    public function setSubject($subject)
    {
        $this->subject = (string) $subject;

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
     * @param string $message
     *
     * @return Newscoop\Entity\Comment
     */
    public function setMessage($message)
    {
        $this->message = $this->formatMessage((string) $message);

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
     * @param string $ip
     *
     * @return Newscoop\Entity\Comment
     */
    public function setIp($ip)
    {
        // remove subnet & limit to IP_LENGTH
        $ipArray = explode('/', $ip);
        $this->ip = substr($ipArray[0], 0, 39);

        return $this;
    }

    /**
     * Get comment ip address
     *
     * @return string
     */
    public function getIp()
    {
        if (is_numeric($this->ip)) {
            // try to use old format
            static $max = 0xffffffff; // 2^32
            if ($this->ip > 0 && $this->ip < $max) {
                return long2ip($this->ip);
            }
        }

        return $this->ip;
    }

    /**
     * Set recommended
     *
     * @param string $recommended
     *
     * @return Newscoop\Entity\Comment
     */
    public function setRecommended($recommended)
    {
        if ($recommended) {
            $this->recommended = 1;
        } else {
            $this->recommended = 0;
        }

        return $this;
    }

    /**
     * Get comment recommended
     *
     * @return string
     */
    public function getRecommended()
    {
        return (bool) $this->recommended;
    }

    /**
     * Set commenter
     *
     * @param Newscoop\Entity\Comment\Commenter $commenter
     *
     * @return Newscoop\Entity\Comment
     */
    public function setCommenter(Commenter $commenter)
    {
        $this->commenter = $commenter;

        return $this;
    }

    /**
     * Get commenter
     *
     * @return Newscoop\Entity\Comment\Commenter
     */
    public function getCommenter()
    {
        return $this->commenter;
    }

    /**
     * Get the commenter's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getCommenterName();
    }

    /**
     * Get commenter name
     *
     * @return string
     */
    public function getCommenterName()
    {
        return $this->getCommenter()->getName();
    }

    /**
     * Get the commenter's email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getCommenterEmail();
    }

    /**
     * Get commenter email
     *
     * @return string
     */
    public function getCommenterEmail()
    {
        return $this->getCommenter()->getEmail();
    }

    /**
     * Set status string
     *
     * @return Newscoop\Entity\Comment
     */
    public function setStatus($status)
    {
        $status_enum = array_flip(self::$status_enum);
        $this->status = $status_enum[$status];

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
     * @return Newscoop\Entity\Comment
     */
    public function setForum(Publication $forum)
    {
        $this->forum = $forum;

        return $this;
    }

    /**
     * Get forum
     *
     * @return Newscoop\Entity\Publication
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * Set thread
     *
     * @return Newscoop\Entity\Comment
     */
    public function setThread($thread)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread
     *
     * @return int
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set thread level
     *
     * @return Newscoop\Entity\Comment
     */
    public function setThreadLevel($level)
    {
        $this->thread_level = $level;

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
     * @return Newscoop\Entity\Comment
     */
    public function setThreadOrder($order)
    {
        $this->thread_order = $order;

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
     * @return Newscoop\Entity\Comment
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get Language
     *
     * @return Newscoop\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set Parent
     *
     * @return Newscoop\Entity\Comment
     */
    public function setParent(Comment $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get Parent
     *
     * @return Newscoop\Entity\Comment
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get Parent Id
     *
     * @return Newscoop\Entity\Comment
     */
    public function getParentId()
    {
        if ($this->parent) {
            return $this->parent->getId();
        }

        return;
    }

    /**
     * Add one more like for comment
     *
     * @return self
     */
    public function addLike()
    {
        $this->likes = $this->likes+1;

        return $this;
    }

    /**
     * Set likes number for comment
     *
     * @param int $likesNumber
     *
     * @return self
     */
    public function setLikes($likesNumber)
    {
        $this->likes = $likesNumber;

        return $this;
    }

    /**
     * Get the likes count
     *
     * @return int
     */
    public function getLikes()
    {
        return $this->likes;
    }

    public function addDislike()
    {
        $this->dislikes = $this->dislikes+1;

        return $this;
    }

    public function setDislikes($dislikesNumber)
    {
        $this->dislikes = $dislikesNumber;

        return $this;
    }

    /**
     * Get the dislikes count
     *
     * @return int
     */
    public function getDislikes()
    {
        return $this->dislikes;
    }

    /**
     * Get username witch should be the real name
     *
     * @return string
     * @deprecated legacy from frontend controllers
     */
    public function getRealName()
    {
        $this->getCommenter()->getUserName();
    }

    /**
     * Check if the comment is the same as this one
     *
     * @param Comment $comment
     *
     * @return bool
     * @deprecated legacy from frontend controllers
     */
    public function SameAs($comment)
    {
        if (is_object($comment)) {
            return $comment->getId() == $this->getId();
        }

        return false;
    }

    /**
     * Check if the comment exists
     * Test if there is set an id
     *
     * @return bool
     * @deprecated legacy from frontend controllers
     */
    public function exists()
    {
        return !is_null($this->id);
    }

    /**
     * Get an enity property
     *
     * @param $key
     *
     * @return mixed
     * @deprecated legacy from frontend controllers
     */
    public function getProperty($key)
    {
        if (isset($this->$key)) {
            return $this->$key;
        } else {
            return;
        }
    }

    /**
     * Method used to format message from comments
     *
     * @param  string $str
     * @return string
     */
    protected function formatMessage($str)
    {
        $parts = explode('<', $str);
        // if no < was found then return the original string
        if (count($parts) === 1) {
            return $str;
        }
        /** @type array vector where the tag list are keeped */
        $tag = array();
        $attrib = array();
        $contentAfter = array(0 => $parts[0]);
        for ($i = 1, $counti = count($parts); $i < $counti; $i++) {
            $tagAndContent = explode('>', $parts[$i], 2);
            $tagAndAttrib = explode(' ', $tagAndContent[0], 2);

            if (isset($tagAndAttrib[1])) {
                /**
                 * @todo make a better attributes filter regex
                 *      this is breaking on not quotes define attibutes
                 *      ex: like checked=true for good parsing should be checked="true" or checked='true'
                 */
                preg_match_all("#([^=]+)=\s*(['\"])?([^\\2]*)\\2#iU", $tagAndAttrib[1], $rez);
                if (isset($rez[1])) {
                    for ($k = 0, $countk = count($rez[1]); $k < $countk; $k++) {
                        $attrib[$i][] = array(strtolower(trim($rez[1][$k])), $rez[3][$k]);
                    }
                }
            }
            $tag[$i] = $tagAndAttrib[0];
            $contentAfter[$i] = isset($tagAndContent[1]) ? $tagAndContent[1] : '';
        }
        $closed = $tag;
        $return = '';
        $allowedNameTags = array_keys($this->allowedTags);
        for ($i = 0, $counti = count($contentAfter); $i < $counti; $i++) {
            $isClosed = isset($tag[$i]) ? (substr($tag[$i], 0, 1) == '/') : false;
            if (isset($tag[$i])) {
                $tagName = $tag[$i];
                if (substr($tagName, -1, 1) == '/') {
                    $tagName = substr($tagName, 0, -1);
                }
            }
            if (isset($tag[$i]) && (in_array($tagName, $allowedNameTags))) {
                unset($closed[$i]);
                $good = array_search('/'.$tag[$i], $closed, true);
                if ($good) {
                    unset($closed[$good]);
                    $composeTag = '<'.$tag[$i].' ';
                    if (isset($attrib[$i])) {
                        for ($j = 0, $countj = count($attrib[$i]); $j < $countj; $j++) {
                            if ($attrib[$i][$j][0] == 'href') {
                            }
                            $attrib[$i][$j][1] = preg_replace('/(javascript[:]?)/i', '', $attrib[$i][$j][1]);
                            if (in_array($attrib[$i][$j][0], $this->allowedTags[$tag[$i]])) {
                                $composeTag .= $attrib[$i][$j][0].'="'.$attrib[$i][$j][1].'" ';
                            }
                        }
                    }
                    $return .= substr($composeTag, 0, -1).'>'.$contentAfter[$i];
                } else {
                    $composeTag = '<'.$tag[$i].' ';
                    $title = false;
                    $cite = false;
                    if (isset($attrib[$i])) {
                        for ($j = 0, $countj = count($attrib[$i]); $j < $countj; $j++) {
                            if (in_array($attrib[$i][$j][0], $this->allowedTags[$tag[$i]])) {
                                if ($attrib[$i][$j][0] == 'href') {
                                    $attrib[$i][$j][1] = preg_replace('/(javascript[:]?)/i', '', $attrib[$i][$j][1]);
                                }
                                if ($attrib[$i][$j][0] == 'title') {
                                    $title = $attrib[$i][$j][1];
                                } elseif ($attrib[$i][$j][0] == 'cite') {
                                    $cite = $attrib[$i][$j][1];
                                } else {
                                    $composeTag .= $attrib[$i][$j][0].'="'.$attrib[$i][$j][1].'" ';
                                }
                            }
                        }
                    }
                    // if title is set and is a broken tag use the title like inline text
                    if (in_array($tagName, $this->allowedEmpty)) {
                        $return .= substr($composeTag, 0, -1).'>'.$contentAfter[$i];
                    } elseif ($title !== false) {
                        $return .= substr($composeTag, 0,
                                          -1).'>'.$title.'</'.$tag[$i].'>'.$contentAfter[$i];
                    } // if cite is set and is a broken tag use the cite like inline text
                    elseif ($cite !== false) {
                        $return .= substr($composeTag, 0, -1).'>'.$cite.'</'.$tag[$i].'>'.$contentAfter[$i];
                    } // else use the text after
                    else {
                        $return .= substr($composeTag, 0, -1).'>'.$contentAfter[$i].'</'.$tag[$i].'>';
                    }
                }
            } elseif (isset($tag[$i]) && $isClosed && in_array(substr($tag[$i], 1), $allowedNameTags)) {
                unset($closed[$i]);
                $return .= '<'.$tag[$i].'>'.$contentAfter[$i];
            } else {
                $return .= $contentAfter[$i];
            }
        }

        return $return;
    }

    /**
     * Set indexed
     *
     * @param DateTime $indexed
     *
     * @return self
     */
    public function setIndexed(DateTime $indexed = null)
    {
        $this->indexed = $indexed;

        return self;
    }

    /**
     * Get indexed
     *
     * @return DateTime
     */
    public function getIndexed()
    {
        return $this->indexed;
    }

    /**
     * Get comment source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set comment source
     *
     * @param string $source
     *
     * @return string
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }
}
