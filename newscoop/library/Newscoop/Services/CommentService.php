<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\Comment;

/**
 * User service
 */
class CommentService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

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
     * @param sfEvent $event
     * @return void
     */
    public function update(\sfEvent $event)
    {
        $params = $event->getParameters();
        list($resource, $action) = explode('.', $event->getName());

        $method = $resource."_".$action;
        $this->$method($params);

        $this->em->flush();
    }

    /**
     * Get total count for given criteria
     *
     * @param array $criteria
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
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->em->getRepository('Newscoop\Entity\Comment')
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findUserComments($params, $order, $p_limit, $p_start)
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('c');
        $qb->from('Newscoop\Entity\Comment', 'c');

        $conditions = $qb->expr()->andx();
        $conditions->add($qb->expr()->in("c.commenter", $params["commenters"]));

        $qb->where($conditions);

        foreach ($order as $column => $direction) {
            $qb->addOrderBy("c.$column", $direction);
        }

        $qb->setFirstResult($p_start);
        $qb->setMaxResults($p_limit);

        //echo $qb->getQuery()->getSql();

        return $qb->getQuery()->getResult();
    }

    /**
     * Get repository for comment entity
     *
     * @return Newscoop\Entity\Repository\CommentRepository
     */
    private function getRepository()
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

        $query =  $this->em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('a.comments_enabled')
            ->where('a.number = :number')
            ->setMaxResults(1)
            ->getQuery();

        foreach ($ids as $id) {
            $comments_enabled = $query->setParameter('number',$id)
                ->getResult();

            $stats[$id] = array(
                'comments_enabled' => (bool) $comments_enabled,
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

        return $stats;
    }

    /**
     * Get articles comment counts
     *
     * @param array $ids
     * @return array
     */
    private function getCommentCounts(array $ids, $recommended = false)
    {
        $qb = $this->getCommentsRepository()
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

    /**
     * Get comments repository
     *
     * @return Doctrine\ORM\EntityRepository
     */
    private function getCommentsRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\Comment');
    }
}
