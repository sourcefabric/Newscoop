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
                var_dump(array_merge(array($commentId), $this->getAllReplies($directReplies)));die;
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
     * Checks filtered status
     *
     * @param string                    $filter       Selected comment filter
     * @param Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     *
     * @return void
     */
    public function checkFilter($filter, $queryBuilder)
    {
        if ($filter) {
            $queryBuilder->andWhere('c.status = ?1')
                ->setParameter(1, $filter);
        }
    }

    /**
     * Checks recommend/unrecommend status
     *
     * @param string                    $filter       Selected comment filter
     * @param Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     *
     * @return void
     */
    public function checkFilterRecommended($filter, $queryBuilder)
    {
        if ($filter) {
            $queryBuilder->andWhere('c.recommended = ?1')
                ->setParameter(1, $filter);
        }
    }
}
