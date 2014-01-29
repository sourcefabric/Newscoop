<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\EventDispatcher\Events\GenericEvent;
use Newscoop\Entity\Comment;

/**
 * Comment service
 */
class CommentService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    private function comment_create($params)
    {
        $comment = $this->find($params['id']);

        $commenter = $comment->getCommenter();
        $user = $commenter->getUser();

        if (!isset($user)) {
            return;
        }

        $attribute_value = $user->getAttribute("comment_delivered");
        $attribute_value = isset($attribute_value) ? ($attribute_value + 1) : 1;

        $user->addAttribute("comment_delivered", $attribute_value);

        $points_action = $this->em->getRepository('Newscoop\Entity\UserPoints')
                    ->getPointValueForAction("comment_delivered");

        $points = $user->getPoints();

        $user->setPoints($points+$points_action);
    }

    private function comment_recommended($params)
    {
        $comment = $this->find($params['id']);

        $commenter = $comment->getCommenter();
        $user = $commenter->getUser();

        if (!isset($user)) {
            return;
        }

        $attribute_value = $user->getAttribute("comment_recommended");
        $attribute_value = isset($attribute_value) ? ($attribute_value + 1) : 1;

        $user->addAttribute("comment_recommended", $attribute_value);

        $points_action = $this->em->getRepository('Newscoop\Entity\UserPoints')
                    ->getPointValueForAction("comment_recommended");

        $points = $user->getPoints();

        $user->setPoints($points+$points_action);
    }

    private function comment_update($params)
    {
        $comment = $this->find($params['id']);
    }

    private function comment_delete($params)
    {
        $comment = $this->find($params['id']);

        $commenter = $comment->getCommenter();
        $user = $commenter->getUser();

        if (!isset($user)) {
            return;
        }

        $attribute_value = $user->getAttribute("comment_deleted");
        $attribute_value = isset($attribute_value) ? ($attribute_value + 1) : 1;

        $user->addAttribute("comment_deleted", $attribute_value);

        //have to remove points for a deleted comment.
        $points_action = $this->em->getRepository('Newscoop\Entity\UserPoints')
                    ->getPointValueForAction("comment_delivered");

        $points = $user->getPoints();

        $user->setPoints($points-$points_action);
    }

    /**
     * Receives notifications of points events.
     *
     * @param GenericEvent $event
     *
     * @return void
     */
    public function update(GenericEvent $event)
    {
        $params = $event->getArguments();
        $method = str_replace('.', '_', $event->getName());
        $this->$method($params);

        $this->em->flush();
    }

    /**
     * Get total count for given criteria
     *
     * @param array $criteria
     *
     * @return int
     */
    public function countBy(array $criteria)
    {
        return count($this->findBy($criteria));
    }

    /**
     * Find a comment by its id.
     *
     * @param int $id
     *
     * @return Newscoop\Entity\Comment
     *
     */
    public function find($id)
    {
        return $this->em->getRepository('Newscoop\Entity\Comment')
            ->find($id);
    }

    /**
     * Find records by set of criteria
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->em->getRepository('Newscoop\Entity\Comment')
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Gets all replies to a comment.
     *
     * @param array $params Parameters
     * @param array $order  Order
     * @param int   $limit  Result limit
     * @param int   $start  Result start
     *
     * @return array
     */
    public function findUserComments($params, $order, $limit, $start)
    {
        $qb = $this->em->getRepository('Newscoop\Entity\Comment')
            ->createQueryBuilder('c');

        $conditions = $qb->expr()->andx();
        $conditions->add($qb->expr()->in("c.commenter", $params["commenters"]));

        $qb->where($conditions);

        foreach ($order as $column => $direction) {
            $qb->addOrderBy("c.$column", $direction);
        }

        $qb->setFirstResult($start);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
    * Gets all replies to a comment.
    *
    * @param int|array                             $commentId         Comment id
    * @param Newscoop\Repository\CommentRepository $commentRepository Comment repository
    *
    * @return array
    */
    public function getAllReplies($commentId, $commentRepository)
    {
        if (!is_array($commentId)) {
            $directReplies = $commentRepository->getDirectReplies($commentId);
            if (count($directReplies)) {
                return array_merge(array($commentId), $this->getAllReplies($directReplies, $commentRepository));
            } else {
                return array($commentId);
            }
        } else {
            if (count($commentId) > 1) {
                return array_merge(
                    $this->getAllReplies(array_pop($commentId), $commentRepository),
                    $this->getAllReplies($commentId, $commentRepository)
                );
            } else {
                return $this->getAllReplies(array_pop($commentId), $commentRepository);
            }
        }
    }

    /**
     * Checks if a commenter is banned
     *
     * @param Newscoop\Entity\Comment\Commenter $commenter Commenter
     *
     * @return bool
     */
    public function isBanned($commenter)
    {
        $queryBuilder = $this->em->getRepository('Newscoop\Entity\Comment\Acceptance')
            ->createQueryBuilder('a');

        $queryBuilder->where($queryBuilder->expr()->orX(
            $queryBuilder->expr()->eq('a.search', ':name'),
            $queryBuilder->expr()->eq('a.search', ':email'),
            $queryBuilder->expr()->eq('a.search', ':ip')
        ));

        $queryBuilder->setParameters(array(
            'name' => $commenter->getName(),
            'email' => $commenter->getEmail(),
            'ip' => $commenter->getIp()
        ));

        $result = $queryBuilder->getQuery()->getResult();

        if ($result) {
            return true;
        }

        return false;
    }

    /**
     * Searchs comments by given phrase
     *
     * @param string $phrase Phrase
     *
     * @return Doctrine\ORM\QueryBuilder
     */
    public function searchByPhrase($phrase)
    {
        $queryBuilder = $this->em->getRepository('Newscoop\Entity\Comment')
            ->createQueryBuilder('c');

        $queryBuilder
            ->select('c', 'cm.name', 't.name')
            ->leftJoin('c.commenter', 'cm')
            ->leftJoin('c.thread', 't')
            ->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('c.message', $queryBuilder->expr()->literal('%'.$phrase.'%')),
                $queryBuilder->expr()->like('c.subject', $queryBuilder->expr()->literal('%'.$phrase.'%')),
                $queryBuilder->expr()->like('cm.name', $queryBuilder->expr()->literal('%'.$phrase.'%')),
                $queryBuilder->expr()->like('cm.email', $queryBuilder->expr()->literal('%'.$phrase.'%')),
                $queryBuilder->expr()->like('t.name', $queryBuilder->expr()->literal('%'.$phrase.'%'))
            ))
            ->andWhere('c.status != 3')
            ->orderBy('c.time_created', 'desc');

        return $queryBuilder;
    }

    /**
     * Get repository for comment entity
     *
     * @return Newscoop\Entity\Repository\CommentRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\Comment');
    }

    /**
     * Get comments statistics for articles
     *
     * @param mixed $ids
     * @return array
     */
    public function getArticleStats($ids)
    {
        $ids = (array) $ids;
        if (empty($ids)) {
            return array();
        }

        $stats = array();
        foreach ($ids as $id) {
            $stats[$id] = array(
                'normal' => 0,
                'recommended' => 0,
            );
        }

        foreach (array('normal' => false, 'recommended' => true) as $key => $recommended) {
            $rows = $this->getCommentCounts($ids, $recommended);
            foreach ($rows as $row) {
                $stats[(int) $row['article_num']][$key] = (int) $row[1];
            }
        }

        $ce_rows =  $this->getCommentsEnabled($ids);
        foreach ($ce_rows as $row) {
            $stats[(int) $row['number']]['comments_enabled'] = (bool) $row['comments_enabled'];
        }

        return $stats;
    }

    /**
     * Get article comments_enabled
     *
     * @param array $ids
     */
    private function getCommentsEnabled(array $ids)
    {
        return $this->em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('a.comments_enabled, a.number')
            ->where('a.number IN (:ids)')
            ->setParameter('ids', array_values($ids))
            ->getQuery()
            ->getResult();
    }

    /**
     * Get articles comment counts
     *
     * @param array $ids
     * @return array
     */
    private function getCommentCounts(array $ids, $recommended = false)
    {
        $qb = $this->getRepository()
            ->createQueryBuilder('c')
            ->select('COUNT(c), c.article_num')
            ->andWhere('c.article_num IN (:ids)')
            ->andWhere('c.status = :status');

        if ($recommended) {
            $qb->andWhere('c.recommended = 1');
        } else {
            $qb->andWhere('c.recommended <> 1');
        }

        return $qb->groupBy('c.article_num')
            ->setParameter('ids', array_values($ids))
            ->setParameter('status', Comment::STATUS_APPROVED)
            ->getQuery()
            ->getResult();
    }
}
