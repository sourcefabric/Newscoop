<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Exception\ResourcesConflictException;
use Newscoop\Entity\Article;
use Newscoop\Entity\ArticleAuthor;
use Newscoop\Entity\Author;
use Newscoop\Entity\AuthorType;

/**
 * Author service
 */
class AuthorService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var Doctrine\ORM\EntityRepository */
    protected $repository;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository('Newscoop\Entity\Author');
    }

    /**
     * Get authors
     *
     * @param  string $term      Search term
     * @param  int    $limit     Max results
     * @param  bool   $alsoUsers Also return users
     *
     * @return array
     */
    public function getAuthors($term = null, $limit = null, $alsoUsers = false)
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select("trim(concat(aa.first_name, concat(' ', aa.last_name))) as name")
            ->from('Newscoop\Entity\Author', 'aa');

        if ($term !== null && trim($term) !== '') {
            $qb
            ->where($qb->expr()->like('aa.last_name', ':term'))
            ->orWhere($qb->expr()->like('aa.first_name', ':term'))
            ->orWhere($qb->expr()->like('concat(aa.first_name, concat(\' \', aa.last_name))', ':term'))
            ->setParameter('term', $term . '%')
            ->groupBy('aa.last_name', 'aa.first_name');
        }

        if (!is_null($limit)) {
            $qb->setMaxResults($limit);
        }

        $authorsArray = $qb->getQuery()->getArrayResult();

        if ($alsoUsers) {
            $qbUsers = clone $qb;
            $qbUsers->resetDQLPart('from');
            $qbUsers->from('Newscoop\Entity\User', 'aa');
            $usersArray = $qbUsers->getQuery()->getArrayResult();
            $authorsArray = array_unique(array_merge($authorsArray, $usersArray), SORT_REGULAR);
        }

        return $authorsArray;
    }

    /**
     * Get author options
     *
     * @return array
     */
    public function getOptions()
    {
        $authors = array();
        foreach ($this->repository->findBy(array(), array('last_name' => 'asc', 'first_name' => 'asc')) as $author) {
            $authors[$author->getId()] = $author->getFullName();
        }

        return $authors;
    }

    /**
     * Add author with type to article
     *
     * @param Article       $article
     * @param Author        $author
     * @param AuthorType $authorType
     *
     * @return ArticleAuthor
     */
    public function addAuthorToArticle(Article $article, Author $author, AuthorType $authorType)
    {
        $articleAuthor = $this->em->getRepository('Newscoop\Entity\ArticleAuthor')
            ->getArticleAuthor($article->getNumber(), $article->getLanguageCode(), $author->getId(), $authorType->getId())
            ->getOneOrNullResult();

        if (!is_null($articleAuthor)) {
            throw new ResourcesConflictException("Author with this type is already attached to article", 409);
        }

        $articleAuthors = $this->em->getRepository('Newscoop\Entity\ArticleAuthor')
            ->getArticleAuthors($article->getNumber(), $article->getLanguageCode())
            ->getArrayResult();

        $articleAuthor = new ArticleAuthor();
        $articleAuthor->setArticle($article);
        $articleAuthor->setAuthor($author);
        $articleAuthor->setType($authorType);
        $articleAuthor->setOrder(count($articleAuthors)+1);

        $this->em->persist($articleAuthor);
        $this->em->flush();

        return $articleAuthor;
    }


    /**
     * Remove author with type from article
     *
     * @param Article       $article
     * @param Author        $author
     * @param AuthorType $authorType
     *
     * @return ArticleAuthor
     */
    public function removeAuthorFromArticle(Article $article, Author $author, AuthorType $authorType)
    {
        $articleAuthor = $this->em->getRepository('Newscoop\Entity\ArticleAuthor')
            ->getArticleAuthor($article->getNumber(), $article->getLanguageCode(), $author->getId(), $authorType->getId())
            ->getOneOrNullResult();

        if (!$articleAuthor) {
            throw new ResourcesConflictException("Author with this type is not attached to article", 409);
        }

        $this->em->remove($articleAuthor);
        $this->em->flush();

        $articleAuthors = $this->em->getRepository('Newscoop\Entity\ArticleAuthor')
            ->getArticleAuthors($article->getNumber(), $article->getLanguageCode())
            ->getResult();

        $this->reorderAuthors($this->em, $articleAuthors);
    }

    /**
     * Reorder Article Authors
     *
     * @param Doctrine\ORM\EntityManager $em
     * @param array                      $articleAuthors
     * @param array                      $order
     *
     * @return boolean
     */
    public function reorderAuthors($em, $articleAuthors, $order = array())
    {
        // clear current order
        foreach ($articleAuthors as $articleAuthor) {
            $articleAuthor->setOrder(null);
        }
        $em->flush();

        if (count($order) > 1) {
            $counter = 0;
            foreach ($order as $item) {
                list($authorId, $authorTypeId) = explode("-", $item);

                foreach ($articleAuthors as $articleAuthor) {
                    if ($articleAuthor->getAuthor()->getId() == $authorId && $articleAuthor->getType()->getId() == $authorTypeId) {
                        $articleAuthor->setOrder($counter+1);
                        $counter++;
                    }
                }
            }
        } else {
            $counter = 1;
            foreach ($articleAuthors as $articleAuthor) {
                $articleAuthor->setOrder($counter);
                $counter++;
            }
        }

        $em->flush();

        return true;
    }
}
