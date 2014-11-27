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
        $em = $this->getEntityManager();
        $queryBuilder = $this
            ->getQueryBuilder('t');

        $countQueryBuilder = $this
            ->getQueryBuilder('t')
            ->select('count(t)');

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
     * @return void|boolean
     */
    public function saveTopicPosition(Topic $node, $parentId, $asRoot, $params)
    {
        if ($parentId) {
            $parent = $this->findOneBy(array(
                'id' => $parentId,
            ));

            if (!$parent) {
                return true;
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
        if ($asRoot) {
            $node->setParent(null);
        }

        $this->_em->flush();
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
}
