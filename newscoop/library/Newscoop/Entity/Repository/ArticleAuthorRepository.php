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
 * ArticleAuthor repository
 */
class ArticleAuthorRepository extends EntityRepository
{
    public function getArticleAuthor($articleNumber, $languageCode, $authorId, $typeId = null)
    {
        $languageId = $this->_em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($languageCode);

        $qb = $this->createQueryBuilder('au')
            ->where('au.articleNumber = :articleNumber')
            ->andWhere('au.languageId = :languageId')
            ->andWhere('au.author = :author')
            ->setParameters(array(
                'articleNumber' => $articleNumber,
                'languageId' => $languageId,
                'author' => $authorId
            ));

        if ($typeId) {
            $qb->andWhere('au.type = :type')
                ->setParameter('type', $typeId);
        }

        return $qb->getQuery();
    }

    public function getArticleAuthors($articleNumber, $languageCode)
    {
        $languageId = $this->_em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($languageCode);

        $qb = $this->createQueryBuilder('au')
            ->where('au.articleNumber = :articleNumber')
            ->andWhere('au.languageId = :languageId')
            ->setParameters(array(
                'articleNumber' => $articleNumber,
                'languageId' => $languageId
            ))
            ->orderBy('au.order', 'asc');

        return $qb->getQuery();
    }
}
