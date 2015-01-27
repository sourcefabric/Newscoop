<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * RelatedArticles repository
 */
class RelatedArticlesRepository extends EntityRepository
{
    public function getRelatedArticles($articleNumber)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.articleNumber = :articleNumber')
            ->setParameter('articleNumber', $articleNumber);

        $query = $qb->getQuery();

        return $query;
    }
}
