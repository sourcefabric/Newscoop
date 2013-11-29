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
     * Retrieve the latest issue for a publication
     *
     * @param  NewscoopEntityPublication $publication    Publication
     * @param  string                    $workflowStatus Published (Y) or not (N)
     *
     * @return \Newscoop\Entity\Issue|null
     */
    public function getLatestByPublication(Publication $publication, $workflowStatus = 'Y')
    {
        $issue  = $this->getEntityManager()
            ->getRepository('\Newscoop\Entity\Issue')
            ->findOneBy(array(
                'publication' => $publication->getId(),
                'workflowStatus' => 'Y'
            ), array(
                'id' => 'DESC'
            ));

        return $issue;
    }
}
