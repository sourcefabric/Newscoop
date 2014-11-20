<?php
/**
 * @package Newscoop
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository\Snippet;

use Doctrine\ORM\EntityRepository;
use Newscoop\Entity\Snippet\SnippetTemplate;

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

    /**
     * @param string $show
     */
    public function getSnippetTemplateQueryBuilder($show)
    {
        if (!in_array($show, array('enabled', 'disabled', 'all'))) {
            $show = 'enabled';
        }

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

        $queryBuilder = $this->getSnippetTemplateQueryBuilder($show)
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
        $queryBuilder = $this->getSnippetTemplateQueryBuilder($show)
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
     * @param int    $articleNr  Article number
     * @param string $languageCode Language code in format "en" for example.
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

    public function deleteSnippetTemplate($id, $force = false)
    {
        // check if the SnippetTemplate has any Snippets attached to it.
        $snippetTemplate = $this->getTemplateById($id, 'all');
        if (!is_null($snippetTemplate)) {
            $snippets = $snippetTemplate->getSnippets()->toArray();
            if (count($snippets) == 0 || $force == true) {
                $em = $this->getEntityManager();
                $em->remove($snippetTemplate);
                $em->flush();

                return true;
            } else {
                foreach ($snippets as $snippet) {
                    $snippetIdArr[$snippet->getId()] = $snippet->getId();
                }
                $snippetIds = implode(", ", array_flip($snippetIdArr));

                throw new \Newscoop\Exception\ResourcesConflictException('SnippetTemplate with ID: '.$id.' is in use by Snippets ('.$snippetIds.')');
            }
        } else {
            throw new \Exception('SnippetTemplate with ID: '.$id.' does not exist');
        }
    }

    public function save(SnippetTemplate $snippetTemplate)
    {
        if (!$snippetTemplate->hasName()) {
            throw new \InvalidArgumentException("SnippetTemplate name cannot be empty");
        }

        if (!$snippetTemplate->hasTemplateCode()) {
            throw new \InvalidArgumentException("SnippetTemplate templateCode cannot be empty");
        }

        if (!$snippetTemplate->hasFields()) {
            throw new \InvalidArgumentException("SnippetTemplate requires at least 1 SnippetTemplateField");
        }
        foreach($snippetTemplate->getFields()->toArray() as $field) {
            $field->setTemplate($snippetTemplate);
        }

        $em = $this->getEntityManager();
        $em->persist($snippetTemplate);
        $em->flush();
    }
}