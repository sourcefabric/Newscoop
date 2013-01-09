<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 * @Entity(repositoryClass="Newscoop\Entity\Repository\UserTopicRepository")
 * @Table(name="user_topic")
 */
class UserTopic
{
    /**
     * @Id
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(referencedColumnName="Id")
     * @var User
     */
    private $user;

    /**
     * @Id
     * @ManyToOne(targetEntity="Topic")
     * @JoinColumn(name="topic_id", referencedColumnName="fk_topic_id")
     * @var Topic
     */
    private $topic;

    /**
     * @param User $user
     * @param Topic $topic
     */
    public function __construct(User $user, Topic $topic)
    {
        $this->user = $user;
        $this->topic = $topic;
    }

    /**
     * Get topic
     *
     * @return Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }
}
