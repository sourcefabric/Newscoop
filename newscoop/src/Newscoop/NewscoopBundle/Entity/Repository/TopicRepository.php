<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\NewscoopBundle\Entity\Repository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Doctrine\ORM\Query;
use Newscoop\NewscoopBundle\Entity\Topic;
use Closure;
use Gedmo\Translatable\TranslatableListener;
use Gedmo\Tool\Wrapper\EntityWrapper;
use Gedmo\Exception\InvalidArgumentException;

class TopicRepository extends NestedTreeRepository
{
    public $onChildrenQuery;

    /**
     * Get all topics
     *
     * @param $languageCode Language code
     *
     * @return Doctrine\ORM\Query
     */
    public function getTopics($languageCode = null)
    {
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);

        $queryBuilder = $this
            ->getQueryBuilder('t')
            ->from($config['useObjectClass'], 't');

        if ($languageCode) {
            $queryBuilder->leftJoin('t.translations', 'tt')
                ->where('tt.locale = :locale')
                ->setParameter('locale', $languageCode);
        }

        $countQueryBuilder = clone($queryBuilder);
        $countQueryBuilder->select('count(t)');

        $queryBuilder->select('t');
        $topicsCount = $countQueryBuilder->getQuery()->getSingleScalarResult();
        $query = $this->setTranslatableHint($queryBuilder->getQuery(), $languageCode);
        $query->setHint('knp_paginator.count', $topicsCount);

        return $query;
    }

    /**
     * Get all parent choices
     *
     * @param Topic|null $node Topic object
     *
     * @return array
     */
    public function findAllParentChoices(Topic $node = null)
    {
        $dql = "SELECT c FROM {$this->_entityName} c";
        if (!is_null($node)) {
            $subSelect = "SELECT n FROM {$this->_entityName} n";
            $subSelect .= ' WHERE n.root = '.$node->getRoot();
            $subSelect .= ' AND n.lft BETWEEN '.$node->getLeft().' AND '.$node->getRight();

            $dql .= " WHERE c.id NOT IN ({$subSelect})";
        }
        $q = $this->_em->createQuery($dql);
        $q->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        $nodes = $q->getArrayResult();
        $indexed = array();
        foreach ($nodes as $node) {
            $indexed[$node['id']] = $node['title'];
        }

        return $indexed;
    }

    /**
     * Will do reordering based on current translations
     */
    public function childrenQuery($node = null, $direct = false, $sortByField = null, $direction = 'ASC', $include = false)
    {
        $q = parent::childrenQuery($node, $direct, $sortByField, $direction, $include);
        if ($this->onChildrenQuery instanceof Closure) {
            $c = &$this->onChildrenQuery;
            $c($q);
        }

        return $q;
    }

    public function getTopicsQuery($direction = 'ASC')
    {
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);

        return $this
            ->getQueryBuilder()
            ->select('node')
            ->from($config['useObjectClass'], 'node')
            ->orderBy('node.root, node.lft', $direction)
            ->getQuery();
    }

    /**
     * Gets the single topic's query by id
     *
     * @param int    $id     Topic id
     * @param string $locale Language code
     *
     * @return Query $query Query object
     */
    public function getSingleTopicQuery($id, $locale = null)
    {
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);

        $queryBuilder = $this->getQueryBuilder()
            ->select('t')
            ->from($config['useObjectClass'], 't')
            ->where('t.id = :id')
            ->setParameter('id', $id);

        $query = $queryBuilder->getQuery();

        return $this->setTranslatableHint($query, $locale);
    }

    /**
     * Get topics query and set translatable hints
     *
     * @param Query $query Query object
     */
    public function getTranslatableTopics($locale, $order = 'asc')
    {
        $query = $this
            ->getQueryBuilder()
            ->select('node', 't')
            ->from('Newscoop\NewscoopBundle\Entity\Topic', 'node')
            ->leftJoin('node.translations', 't')
            ->where("t.field = 'title'");

        $query = $query
            ->orderBy('node.root, node.lft', $order)
            ->getQuery();

        return $this->setTranslatableHint($query, $locale);
    }

    /**
     * Add hints to the query
     *
     * @param Query       $query  Query
     * @param string|null $locale Lecale to which fallback
     *
     * @return Query
     */
    public function setTranslatableHint(Query $query, $locale = null)
    {
        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        $query->setHint(
            TranslatableListener::HINT_INNER_JOIN,
            false
        );
        $query->setHint(
            TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $locale
        );
        $query->setHint(
            TranslatableListener::HINT_FALLBACK,
            true
        );

        return $query;
    }

    /**
     * Get all articles for given topic by topic id and language code
     *
     * @param int     $topicId         Topic id
     * @param string  $languageCode    Language code
     * @param boolean $defaultFallback Sets the language of the topic to the default one
     *
     * @return Query
     */
    public function getArticlesQueryByTopicIdAndLanguage($topicId, $languageCode, $defaultFallback = false)
    {
        $query = $this
            ->getQueryBuilder()
            ->select('node', 't')
            ->from('Newscoop\NewscoopBundle\Entity\Topic', 'node')
            ->leftJoin('node.translations', 't')
            ->where("t.field = 'title'")
            ->andWhere('node.id = :id')
            ->setParameter('id', $topicId);

        if ($defaultFallback) {
            return $this->setTranslatableHint($query->getQuery(), $languageCode);
        }

        $query->andWhere('t.locale = :locale')
            ->setParameter('locale', $languageCode);

        return $query->getQuery();
    }

    /**
     * Search topic by given query
     *
     * @param string $query
     * @param array  $sort
     *
     * @return Query
     */
    public function searchTopics($query, $sort = array(), $limit = null, $locale = null)
    {
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);
        $qb = $this->getQueryBuilder()
            ->select('t', 'tt')
            ->from($config['useObjectClass'], 't')
            ->leftJoin('t.translations', 'tt')
            ->where("tt.field = 'title'");

        $orX = $qb->expr()->orx();

        $orX->add($qb->expr()->like('t.title', $qb->expr()->literal("%{$query}%")));
        $orX->add($qb->expr()->like('tt.content', $qb->expr()->literal("%{$query}%")));
        $qb->andWhere($orX);

        if ((!empty($sort)) && is_array($sort)) {
            foreach ($sort as $sortColumn => $sortDir) {
                $qb->addOrderBy('t.'.$sortColumn, $sortDir);
            }
        }

        if (!is_null($limit)) {
            $qb->setMaxResults($limit);
        }

        return $this->setTranslatableHint($qb->getQuery(), $locale);
    }

    /**
     * Find topic options
     *
     * @return array
     */
    public function findOptions()
    {
        $query = $this->createQueryBuilder('t')
            ->select('t.id, t.title as name')
            ->orderBy('t.title')
            ->getQuery();

        $options = array();
        foreach ($query->getResult() as $row) {
            $options[$row['id']] = $row['name'];
        }

        return $options;
    }

    /**
     * Gets topic's path
     *
     * @param Topic $topic Topic
     *
     * @return string Path of the topic
     */
    public function getReadablePath(Topic $topic, $locale = null)
    {
        $pathQuery = $this->getPathQuery($topic);
        if (!$locale) {
            $locale = $this->getTranslatableTopicLocale($topic);
        }

        $this->setTranslatableHint($pathQuery, $locale);
        $path = $pathQuery->getArrayResult();
        $pathStr = '';
        foreach ($path as $element) {
            $pathStr = $pathStr.' / '.$element['title'];
        }

        return $pathStr;
    }

    public function getTranslatableTopicLocale(Topic $topic)
    {
        foreach ($topic->getTranslations()->toArray() as $translation) {
            if ($translation->getField() == 'title' && $topic->getTitle() == $translation->getContent()) {
                return $translation->getLocale();
            }
        }
    }

    /**
     * Get Topics for Article
     *
     * Returns all the associated Topics to an Article.
     *
     * @param int    $articleNr    Article number
     * @param string $languageCode Language code in format "en" for example.
     * @param string $order        Order of the topics, default ascending
     *
     * @return Doctrine\ORM\Query Query
     */
    public function getArticleTopics($articleNr, $languageCode, $order = "asc")
    {
        $em = $this->getEntityManager();
        $articleTopicsIds = $em->getRepository('Newscoop\Entity\ArticleTopic')->getArticleTopicsIds($articleNr, true);
        $articleTopicsIds = $articleTopicsIds->getArrayResult();
        $topicsIds = array();
        foreach ($articleTopicsIds as $key => $value) {
            $topicsIds[$key] = $value[1];
        }

        $queryBuilder = $this->createQueryBuilder('t')
            ->where('t.id IN(:ids)')
            ->setParameters(array(
                'ids' => $topicsIds,
            ))
            ->orderBy('t.root, t.lft', $order);

        $countQueryBuilder = clone $queryBuilder;
        $countQueryBuilder->select('COUNT(t)');
        $count = $countQueryBuilder->getQuery()->getSingleScalarResult();

        $query = $queryBuilder->getQuery();
        $query->setHint('knp_paginator.count', $count);
        $query = $this->setTranslatableHint($query, $languageCode);

        return $query;
    }

    /**
     * Count topics by given criteria
     *
     * @param array $criteria
     *
     * @return integer
     */
    public function countBy(array $criteria = array())
    {
        $queryBuilder = $this->getQueryBuilder()
            ->select('COUNT(t)')
            ->from($this->getEntityName(), 't');

        foreach ($criteria as $property => $value) {
            if (!is_array($value)) {
                $queryBuilder->andWhere("t.$property = :$property");
            }
        }

        $query = $queryBuilder->getQuery();
        foreach ($criteria as $property => $value) {
            if (!is_array($value)) {
                $query->setParameter($property, $value);
            }
        }

        return (int) $query->getSingleScalarResult();
    }

    /**
     * Gets topic by given id or name
     *
     * @param string|integer $topicIdOrName Topicid or name
     * @param string|integer $locale        Current locale, language code or id
     *
     * @return Query
     */
    public function getTopicByIdOrName($topicIdOrName, $locale)
    {
        $qb = $this->getQueryBuilder()
            ->select('t', 'tt', "p")
            ->from($this->getEntityName(), 't')
            ->leftJoin("t.translations", "tt")
            ->leftJoin("t.parent", "p")
            ->where("tt.field = 'title'");

        if (is_numeric($topicIdOrName)) {
            $qb
                 ->andWhere("t.id = :id")
                 ->setParameter("id", $topicIdOrName);
        } else {
            $qb
                ->andWhere("t.title = :title")
                ->setParameter("title", $topicIdOrName);
        }

        if (is_numeric($locale)) {
            $language = $this->_em->getReference('Newscoop\Entity\Language', $locale);
            $locale = $language->getCode();
        }

        $topic = $this->setTranslatableHint($qb->getQuery(), $locale);

        return $topic;
    }

    /**
     * @see getChildrenQueryBuilder
     */
    public function childrenWithTranslations($node = null, $locale = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
    {
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);

        $qb = $this->getQueryBuilder();
        $qb->select('node', 't')
            ->from($config['useObjectClass'], 'node')
            ->leftJoin('node.translations', 't')
        ;
        if ($node !== null) {
            if ($node instanceof $meta->name) {
                $wrapped = new EntityWrapper($node, $this->_em);
                if (!$wrapped->hasValidIdentifier()) {
                    throw new InvalidArgumentException("Node is not managed by UnitOfWork");
                }
                if ($direct) {
                    $id = $wrapped->getIdentifier();
                    $qb->where($id === null ?
                        $qb->expr()->isNull('node.'.$config['parent']) :
                        $qb->expr()->eq('node.'.$config['parent'], is_string($id) ? $qb->expr()->literal($id) : $id)
                    );
                } else {
                    $left = $wrapped->getPropertyValue($config['left']);
                    $right = $wrapped->getPropertyValue($config['right']);
                    if ($left && $right) {
                        $qb
                            ->where($qb->expr()->lt('node.'.$config['right'], $right))
                            ->andWhere($qb->expr()->gt('node.'.$config['left'], $left))
                        ;
                    }
                }
                if (isset($config['root'])) {
                    $rootId = $wrapped->getPropertyValue($config['root']);
                    $qb->andWhere($rootId === null ?
                        $qb->expr()->isNull('node.'.$config['root']) :
                        $qb->expr()->eq('node.'.$config['root'], is_string($rootId) ? $qb->expr()->literal($rootId) : $rootId)
                    );
                }
                if ($includeNode) {
                    $idField = $meta->getSingleIdentifierFieldName();
                    $qb->where('('.$qb->getDqlPart('where').') OR node.'.$idField.' = :rootNode');
                    $qb->setParameter('rootNode', $node);
                }
            } else {
                throw new \InvalidArgumentException("Node is not related to this repository");
            }
        } else {
            if ($direct) {
                $qb->where($qb->expr()->isNull('node.'.$config['parent']));
            }
        }
        if (!$sortByField) {
            $qb->orderBy('node.'.$config['left'], 'ASC');
        } elseif (is_array($sortByField)) {
            $fields = '';
            foreach ($sortByField as $field) {
                $fields .= 'node.'.$field.',';
            }
            $fields = rtrim($fields, ',');
            $qb->orderBy($fields, $direction);
        } else {
            if ($meta->hasField($sortByField) && in_array(strtolower($direction), array('asc', 'desc'))) {
                $qb->orderBy('node.'.$sortByField, $direction);
            } else {
                throw new InvalidArgumentException("Invalid sort options specified: field - {$sortByField}, direction - {$direction}");
            }
        }

        return $this->setTranslatableHint($qb->getQuery(), $locale);
    }

    /**
     * {@inheritDoc}
     */
    public function getRootNodes($locale = null, $childrenLevel = false, $sortByField = null, $direction = 'asc')
    {
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);
        $qb = $this->getQueryBuilder();
        $qb
            ->select('node', 't', 'c')
            ->from($config['useObjectClass'], 'node')
            ->leftJoin('node.translations', 't')
            ->leftJoin('node.children', 'c');

        if (!$childrenLevel) {
            $qb->where($qb->expr()->isNull('node.'.$config['parent']));
        } else {
            $qb->where($qb->expr()->isNull('node.'.$config['parent']));
        }

        if ($sortByField !== null) {
            $qb->orderBy('node.'.$sortByField, strtolower($direction) === 'asc' ? 'asc' : 'desc');
        } else {
            $qb->orderBy('node.'.$config['left'], 'ASC');
        }

        return $this->setTranslatableHint($qb->getQuery(), $locale);
    }
}
