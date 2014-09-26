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
    public function getAllByArticleNumber($articleNumber, $fetchReplies = true)
    {
        $qb = $this->createQueryBuilder('ec');

        $qb
            ->select('ec', 'u')
            ->join('ec.user', 'u')
            ->where('ec.articleNumber = :articleNumber')
            ->andWhere('ec.is_active = :is_active')
            ->setParameters(array(
                'articleNumber' => $articleNumber,
                'is_active' => true,
            ));

        if (!$fetchReplies) {
            $qb->andWhere($qb->expr()->isNull('ec.parentId'));
        }

        return $qb->getQuery()->getArrayResult();
    }
}
