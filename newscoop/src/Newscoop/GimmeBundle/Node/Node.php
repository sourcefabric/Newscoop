<?php
/**
 * @package   Newscoop\Gimme
 * @author    Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Node;

/**
 * Node class
 * A treewalker for nesting objects
 */
class Node
{
    public $id;
    public $pid;
    public $data;

    private $children = array();

    /**
     * Create the root Node object to nest the rest inside of
     *
     * @param mixed $id   Object (ID)
     * @param mixed $pid  Parent Object (ID)
     * @param mixed $data Object Data
     */
    public function __construct($id, $pid, $data)
    {
        $this->id = $id;
        $this->pid = $pid;
        $this->data = $data;
    }

    /**
     * Flatten created Node tree into an array
     *
     * @param bool $rootNode include root Node or not
     *
     * @return array flattened Node tree as array
     */

    public function flatten($rootNode = true)
    {
        $aggregate = ($rootNode) ? array($this->data) : array();

        foreach ($this->children as $child) {
            foreach ($child->flatten() as $flat) {
                $aggregate[] = $flat;
            }
        }

        return $aggregate;
    }

    /**
     * @param array $nodes array of Nodes
     */
    public function buildTree(array $nodes)
    {
        $children = $this->buildSubTree($nodes, $this);
        $this->setChildren($children);
    }

    /**
     * @param array $nodes array of Nodes
     * @param $parent Node whose subtree is to be built
     * @return array Nodes, each with its subtree built
     */
    private function buildSubTree(array $nodes, $parent)
    {
        $children = array();

        foreach ($nodes as $node) {
            if ($node->pid === $parent->id) {
                $grandChildren = $node->buildSubTree($nodes, $node);
                $node->setChildren($grandChildren);
                $children[$node->id] = $node;
                unset($nodes[$node->id]);
            }
        }

        return $children;
    }

    /**
     * @param array $children array of Nodes
     */
    private function setChildren(array $children)
    {
        $this->children = $children;
    }
}
