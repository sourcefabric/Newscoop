<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Newscoop\Image\LocalImage;

/**
 * Image Repository
 */
class ImageRepository extends EntityRepository
{
    /**
     * Get images for storage update
     *
     * @param int $maxResults
     * @return array
     */
    public function findImagesForStorageUpdate($maxResults)
    {
        $query = $this->createQueryBuilder('i')
            ->andWhere('i.isUpdatedStorage = 0')
            ->andWhere('i.location = :local')
            ->setMaxResults($maxResults)
            ->getQuery();

        $query->setParameter('local', LocalImage::LOCATION_LOCAL);

        return $query->getResult();
    }

    /**
     * Get image file references count
     *
     * @param string $file
     * @return int
     */
    public function getImageFileReferencesCount($file)
    {
        $query = $this->createQueryBuilder('i')
            ->select('COUNT(i)')
            ->where('i.basename = :file')
            ->getQuery();

        $query->setParameter('file', $file);

        return $query->getSingleScalarResult();
    }
}
