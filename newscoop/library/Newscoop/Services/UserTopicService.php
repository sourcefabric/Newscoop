<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\User,
    Newscoop\Entity\Topic,
    Newscoop\Entity\UserTopic;

/**
 * User service
 */
class UserTopicService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /** @var Newscoop\Services\EventDispatcherService */
    private $dispatcher;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Newscoop\Services\EventDispatcherService $dispatcher
     */
    public function __construct(EntityManager $em, EventDispatcherService $dispatcher = null)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Follow topic by user
     *
     * @param Newscoop\Entity\User $user
     * @param Newscoop\Entity\Topic $topic
     * @return void
     */
    public function followTopic(User $user, Topic $topic)
    {
        try {
            $this->em->persist(new UserTopic($user, $topic));
            $this->em->flush();
            $this->notify($user, $topic);
        } catch (Exception $e) { // ignore if exists
        }
    }

    /**
     * Get user topics
     *
     * @param Newscoop\Entity\User $user
     * @return array
     */
    public function getTopics(User $user)
    {
        $userTopics = $this->em->getRepository('Newscoop\Entity\UserTopic')
            ->findBy(array(
                'user' => $user->getId(),
            ));

        if (empty($userTopics)) {
            return array();
        }

        $topics = array();
        foreach ($userTopics as $userTopic) {
            $topics[] = $userTopic->getTopic();
        }

        return $topics;
    }

    /**
     * Find topic
     *
     * @param int $id
     * @return Newscoop\Entity\Topic
     */
    public function findTopic($id)
    {
        $topics = $this->em->getRepository('Newscoop\Entity\Topic')
            ->findBy(array(
                'id' => $id,
            ));

        if (empty($topics)) {
            return null;
        }

        // @todo select by language
        return array_shift($topics);
    }

    /**
     * Dispatch event
     *
     * @param Newscoop\Entity\User $user
     * @param Newscoop\Entity\Topic $topic
     */
    private function notify(User $user, Topic $topic)
    {
        if (empty($this->dispatcher)) {
            return;
        }

        $this->dispatcher->notify(new \sfEvent($this, 'topic.follow', array(
            'topic_name' => $topic->getName(),
            'topic_id' => $topic->getTopicId(),
            'user' => $user,
        )));
    }
}
