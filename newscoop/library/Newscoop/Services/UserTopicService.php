<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\EventDispatcher\EventDispatcher;
use Newscoop\Entity\Topic;
use Newscoop\Entity\User;
use Newscoop\Entity\UserTopic;
use Newscoop\Topic\SaveUserTopicsCommand;

/**
 * User service
 */
class UserTopicService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /** @var Newscoop\EventDispatcher\EventDispatcher */
    private $dispatcher;

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

        $this->dispatcher->dispatch('topic.follow', new \Newscoop\EventDispatcher\Events\GenericEvent($this, array(
            'topic_name' => $topic->getName(),
            'topic_id' => $topic->getTopic()->getId(),
            'user' => $user,
        )));
    }
}
