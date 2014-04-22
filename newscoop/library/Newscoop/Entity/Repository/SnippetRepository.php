<?php
/**
 * @package Newscoop
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Newscoop\Entity\Snippet;

/**
 * Snippet repository
 */
class SnippetRepository extends EntityRepository
{
    /**
     * Convenience QueryBuilder for Snippets
     *
     * This internal function is used by almost all Repository functions,
     * it allows for more consistency. The rest of the doc also applies
     * to the functions using it.
     *
     * Returns all Snippets. If the SnippetTemplate
     * is disabled, the Snippets depending on it won't be returned.
     * By Default all Snippets that are Disabled themselves are not returned.
     *
     * @param string $show     Define which Snippets to return, 'enabled' | 'disabled' | 'all'
     *
     * @return Doctrine\ORM\Querybuilder $queryBuilder
     */
    protected function getSnippetQueryBuilder($show)
    {
        $queryBuilder = $this->createQueryBuilder('snippet')
            ->join('snippet.template', 'template')
            ->andWhere('template.enabled = 1');     // Template should always be enabled

        if ($show == 'enabled') {
            $queryBuilder
                ->where('snippet.enabled = 1');
        }

        if ($show == 'disabled') {
            $queryBuilder
                ->where('snippet.enabled = 0');
        }

        return $queryBuilder;
    }

    /**
     * Convenience QueryBuilder for Snippets attached to an Article
     *
     * This internal function is used by almost all Repository functions,
     * it allows for more consistency. The rest of the doc also applies
     * to the functions using it.
     *
     * Returns all the associated Snippets to an Article. If the SnippetTemplate
     * is disabled, the Snippets depending on it won't be returned.
     * By Default all Snippets that are Disabled themselves are not returned.
     *
     * @param int    $article  Article number
     * @param string $language Language code in format "en" for example.
     * @param string $show     Define which Snippets to return, 'enabled' | 'disabled' | 'all'
     *
     * @return Doctrine\ORM\Querybuilder $queryBuilder
     */
    protected function getArticleSnippetQueryBuilder($articleNr, $language, $show)
    {
        $em = $this->getEntityManager();
        $languageId = $em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($language)->getId();

        $queryBuilder = $this->getSnippetQueryBuilder($show)
            ->join('snippet.articles', 'article')
            ->andWhere('article.number = :article')
            ->andWhere('article.language = :language')
            ->setParameters(array(
                'article' => $articleNr,
                'language' => $languageId
            ));

        return $queryBuilder;
    }

    /**
     * Get all Snippets
     *
     * Returns all Snippets. If the SnippetTemplate
     * is disabled, the Snippets depending on it won't be returned.
     * By Default all Snippets that are Disabled themselves are not returned.
     *
     * @param string $show     Define which Snippets to return, 'enabled' | 'disabled' | 'all'
     *
     * @return Doctrine\ORM\Query Query
     */
    public function getSnippets($show = 'enabled')
    {
        $queryBuilder = $this->getSnippetQueryBuilder($show);
        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Get Snippets for Article
     *
     * Returns all the associated Snippets to an Article. If the SnippetTemplate
     * is disabled, the Snippets depending on it won't be returned.
     * By Default all Snippets that are Disabled themselves are not returned.
     *
     * @param int    $article  Article number
     * @param string $language Language code in format "en" for example.
     * @param string $show     Define which Snippets to return, 'enabled' | 'disabled' | 'all'
     *
     * @return Doctrine\ORM\Query Query
     */
    public function getArticleSnippets($articleNr, $languageCode, $show = 'enabled')
    {
        $queryBuilder = $this->getArticleSnippetQueryBuilder($articleNr, $languageCode, $show);
        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Get Snippet
     *
     * @param int    $id   Snippet ID
     * @param string $show Define which Snippets to return, 'enabled' | 'disabled' | 'all'
     *
     * @return Newscoop\Entity\Snippet
     */
    public function getSnippetById($id, $show = 'enabled')
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException("ID is not numeric: ".$id);
        }

        $queryBuilder = $this->getSnippetQueryBuilder($show)
            ->andWhere('snippet.id = :id')
            ->setParameter('id', $id);

        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        return $result;
    }

    /**
     * Get Snippet by Name
     *
     * @param string  $name  Snippet Name
     * @param string  $show  Define which Snippets to return, 'enabled' | 'disabled' | 'all'
     * @param boolean $fuzzy Find fuzzy or not
     *
     * @return Doctrine\ORM\Query Query
     */
    public function findSnippetsByName($name, $show = 'enabled', $fuzzy = false)
    {
        $queryBuilder = $this->getSnippetQueryBuilder($show)
            ->andWhere('snippet.name LIKE :name');

        if ($fuzzy) {
            $queryBuilder
                ->setParameter('name', '%'.$name.'%');
        } else {
            $queryBuilder
                ->setParameter('name', $name.'%');
        }

        return $queryBuilder->getQuery();
    }

    /**
     * Get Snippet by Name
     *
     * @param int     $article  Article number
     * @param string  $language Language code in format "en" for example.
     * @param string  $name     Snippet Name
     * @param string  $show     Define which Snippets to return, 'enabled' | 'disabled' | 'all'
     * @param boolean $fuzzy    Find fuzzy or not
     *
     * @return Doctrine\ORM\Query Query
     */
    public function findSnippetsByNameForArticle($articleNr, $languageCode, $name, $show = 'enabled', $fuzzy = false)
    {
        $queryBuilder = $this->getArticleSnippetQueryBuilder($articleNr, $languageCode, $show)
            ->andWhere('snippet.name LIKE :name');

        if ($fuzzy) {
            $queryBuilder
                ->setParameter('name', '%'.$name.'%');
        } else {
            $queryBuilder
                ->setParameter('name', $name.'%');
        }

        return $queryBuilder->getQuery();
    }

    public function createSnippetForArticle($articleNr, $languageCode, array $snippetData)
    {
        // $snipp
        // $snippet = new Snippet();
    }
}
