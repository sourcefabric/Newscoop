<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use DateTime,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\QueryBuilder,
    Newscoop\Datatable\Source as DatatableSource;

/**
 * Article repository
 */
class ArticleRepository extends DatatableSource
{
    private $fields = array();

    public function setFields($fields)
    {
        $this->fields = explode(',', $fieds);
    }

    public function getArticles()
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a');

        if (count($this->fields) > 0) {
            foreach ($this->fields as $field) {
                $queryBuilder->add('select', 'a.'.$field);
            }
        }
        
        return $queryBuilder->getQuery();
    }
}
