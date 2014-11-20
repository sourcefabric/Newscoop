<?php
/**
 * @package Newscoop
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.u.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Newscoop\Entity\Snippet;
use Newscoop\Exception\ResourcesConflictException;

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
    protected function getSnippetQueryBuilder($show, $templateEnabled = true)
    {
        if (!in_array($show, array('enabled', 'disabled', 'all'))) {
            $show = 'enabled';
        }

        $queryBuilder = $this->createQueryBuilder('snippet');

        if ($templateEnabled) { // by default a Template should always be enabled
            $queryBuilder
                ->join('snippet.template', 'template')
                ->andWhere('template.enabled = 1');
        }

        if ($show == 'enabled') {
            $queryBuilder
                ->andWhere('snippet.enabled = 1');
        }

        if ($show == 'disabled') {
            $queryBuilder
                ->andWhere('snippet.enabled = 0');
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
     * @param int    $articleNr  Article number
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
     * Get all the Articles attached to a Snippet
     *
     * @param integer $id SnippetID
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    protected function getSnippetArticles($id)
    {
        $queryBuilder = $this->getSnippetQueryBuilder('all', false)
            ->andWhere('snippet.id = :id')
            ->setParameter('id', $id);

        $snippet = $queryBuilder->getQuery()->getOneOrNullResult();

        if (!$snippet) {
            throw new \Exception('Snippet with ID: '.$id.' does not exist');
        }
    
        return $snippet->getArticles();
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
     * @param int    $articleNr  Article number
     * @param string $languageCode Language code in format "en" for example.
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
    public function getSnippetById($id, $show = 'enabled', $template = true)
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException("ID is not numeric: ".$id);
        }

        $queryBuilder = $this->getSnippetQueryBuilder($show, $template)
            ->andWhere('snippet.id = :id')
            ->setParameter('id', $id);

        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        return $this->checkIfSnippetIsDisabled($queryBuilder, $result, $show);
    }

    /**
     * Check if a Snippet is disabled
     * Internal use only
     *
     * @param Doctrine\ORM\QueryBuilder $queryBuilder
     * @param mixed $result
     * @param string $show
     */
    protected function checkIfSnippetIsDisabled($queryBuilder, $result, $show)
    {
        if ($show == 'enabled' && is_null($result)) {
            $expr = $queryBuilder->getDQLPart('where')->getParts();
            $newExpr = new \Doctrine\ORM\Query\Expr\Andx();
            $newExpr->addMultiple(preg_grep("/\bsnippet.enabled\b/i", $expr, PREG_GREP_INVERT));
            $queryBuilder->resetDQLPart('where');
            $queryBuilder->add('where', $newExpr);

            if (!is_null($queryBuilder->getQuery()->getOneOrNullResult())) {
                throw new \Exception('Result was found but disabled.');
            }
        }

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
     * @param int     $articleNr  Article number
     * @param string  $languageCode Language code in format "en" for example.
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

	public function addSnippetToArticle(Snippet $snippet, \Newscoop\Entity\Article $article)
	{
		$snippet->addArticle($article);
		$this->save($snippet);
	}

	public function removeSnippetFromArticle(Snippet $snippet, \Newscoop\Entity\Article $article)
	{
		$snippet->removeArticle($article);
		$this->save($snippet);
	}

    public function deleteSnippet($id, $force = false)
    {
        $articles = $this->getSnippetArticles($id)->toArray();
        if (count($articles) == 0 || $force == true) {
            $snippet = $this->getSnippetById($id, 'all', false);
            $em = $this->getEntityManager();
            $em->remove($snippet);
            $em->flush();

            return true;
        } else {
            $articleNumbers = array();
            foreach ($articles as $article) {
                $articleNumbersArr[$article->getNumber()] = $article->getNumber();
            }
            $articleNumbers = implode(", ", array_flip($articleNumbersArr));
            throw new \Newscoop\Exception\ResourcesConflictException('Snippet with ID: '.$id.' is in use by Articles ('.$articleNumbers.')');
        }
    }

    public function save(Snippet $snippet)
    {
        if (!$snippet->hasName()) {
            throw new \InvalidArgumentException("Snippet name cannot be empty");
        }

        foreach($snippet->getFields()->toArray() as $field) {
            if ($field->isRequired()) {
                if (!$field->hasData()) {
                    throw new \InvalidArgumentException("SnippetField: '".$field->getFieldName()."' requires a value");
                }
            }
        }

        if ($snippet->getTemplate()->hasController()) {
            $object = $snippet->getTemplate()->executeController($snippet);
            $snippet = $object->getSnippet();
        }

        $em = $this->getEntityManager();
        $em->persist($snippet);
        $em->flush();
    }
}