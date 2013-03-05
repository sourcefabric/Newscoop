<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Notice repository.
 */
class NoticeCategoryRepository extends NestedTreeRepository
{
    /**
     * Get all notice categories as array
     * @return array
     */
    public function getAllCategoriesArray()
    {

        // create query to fetch tree nodes
        $query = $this->createQueryBuilder('c')
            ->select('category')
            ->from('Newscoop\Entity\NoticeCategory', 'category')
            ->orderBy('category.root, category.lft', 'ASC')
            ->getQuery();

        return $query->getArrayResult();
    }
}
