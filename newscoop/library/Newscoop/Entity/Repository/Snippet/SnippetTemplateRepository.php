<?php
/**
 * @package Newscoop
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository\Snippet;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Newscoop\Entity\Snippet\SnippetTemplate;
// use Newscoop\Entity\Snippet\Commenter;

/**
 * Snippet repository
 */
class SnippetTemplateRepository extends EntityRepository
{

    /**
     * Get new instance of the Snippet
     *
     * @return \Newscoop\Entity\Snippet\SnippetTemplate
     */
    public function getPrototype()
    {
        return new SnippetTemplate;
    }

    private function getSnippetTemplateQueryBuilder($show)
    {
        $queryBuilder = $this->createQueryBuilder('template');

        if ($show == 'enabled') {
            $queryBuilder
                ->where('template.enabled = 1');
        }

        if ($show == 'disabled') {
            $queryBuilder
                ->where('template.enabled = 0');
        }

        return $queryBuilder;
    }

    /**
     * Get SnippetTemplate by ID
     *
     * @param int $id SnippetTemplate ID
     * @param string  $show  Define which Snippets to return, 'enabled' | 'disabled' | 'all'
     *
     * @return Newscoop\Entity\Snippet\SnippetTemplate
     */
    public function getTemplateById($id, $show = 'enabled')
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException("ID is not numeric: ".$id);
        }

        $queryBuilder = $this->getSnippetQueryBuilder($show)
            ->andWhere('template.id = :id')
            ->setParameter('id', $id);

        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        return $result;
    }

    /**
     * Find SnippetTemplate by Name
     *
     * @param string  $name  SnippetTemplate Name
     * @param string  $show  Define which Snippets to return, 'enabled' | 'disabled' | 'all'
     * @param boolean $fuzzy Find fuzzy or not
     *
     * @return Doctrine\ORM\Query Query
     */
    public function findSnippetTemplatesByName($name, $show = 'enabled', $fuzzy = false)
    {
        $queryBuilder = $this->getSnippetQueryBuilder($show)
            ->andWhere('template.name LIKE :name');

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
     * Get SnippetsTemplates for Article
     *
     * Returns all the SnippetsTemplates associated to the Snippets for an Article.
     *
     * @param int    $article  Article number
     * @param string $language Language code in format "en" for example.
     * @param string $show     Define which Snippets to return, 'enabled' | 'disabled' | 'all'
     *
     * @return Doctrine\ORM\Query Query
     */
    public function getSnippetTemplatesForArticle($articleNr, $languageCode, $show = 'enabled')
    {
        $em = $this->getEntityManager();
        $snippetTemplateIDsQuery = $em->getRepository('Newscoop\Entity\Snippet')
            ->getArticleSnippetQueryBuilder($articleNr, $languageCode, $show)
            ->select('template.id');

        $snippetTemplateIDsQueryResult = $snippetTemplateIDsQuery
            ->distinct()
            ->getQuery()
            ->getResult();

        $ids = array_map('current', $snippetTemplateIDsQueryResult);

        $queryBuilder = $this->createQueryBuilder('template');

        $queryBuilder->add('where',
            $queryBuilder->expr()->in('template.id', $ids)
        );

        return $queryBuilder->getQuery();
    }

    /**
     * Get Favourited SnippetTemplates
     *
     * @return Doctrine\ORM\Query Query
     */
    public function getFavourites()
    {
        $queryBuilder = $this->createQueryBuilder('template')
            ->andWhere('template.favourite = TRUE');
        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Get Enabled SnippetTemplates
     *
     * @return Doctrine\ORM\Query Query
     */
    public function getEnabled()
    {
        $queryBuilder = $this->createQueryBuilder('template')
            ->andWhere('template.enabled = TRUE');
        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Get Disabled SnippetTemplates
     *
     * @return Doctrine\ORM\Query Query
     */
    public function getDisabled()
    {
        $queryBuilder = $this->createQueryBuilder('template')
            ->andWhere('template.enabled = FALSE');
        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Get Active SnippetTemplates
     *
     * @return Doctrine\ORM\Query Query
     */
    public function getCurrentlyUsed()
    {
        $em = $this->getEntityManager();
        $snippetTemplateIDsQuery = $em->getRepository('Newscoop\Entity\Snippet')
            ->createQueryBuilder('snippet')
            ->select('template.id')
            ->join('snippet.template', 'template')
            ->distinct()
            ->getQuery()
            ->getResult();

        $ids = array_map('current', $snippetTemplateIDsQuery);

        $queryBuilder = $this->createQueryBuilder('template');
        $queryBuilder->add('where',
            $queryBuilder->expr()->in('template.id', $ids)
        );

        return $queryBuilder->getQuery();
    }

    /**
     * Flush method
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}
