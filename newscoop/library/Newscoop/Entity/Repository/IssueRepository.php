<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Newscoop\Entity\Publication;

/**
 * Issie repository
 */
class IssueRepository extends EntityRepository
{
    /**
     * Retrieve the latest issue. Optional an array for filtering can be
     * specified. Think of parameters: Publication, Languages, published or not,
     * etc.
     *
     * @param array $parameters Array containing filter options
     *
     * @return \Newscoop\Entity\Issue|null
     */
    public function getLatestBy(Array $parameters = array())
    {
        $issue  = $this->getEntityManager()
            ->getRepository('\Newscoop\Entity\Issue')
            ->findOneBy($parameters, array(
                'id' => 'DESC'
            ));

        return $issue;
    }
}
