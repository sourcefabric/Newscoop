<?php
/**
 * @package   Newscoop\Node
 * @author    Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

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
     * Insert a New Node inside the Root Node
     *
     * @param Node $node Node object
     *
     * @return bool success
     */
    public function insertNode(Node $node)
    {
        if ($node->pid == $this->id) {
            $this->children[] = $node;

            return true;
        }

        foreach ($this->children as $child) {
            if ($child->insertNode($node)) {
                return true;
            }
        }

        return false;
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
}
