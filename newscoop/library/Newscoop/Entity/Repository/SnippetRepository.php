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

    private function getArticleSnippetQueryBuilder($articleNr, $language)
    {
        $em = $this->getEntityManager();
        $languageId = $em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($language)->getId();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Snippet')
            ->createQueryBuilder('snippet')
            ->join('snippet.articles', 'article')
            ->join('snippet.template', 'template')
            ->where('article.number = :article')
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
     * @param  int $enabled Return enabled Snippets (default), 0 returns disabled.
     *
     * @return Doctrine\ORM\Query           Query
     */
    public function getArticleSnippets($articleNr, $languageCode, $enabled = 1)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $this->getArticleSnippetQueryBuilder($articleNr, $languageCode)
                ->andWhere('snippet.enabled = :enabled')
                ->andWhere('template.enabled = 1')
                ->setParameter('enabled', $enabled);

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
     * @param  int $enabled Return enabled SnippetTemplates (default), 0 returns disabled.
     *
     * @return Doctrine\ORM\Query           Query
     */
    public function getArticleSnippetTemplates($articleNr, $languageCode, $enabled = 1)
    {
        $em = $this->getEntityManager();
        $snippetTemplateIDsQuery = $this->getArticleSnippetQueryBuilder($articleNr, $languageCode)
            ->select('template.id')
            ->andWhere('snippet.enabled = :enabled')
            ->andWhere('template.enabled = 1')
            ->setParameter('enabled', $enabled)
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
     * Flush method
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}
