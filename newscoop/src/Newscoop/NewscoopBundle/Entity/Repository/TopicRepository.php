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
use Gedmo\DemoBundle\Entity\Category;
use Closure;
use Gedmo\Translatable\TranslatableListener;

class TopicRepository extends NestedTreeRepository
{
    public $onChildrenQuery;

    public function getTopics()
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')
            ->createQueryBuilder('t');

        $countQueryBuilder = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')
            ->createQueryBuilder('t')
            ->select('count(t)');

        $topicsCount = $countQueryBuilder->getQuery()->getSingleScalarResult();

        $query = $queryBuilder->getQuery();
        $query->setHint('knp_paginator.count', $topicsCount);

        return $query;
    }

    public function findAllParentChoises(Category $node = null)
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
        return $this
            ->getQueryBuilder()
            ->select('node')
            ->from('Newscoop\NewscoopBundle\Entity\Topic', 'node')
            ->orderBy('node.root, node.lft', $direction)
            ->getQuery();
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
