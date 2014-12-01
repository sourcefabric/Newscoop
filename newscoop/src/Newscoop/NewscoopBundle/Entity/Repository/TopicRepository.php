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

class TopicRepository extends NestedTreeRepository
{
    public $onChildrenQuery;

    public function getTopics()
    {
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);

        $queryBuilder = $this
            ->getQueryBuilder('t')
            ->select('t')
            ->from($config['useObjectClass'], 't');

        $countQueryBuilder = $this
            ->getQueryBuilder('t')
            ->select('count(t)')
            ->from($config['useObjectClass'], 't');

        $topicsCount = $countQueryBuilder->getQuery()->getSingleScalarResult();

        $query = $queryBuilder->getQuery();
        $query->setHint('knp_paginator.count', $topicsCount);

        return $query;
    }

    public function findAllParentChoises(Topic $node = null)
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
     * Saves topic position when it was dragged and dropped
     *
     * @param Topic   $node     Dragged topic object
     * @param int     $parentId Parent of dragged topic
     * @param boolean $asRoot   If topic is dragged from children to root level
     * @param array   $params   Parameters with positions
     *
     * @return boolean
     */
    public function saveTopicPosition(Topic $node, $params)
    {
        if (isset($params['parent']) && $params['parent']) {
            $parent = $this->findOneBy(array(
                'id' => $params['parent'],
            ));

            if (!$parent) {
                return false;
            }

            $node->setOrder(null);
            foreach ($params as $key => $isSet) {
                switch ($key) {
                    case 'first':
                        if ($isSet) {
                            $this->persistAsFirstChildOf($node, $parent);
                        }
                        break;
                    case 'last':
                        if ($isSet) {
                            $this->persistAsLastChildOf($node, $parent);
                        }
                        break;
                    case 'middle':
                        if ($isSet) {
                            $this->persistAsNextSiblingOf($node, $parent);
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        // when dragging childrens to roots
        if (isset($params['asRoot']) && $params['asRoot']) {
            $node->setParent(null);
        }

        $this->_em->flush();

        return true;
    }

    /**
     * Reorder root topics
     *
     * @param array $rootNodes Root topics
     * @param array $order     Topics ids in order
     *
     * @return boolean
     */
    public function reorderRootNodes($rootNodes, $order = array())
    {
        foreach ($rootNodes as $rootNode) {
            $rootNode->setOrder(null);
        }

        $this->_em->flush();

        if (count($order) > 1) {
            $counter = 0;

            foreach ($order as $item) {
                foreach ($rootNodes as $rootNode) {
                    if ($rootNode->getId() == $item) {
                        $rootNode->setOrder($counter + 1);
                        $counter++;
                    }
                }
            }
        } else {
            $counter = 1;
            foreach ($rootNodes as $rootNode) {
                $rootNode->setOrder($counter);
                $counter++;
            }
        }

        $this->_em->flush();

        return true;
    }

    /**
     * Saves new topic
     *
     * @param Topic       $node   Topic object
     * @param string|null $locale Language code
     *
     * @return boolean
     */
    public function saveNewTopic(Topic $node, $locale = null)
    {
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);
        $node->setTranslatableLocale($locale ?: $node->getTranslatableLocale());
        if (!$node->getParent()) {
            $qb = $this->getQueryBuilder('t')
                ->from($config['useObjectClass'], 't');
            $maxOrderValue = $qb
                ->select('MAX(t.topicOrder)')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult();

            $node->setOrder((int) $maxOrderValue + 1);
        }

        $this->_em->persist($node);
        $this->_em->flush();

        return true;
    }

    /**
     * Gets the single topic's query by id
     *
     * @param int $id Topic id
     *
     * @return Query $query Query object
     */
    public function getSingleTopicQuery($id)
    {
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);

        $queryBuilder = $this->getQueryBuilder()
            ->select('t')
            ->from($config['useObjectClass'], 't')
            ->where('t.id = :id')
            ->setParameter('id', $id);

        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Get topics query and set translatable hints
     *
     * @param Query $query Query object
     */
    public function getTranslatableTopicsQuery($locale, $order = 'asc')
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
    public function searchTopicsQuery($query, $sort = array())
    {
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);
        $qb = $this->getQueryBuilder()
            ->select('t')
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

        return $this->setTranslatableHint($qb->getQuery());
    }
}
