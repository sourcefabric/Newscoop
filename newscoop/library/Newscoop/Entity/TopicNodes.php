<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Topic nodes entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="Topics")
 */
class TopicNodes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", name="id")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="node_left")
     * @var int
     */
    protected $leftNode;

    /**
     * @ORM\Column(type="integer", name="node_right")
     * @var int
     */
    protected $rightNode;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get left node
     *
     * @return int $leftNode
     */
    public function getLeftNode()
    {
        return $this->leftNode;
    }

    /**
     * Set left node
     *
     * @param int $leftNode
     *
     * @return int
     */
    public function setLeftNode($leftNode)
    {
        $this->leftNode = $leftNode;

        return $leftNode;
    }

    /**
     * Get right node
     *
     * @return int $rightNode
     */
    public function getRightNode()
    {
        return $this->rightNode;
    }

    /**
     * Set right node
     *
     * @param int $rightNode
     *
     * @return int
     */
    public function setRightNode($rightNode)
    {
        $this->rightNode = $rightNode;

        return $rightNode;
    }
}
