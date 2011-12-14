<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Content;

use Doctrine\ORM\EntityManager;

/**
 * Section Service
 */
class SectionService
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
        $query = $this->orm->getRepository('Newscoop\Entity\Section')
            ->createQueryBuilder('s')
            ->select('s.number, s.name')
            ->orderBy('s.name, s.number')
            ->getQuery();

        $options = array();
        foreach ($query->getResult() as $row) {
            $options[$row['number']] = $row['name'];
        }

        return $options;
    }
}
