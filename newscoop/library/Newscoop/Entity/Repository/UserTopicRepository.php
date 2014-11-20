<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Newscoop\Entity\User;
use Newscoop\Entity\Topic;

/**
 * User Topic Repository
 */
class UserTopicRepository extends EntityRepository
{
    /**
     * Find topics for user
     *
     * @param Newscoop\Entity\User
     * @return array
     */
    public function findByUser($user)
    {
        $userId = is_int($user) ? $user : $user->getId();
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT ut FROM Newscoop\Entity\UserTopic ut INNER JOIN ut.topic t WHERE ut.user = :user');
        $query->setParameter('user', $userId);
        return $query->getResult();
    }

    /**
     * Find results for user and topic
     *
     * @param  Newscoop\Entity\User $user
     * @param  Topic $topic
     *
     * @return Newscoop\Entity\UserTopic
     */
    public function findByTopicAndUser(User $user, Topic $topic)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select(array('ut'))
            ->from('Newscoop\Entity\UserTopic', 'ut')
            ->leftJoin('ut.user', 'u')
            ->leftJoin('ut.topic', 't')
            ->where('u.id = :user_id')
            ->andWhere('t.id = :topic_id')
            ->andWhere('t.language = :topic_language_id')
            ->setParameters(array(
                'user_id' => $user->getId(),
                'topic_id' => $topic->getTopicId(),
                'topic_language_id' => $topic->getLanguageId(),
            ));

        return $qb->getQuery()->getResult();;
    }
}
