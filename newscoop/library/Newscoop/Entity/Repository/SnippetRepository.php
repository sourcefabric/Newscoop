<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Newscoop\Entity\Snippet;
// use Newscoop\Entity\Snippet\Commenter;
use Newscoop\Datatable\Source as DatatableSource;
use Newscoop\Entity\User;
use Newscoop\Search\RepositoryInterface;
use Newscoop\NewscoopException\IndexException;

/**
 * Snippet repository
 */
class SnippetRepository extends EntityRepository
{

    /**
     * Get new instance of the Snippet
     *
     * @return \Newscoop\Entity\Snippet
     */
    public function getPrototype()
    {
        return new Snippet;
    }

    private function getSnippetQueryBuilder($show)
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

    private function getArticleSnippetQueryBuilder($articleNr, $language, $show)
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
     * Get Snippets for Article
     * 
     * Returns all the associated Snippets to an Article. If the SnippetTemplate
     * is disabled, the Snippets depending on it won't be returned.
     * By Default all Snippets that are Disabled themselves are not returned.
     *
     * @param  int $article  Article number
     * @param  string $language Language code in format "en" for example.
     * @param string $show Define which Snippets to return, 'enabled' | 'disabled' | 'all'
     *
     * @return Doctrine\ORM\Query           Query
     */
    public function getArticleSnippets($articleNr, $languageCode, $show = 'enabled')
    {
        $em = $this->getEntityManager();

        $queryBuilder = $this->getArticleSnippetQueryBuilder($articleNr, $languageCode, $show);
        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Get SnippetsTemplates for Article
     * 
     * Returns all the SnippetsTemplates associated to a Snippet for an Article.
     *
     * @param  int $article  Article number
     * @param  string $language Language code in format "en" for example.
     * @param string $show Define which Snippets to return, 'enabled' | 'disabled' | 'all'
     *
     * @return Doctrine\ORM\Query           Query
     */
    public function getArticleSnippetTemplates($articleNr, $languageCode, $show = 'enabled')
    {
        $em = $this->getEntityManager();
        $snippetTemplateIDsQuery = $this->getArticleSnippetQueryBuilder($articleNr, $languageCode, $show)
            ->select('template.id');

        $snippetTemplateIDsQueryResult = $snippetTemplateIDsQuery
            ->distinct()
            ->getQuery()
            ->getResult();

        $ids = array_map('current', $snippetTemplateIDsQuery);

        $queryBuilder = $em->getRepository('Newscoop\Entity\Snippet\SnippetTemplate')->createQueryBuilder('template');

        $queryBuilder->add('where',
            $queryBuilder->expr()->in('template.id', $ids)
        );

        return $queryBuilder->getQuery();
    }

    /**
     * Get Snippet
     *
     * @param int $id Snippet ID
     * @param string $show Define which Snippets to return, 'enabled' | 'disabled' | 'all'
     *
     * @return Newscoop\Entity\Snippet
     */
    public function getSnippetById($id, $show = 'enabled')
    {
        if (empty($id)) {
            throw new \InvalidArgumentException("ID is empty");
        }

        if (!is_numeric($id)) {
            throw new \InvalidArgumentException("ID is not numeric: ".$id);
        }

        $em = $this->getEntityManager();
        $queryBuilder = $this->getSnippetQueryBuilder($show)
            ->andWhere('snippet.id = :id')
            ->setParameter('id', $id);

        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        return $result;
    }

    /**
     * Get Snippet by Name
     *
     * @param string $name Snippet Name
     *
     * @return \Newscoop\Entity\Snippet
     */
    public function getSnippetByName($name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException("Name is empty");
        }

        return $this->findOneByName($name);
    }

    /**
     * Flush method
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}
