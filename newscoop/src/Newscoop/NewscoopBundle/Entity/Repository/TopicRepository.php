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

class TopicRepository extends NestedTreeRepository
{
    public $onChildrenQuery;

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
            ->orderBy('node.root, node.lft', ':direction')
            ->setParameter('direction', $direction)
            ->getQuery();
    }
}
