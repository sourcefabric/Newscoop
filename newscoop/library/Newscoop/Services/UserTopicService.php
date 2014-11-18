<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\EventDispatcher\EventDispatcher;
use Newscoop\Entity\Topic;
use Newscoop\Entity\User;
use Newscoop\Entity\UserTopic;
use Newscoop\Topic\SaveUserTopicsCommand;
use Exception;

/**
 * User service
 */
class UserTopicService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var Newscoop\EventDispatcher\EventDispatcher */
    protected $dispatcher;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Newscoop\EventDispatcher\EventDispatcher $dispatcher
     */
    public function __construct(EntityManager $em, $dispatcher = null)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Follow topic by user
     *
     * @param Newscoop\Entity\User $user
     * @param Topic $topic
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
     * Unfollow topic
     *
     * @param Newscoop\Entity\User $user
     * @param Topic $topic
     * @return void
     */
    public function unfollowTopic(User $user, Topic $topic)
    {
        try {
            $userTopics = $this->em->getRepository('Newscoop\Entity\UserTopic')
                ->findByTopicAndUser($user, $topic);

            if ($userTopics) {
                if (is_array($userTopics)) {
                    foreach ($userTopics AS $userTopic) {
                        $this->em->remove($userTopic);
                    }
                } else {
                    $this->em->remove($userTopics);
                }
                $this->em->flush();
            }
        } catch (Exception $e) {
            throw new Exception('Could not unfollow topic. ('.$e->getMessage().')');
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
     * @return Topic
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
     * Save user topics command
     *
     * @param SaveUserTopicsCommand $command
     * @return void
     */
    public function saveUserTopics(SaveUserTopicsCommand $command)
    {
        if (empty($command->topics)) {
            $query = $this->em->createQuery('DELETE Newscoop\Entity\UserTopic ut WHERE ut.user = :user');
            $query->execute(array('user' => $command->userId));
        } else {
            $topics = $this->em->getRepository('Newscoop\Entity\UserTopic')->findByUser($command->userId);
            foreach ($topics as $topic) {
                if (in_array($topic->getTopicId(), $command->topics)) {
                    $this->em->remove($topic);
                }
            }

            $this->em->flush();
        }

        $user = $this->em->getReference('Newscoop\Entity\User', $command->userId);
        foreach ($command->selected as $topicId) {
            $topic = $this->em->getReference('Newscoop\Entity\Topic', array(
                'id' => $topicId,
                'language' => (int) $command->languageId,
            ));
            $this->em->persist(new UserTopic($user, $topic));
        }

        $this->em->flush();
    }

    /**
     * Update user topics
     *
     * @param Newscoop\Entity\User $user
     * @param array                $topics
     *
     * @return void
     */
    public function updateTopics(User $user, array $topics)
    {
        $repository = $this->em->getRepository('Newscoop\Entity\UserTopic');
        $userTopics = $repository->findByUser($user);
        $matches = array();
        foreach($userTopics as $topic){
            if(array_key_exists($topic->getTopicId(), $topics)) {
                $matches[$topic->getTopicId()] = $topic;
            }
        }

        foreach ($topics as $topicId => $status) {
            if ($status === 'false' && array_key_exists($topicId, $matches)) {
                foreach ($matches as $match) {
                    if ($match->getTopicId() == $topicId) {
                        $this->em->remove($match);
                    }
                }
            } else if ($status === 'true' && !array_key_exists($topicId, $matches)) {
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
     * @param Topic $topic
     */
    private function notify(User $user, Topic $topic)
    {
        if (empty($this->dispatcher)) {
            return;
        }

        $this->dispatcher->dispatch('topic.follow', new \Newscoop\EventDispatcher\Events\GenericEvent($this, array(
            'topic_name' => $topic->getName(),
            'topic_id' => $topic->getTopicId(),
            'user' => $user,
        )));
    }
}
