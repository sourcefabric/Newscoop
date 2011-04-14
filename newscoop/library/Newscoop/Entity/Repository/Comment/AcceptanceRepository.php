<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository\Comment;

use
    InvalidArgumentException,
    DateTime,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\QueryBuilder,
    Newscoop\Datatable\Source as DatatableSource;

/**
 * Comments users repository
 */
class CommentsUsersRepository extends DatatableSource
{
    /**
     * Method for geting a user
     *
     * @param Newscoop\Entity\CommentsAcceptance $p_enitity
     * @param array $p_values
     * @return Newscoop\Entity\CommentsAcceptance $p_enitity
     */
    public function saveDeny($p_entity, array $p_values)
    {

        foreach($p_values as $key => $value) {
            $p_entity->setType('deny');
            $p_entity->setForColumn($key);
            $p_entity->setSearch($value);
            $p_entity->setSearchType('normal');

        }
        return $p_entity;
    }
}
