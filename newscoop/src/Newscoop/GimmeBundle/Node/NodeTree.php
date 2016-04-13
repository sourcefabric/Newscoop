<?php
/**
 * @package   Newscoop\Gimme
 * @author    Daniel Read <daniel.read@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Node;

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
            if ($hierarchable->getParent() instanceof \Newscoop\Entity\Comment) {
                $node = new Node($hierarchable->getId(), $hierarchable->getParent()->getId(), new \MetaComment($hierarchable->getId()));
            } else {
                $node = new Node($hierarchable->getId(), 0, new \MetaComment($hierarchable->getId()));
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
