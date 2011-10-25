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
     * @ManyToOne(targetEntity="Newscoop\Entity\User")
     * @JoinColumn(referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @Id
     * @Column(type="integer", name="topic_id")
     * @var int
     */
    private $topic_id;

    /**
     * @Id
     * @Column(type="integer", name="topic_language")
     * @var int
     */
    private $topic_language;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Topic")
     * @JoinColumns({
     *      @JoinColumn(name="topic_id", referencedColumnName="fk_topic_id"),
     *      @JoinColumn(name="topic_language", referencedColumnName="fk_language_id")
     *  })
     * @var Newscoop\Entity\Topic
     */
    private $topic;

    /**
     * @param Newscoop\Entity\User $user
     * @param Newscoop\Entity\Topic $topic
     */
    public function __construct(User $user, Topic $topic)
    {
        $this->user = $user;
        $this->topic = $topic;
        $this->topic_id = $topic->getTopicId();
        $this->topic_language = $topic->getLanguageId();
    }

    /**
     * Get topic
     *
     * @return Newscoop\Entity\Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }
}
