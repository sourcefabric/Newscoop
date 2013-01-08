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
     * @param mixed $user
     * @return array
     */
    public function getTopics($user)
    {
        $userId = $user instanceof User ? $user->getId() : $user->identifier;
        $userTopics = $this->em->getRepository('Newscoop\Entity\UserTopic')
            ->findByUser($userId);

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

        $germanTopic = null;
        foreach ($topics as $topic) {
            if (5 == $topic->getLanguageId()) { // first go for german
                $germanTopic = $topic;
                break;
            }
        }

        return empty($germanTopic) ? $topics[0] : $germanTopic;
    }

    /**
     * Update user topics
     *
     * @param Newscoop\Entity\User $user
     * @param array $topics
     * @return void
     */
    public function updateTopics(User $user, array $topics)
    {
        $repository = $this->em->getRepository('Newscoop\Entity\UserTopic');
        foreach ($topics as $topicId => $status) {
            $matches = $repository->findBy(array(
                'user' => $user->getId(),
                'topic_id' => $topicId,
            ));

            if ($status === 'false' && !empty($matches)) {
                foreach ($matches as $match) {
                    $this->em->remove($match);
                }
            } else if ($status === 'true' && empty($matches)) {
                $topic = $this->findTopic($topicId);
                if ($topic) {
                    $this->em->persist(new UserTopic($user, $this->findTopic($topicId)));
                }
            }
        }

        $this->em->flush();
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
