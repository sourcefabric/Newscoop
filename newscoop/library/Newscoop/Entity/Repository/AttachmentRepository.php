<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Newscoop\Entity\Attachment;
use Doctrine\ORM\Query;

/**
 * Attrachment Repository
 */
class AttachmentRepository extends EntityRepository
{
    /**
     * Get all attachments
     *
     * @return Query
     */
    public function getAttachments()
    {
        $query = $this->createQueryBuilder('i')
            ->getQuery();

        return $query;
    }

    /**
     * Get single attachment by id
     *
     * @param int $number
     *
     * @return Query
     */
    public function getAttachment($number)
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.id = :number')
            ->setParameter('number', $number)
            ->getQuery();

        return $query;
    }
}
