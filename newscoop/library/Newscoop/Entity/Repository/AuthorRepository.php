<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Newscoop\Datatable\Source as DatatableSource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Author repository
 */
class AuthorRepository extends DatatableSource
{

    public function getAuthor($id)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Author')
            ->createQueryBuilder('a');

        $queryBuilder->where('a.id = :id')
            ->setParameter('id', $id);

        $query = $queryBuilder->getQuery();
        
        return $query;
    }
}
