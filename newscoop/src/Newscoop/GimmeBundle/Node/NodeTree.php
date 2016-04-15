<?php
/**
 * @package   Newscoop\Gimme
 * @author    Daniel Read <daniel.read@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Node;

use Newscoop\Entity\Hierarchable;

class NodeTree
{
    private $root;

    public function __construct()
    {
        $this->root = new Node(0,0,'');
    }

    public function build(array $hierarchables)
    {
        $nodes = array();
        foreach ($hierarchables as $hierarchable) {
            if ($hierarchable->getParent() instanceof Hierarchable) {
                $node = new Node($hierarchable->getId(), $hierarchable->getParent()->getId(), $hierarchable);
            } else {
                $node = new Node($hierarchable->getId(), 0, $hierarchable);
            }
            $nodes[$node->id] = $node;
        }

        $this->root->buildTree($nodes);
    }

    public function getFlattened()
    {
        return $this->root->flatten(false);
    }
}
