<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Newscoop\Entity\User;
use Newscoop\NewscoopBundle\Entity\Topic;

/**
 * User Topic Repository
 */
class UserTopicRepository extends EntityRepository
{
    /**
     * Find topics for user
     *
     * @param Newscoop\Entity\User $user   User object or user id
     * @param string               $locale Topic locale
     *
     * @return array
     */
    public function findByUser($user, $locale = null)
    {
        $userId = is_int($user) ? $user : $user->getId();
        $em = $this->getEntityManager();

        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select(array('ut', 't'))
            ->from('Newscoop\Entity\UserTopic', 'ut')
            ->leftJoin('ut.user', 'u')
            ->leftJoin('ut.topic', 't')
            ->where('u.id = :user_id')
            ->setParameters(array(
                'user_id' => $userId
            ));

        $query = $em->getRepository("Newscoop\NewscoopBundle\Entity\Topic")->setTranslatableHint($qb->getQuery(), $locale);

        return $query->getResult();
    }

    /**
     * Find results for user and topic
     *
     * @param Newscoop\Entity\User                 $user
     * @param Newscoop\NewscoopBundle\Entity\Topic $topic
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
            ->leftJoin('t.translations', 'tt')
            ->where('u.id = :user_id')
            ->andWhere('t.id = :topic_id')
            ->andWhere('tt.locale = :topic_language_id')
            ->setParameters(array(
                'user_id' => $user->getId(),
                'topic_id' => $topic->getId(),
                'topic_language_id' => $topic->getTranslatableLocale(),
            ));

        return $qb->getQuery()->getResult();
    }
}
