<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ArticleTopicRepository extends EntityRepository
{
    public function getArticleTopicsQuery($articleNumber)
    {
        $queryBuilder = $this->createQueryBuilder('at')
            ->where('at.article = :articleNumber')
            ->setParameter('articleNumber', $articleNumber);

        $query = $queryBuilder->getQuery();

        return $query;
    }
}
