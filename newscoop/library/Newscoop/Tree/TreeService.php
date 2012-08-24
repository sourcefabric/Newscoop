<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tree;

use Newscoop\Entity\TopicTree;

/**
 * Tree Service
 */
class TreeService
{
    /**
     * @var Doctrine\ORM\EntityRepository
     */
    protected $repository;

    /**
     * @param Doctrine\ORM\EntityManager $orm
     */
    public function __construct(\Doctrine\ORM\EntityManager $orm)
    {
        $this->orm = $orm;
        $this->repository = $this->orm->getRepository('Newscoop\Entity\TopicTree');
    }

    /**
     * Find tree items
     *
     * @return array
     */
    public function find()
    {
        return $this->repository->findBy(array(), array('node_left' => 'asc'));
    }

    /**
     * Save tree structure
     *
     * @param SimpleXMLElement $xml
     * @return void
     */
    public function save(\SimpleXMLElement $xml)
    {
        $this->clear();

        $left = 1;
        $this->saveChildren($xml, $left);
        $this->orm->flush();
    }

    /**
     * Clear tree structure
     *
     * @return void
     */
    private function clear()
    {
        $query = $this->orm->createQuery('DELETE FROM Newscoop\Entity\TopicTree');
        $query->execute();
    }

    /**
     * Save item children
     *
     * @param SimpleXMLElement $xml
     * @param int $left
     * @return int
     */
    private function saveChildren(\SimpleXMLElement $xml, $left)
    {
        if (!$xml->count()) {
            return $left;
        }

        foreach ($xml->li as $li) {
            $item = new TopicTree($this->parseId($li), $left++);
            $this->orm->persist($item);
            $item->setRight($this->saveChildren($li->ul, $left));
            $left = $item->getRight() + 1;
        }

        return $left;
    }

    /**
     * Parse item id
     *
     * @param SimpleXMLElement $xml
     * @return int
     */
    private function parseId(\SimpleXMLElement $xml)
    {
        return (int) array_pop(explode('_', (string) $xml['id'], 2));
    }
}
