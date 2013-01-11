<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Topic;

use Doctrine\ORM\EntityManager;

/**
 * Topic Service
 */
class TopicService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get options for forms
     *
     * @return array
     */
    public function getMultiOptions()
    {
        return $this->em->getRepository('Newscoop\Entity\Topic')->findOptions();
    }
}
