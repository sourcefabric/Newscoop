<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\UserTopicRepository")
 * @ORM\Table(name="user_topic")
 */
class UserTopic
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="topic_id")
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="Id")
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\NewscoopBundle\Entity\Topic")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="topic_id", referencedColumnName="id")
     *  })
     * @var \Newscoop\NewscoopBundle\Entity\Topic
     */
    protected $topic;

    /**
     * @param User                                  $user
     * @param \Newscoop\NewscoopBundle\Entity\Topic $topic
     */
    public function __construct(User $user, \Newscoop\NewscoopBundle\Entity\Topic $topic)
    {
        $this->user = $user;
        $this->topic = $topic;
    }

    /**
     * Get topic
     *
     * @return \Newscoop\NewscoopBundle\Entity\Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Get topic id
     *
     * @return int
     */
    public function getTopicId()
    {
        return $this->topic->getId();
    }
}
