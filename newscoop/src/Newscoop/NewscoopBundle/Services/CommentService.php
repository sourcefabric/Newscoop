<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Services;

use Doctrine\ORM\EntityManager;

/**
 * Comments service
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

    /**
    * Gets all replies to a comment.
    *
    * @param int|array $commentId Comment id
    *
    * @return array
    */
    public function getAllReplies($commentId)
    {
        if (!is_array($commentId)) {
            $directReplies = $this->em->getRepository('Newscoop\Entity\Comment')->getDirectReplies($commentId);
            if (count($directReplies)) {
                return array_merge(array($commentId), $this->getAllReplies($directReplies));
            } else {
                return array($commentId);
            }
        } else {
            if (count($commentId) > 1) {
                return array_merge(
                    $this->getAllReplies(array_pop($commentId)),
                    $this->getAllReplies($commentId)
                );
            } else {
                return $this->getAllReplies(array_pop($commentId));
            }
        }
    }

    /**
     * Checks if a commenter is banned
     *
     * @param Newcoop\Entity\Commenter $commenter Commenter
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

        $query = $queryBuilder->getQuery()->getResult();

        if ($query) {
            return true;
        }

        return false;
    }

    /**
     * Creates query for given form filters
     *
     * @param array                                         $filters          Filters
     * @param Doctrine\ORM\Query\Expr                       $query            Query operator
     * @param Symfony\Component\HttpFoundation\ParameterBag $sessionParameter Query operator
     * @param Doctrine\ORM\QueryBuilder                     $queryBuilder     Query builder
     *
     * @return Doctrine\ORM\Query\Expr
     */
    public function buildFilterQuery($filters, $query, $sessionParameter, $queryBuilder)
    {
        $statusMap = array(
            'approved' => 0,
            'new' => 1,
            'hidden' => 2,
        );

        foreach ($filters as $key => $value) {
            if ($value) {
                $query->add($queryBuilder->expr()->eq('c.status', $statusMap[$key]));
                $sessionParameter->set('filter'.ucfirst($key), $statusMap[$key]);
                if ($key == 'approved') {
                    $sessionParameter->set('filter'.ucfirst($key), true);
                }
            }
        }

        return $query;
    }

    /**
     * Creates query for given filters in stored in session
     *
     * @param array                     $sessionData  Filters
     * @param Doctrine\ORM\Query\Expr   $query        Query operator
     * @param Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     *
     * @return Doctrine\ORM\Query\Expr
     */
    public function buildSessionFilters($sessionData, $query, $queryBuilder)
    {
        foreach ($sessionData as $key => $value) {
            if ($key) {
                if ($key == 'filterApproved') {
                    $query->add($queryBuilder->expr()->eq('c.status', 0));
                } else {
                    $query->add($queryBuilder->expr()->eq('c.status', $value));
                }
            }
        }

        return $query;
    }

    /**
     * Creates comments array for paginator
     *
     * @param Knp\Bundle\PaginatorBundle $pagination Pagination
     *
     * @return array
     */
    public function createCommentsArray($pagination)
    {
        $counter = 1;
        $commentsArray = array();
        foreach ($pagination as $comment) {
            $commentsArray[] = array(
                'banned' => $this->isBanned($comment[0]->getCommenter()),
                'avatarHash' => md5($comment[0]->getCommenter()->getEmail()),
                'issueNumber' => $comment[0]->getThread()->getSection()->getIssue()->getNumber(),
                'comment' => $comment[0],
                'index' => $counter,
            );

            $counter++;
        }

        return $commentsArray;
    }
}
