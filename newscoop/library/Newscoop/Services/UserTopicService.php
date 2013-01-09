<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\User;
use Newscoop\Entity\Topic;
use Newscoop\Entity\UserTopic;
use Newscoop\Topic\SaveUserTopicsCommand;

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
        $userId = is_int($user) ? $user : $user->getId();
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
                'topic' => $topicId,
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
     * Save user topics command
     *
     * @param Newscoop\Topic\SaveUserTopicsCommand $command
     * @return void
     */
    public function saveUserTopics(SaveUserTopicsCommand $command)
    {
        if (empty($command->topics)) {
            $query = $this->em->createQuery('DELETE Newscoop\Entity\UserTopic ut WHERE ut.user = :user');
            $query->execute(array('user' => $command->userId));
        } else {
            $query = $this->em->createQuery('DELETE Newscoop\Entity\UserTopic ut WHERE ut.user = :user AND ut.topic IN (:topics)');
            $query->execute(array('user' => $command->userId, 'topics' => $command->topics));
        }

        $user = $this->em->getReference('Newscoop\Entity\User', $command->userId);
        foreach ($command->selected as $topicId) {
            $topic = $this->em->getReference('Newscoop\Entity\Topic', array(
                'id' => $topicId,
                'language' => $command->languageId,
            ));
            $this->em->persist(new UserTopic($user, $topic));
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
            'topic_id' => $topic->getTopic()->getId(),
            'user' => $user,
        )));
    }
}
