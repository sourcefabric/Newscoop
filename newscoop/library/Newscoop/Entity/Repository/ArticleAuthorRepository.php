<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ArticleAuthor repository
 */
class ArticleAuthorRepository extends EntityRepository
{
    public function getArticleAuthor($articleNumber, $languageCode, $authorId)
    {
        $qb = $this->createQueryBuilder('au')
            ->where('au.articleNumber = :articleNumber')
            ->andWhere('au.languageId = :languageId')
            ->andWhere('au.author = :author')
            ->setParameters(array(
                'articleNumber' => $articleNumber,
                'languageId' => 1,
                'author' => $authorId
            ));

        return $qb->getQuery();
    }
}
