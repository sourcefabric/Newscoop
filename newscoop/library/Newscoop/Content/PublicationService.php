<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Content;

use Doctrine\ORM\EntityManager;

/**
 * Publication Service
 */
class PublicationService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $orm;

    /**
     * @param Doctrine\ORM\EntityManager $orm
     */
    public function __construct(EntityManager $orm)
    {
        $this->orm = $orm;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        $query = $this->orm->getRepository('Newscoop\Entity\Publication')
            ->createQueryBuilder('p')
            ->select('p.id, p.name')
            ->orderBy('p.name, p.id')
            ->getQuery();

        $options = array();
        foreach ($query->getResult() as $row) {
            $options[$row['id']] = $row['name'];
        }

        return $options;
    }

    /**
     * Find all publications
     *
     * @return array
     */
    public function findAll()
    {
        return $this->orm->getRepository('Newscoop\Entity\Publication')
            ->findAll();
    }

    public function getPublicationsForMenu()
    {
        return $this->orm->getRepository('Newscoop\Entity\Publication')
            ->createQueryBuilder('p')
            ->select('p', 'i', 's')
            ->leftJoin('p.issues', 'i')
            ->leftJoin('i.sections', 's')
            ->getQuery();
    }
}
