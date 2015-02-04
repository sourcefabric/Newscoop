<?php
/**
 * @package Newscoop\ArticlesBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Editorial comments repository
 */
class EditorialCommentRepository extends EntityRepository
{
    /**
     * Get all editorial comments for article
     *
     * @param integer $articleNumber
     * @param boolean $fetchReplies
     *
     * @return Doctrine\ORM\Query
     */
    public function getAllByArticleNumber($articleNumber, $fetchReplies = true)
    {
        $qb = $this->createQueryBuilder('ec');
        $qb
            ->select('ec', 'u')
            ->join('ec.user', 'u')
            ->where('ec.articleNumber = :articleNumber')
            ->andWhere('ec.is_active = :is_active')
            ->andWhere('ec.resolved = false')
            ->setParameters(array(
                'articleNumber' => $articleNumber,
                'is_active' => true,
            ));

        if (!$fetchReplies) {
            $qb->andWhere($qb->expr()->isNull('ec.parentId'));
        }

        return $qb->getQuery();
    }

    /**
     * Get one editorial comment by article and comment id
     *
     * @param integer $articleNumber
     * @param integer $languageId
     * @param integer $commentId
     * @param boolean $fetchReplies
     *
     * @return Doctrine\ORM\Query
     */
    public function getOneByArticleAndCommentId($articleNumber, $languageId, $commentId, $fetchReplies = true)
    {
        $qb = $this->createQueryBuilder('ec');
        $qb
            ->select('ec', 'u')
            ->join('ec.user', 'u')
            ->where('ec.articleNumber = :articleNumber')
            ->andWhere('ec.languageId = :languageId')
            ->andWhere('ec.id = :commentId')
            ->andWhere('ec.is_active = :is_active')
            ->setParameters(array(
                'articleNumber' => $articleNumber,
                'languageId' => $languageId,
                'commentId' => $commentId,
                'is_active' => true,
            ));

        if (!$fetchReplies) {
            $qb->andWhere($qb->expr()->isNull('ec.parentId'));
        }

        return $qb->getQuery();
    }

    /**
     * Get all editorial comments
     *
     * @param boolean $fetchReplies
     *
     * @return Doctrine\ORM\Query
     */
    public function getAll($fetchReplies = true)
    {
        $qb = $this->createQueryBuilder('ec');
        $qb
            ->select('ec', 'u')
            ->join('ec.user', 'u')
            ->andWhere('ec.is_active = :is_active')
            ->setParameters(array(
                'is_active' => true,
            ));

        if (!$fetchReplies) {
            $qb->andWhere($qb->expr()->isNull('ec.parentId'));
        }

        return $qb->getQuery();
    }
}
