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
    Newscoop\Entity\Comment\Commenter,
    Newscoop\Datatable\Source as DatatableSource;

/**
 * Comments author repository
 */
class CommenterRepository extends DatatableSource
{
    /**
     * Method for geting a commenter
     *
     * @param Newscoop\Entity\Comment\Commenter $p_entity
     * @param array $p_values
     * @return Newscoop\Entity\Comment\Commenter $p_entity
     */
    public function save($p_entity, array $p_values)
    {

        $keys = array('user','name','email');
        $set = false;
        for($i=0;$i<count($keys);$i++)
            $set = $set || (isset($p_values[$keys[$i]]) && !empty($p_values[$keys[$i]]));
        if(!$set)
            throw new InvalidArgumentException();

        $em = $this->getEntityManager();
        if(!empty($p_values['user']))
        {
            if(is_numeric($p_values['user'])) {
                $userRepository = $em->getRepository('Newscoop\Entity\User');
                $p_values['user'] = $userRepository->find($p_values['user']);
            }
            if($p_values['user'])
            {
                $p_entity->setUser($p_values['user']);
                if(empty($p_values['name']))
                    $p_values['name'] = $p_values['user']->getName();
                if(empty($p_values['email']))
                    $p_values['email'] = $p_values['user']->getEmail();
           }
        }
        $commenters = $this->findBy(array( 'email' => $p_values['email'], 'name' => $p_values['name']));
        if(count($commenters)==1)
            $p_entity = $commenters[0];
        /*
        $acceptanceRepository = $em->getRepository('Newscoop\Entity\Comment\Acceptance');
        $acceptanceRepository->isBanned($p_entity);
        */
        if(!isset($p_values['url']))
            $p_values['url'] = '';
        $p_entity->setName($p_values['name'])
                 ->setEmail($p_values['email'])
                 ->setUrl($p_values['url'])
                 ->setIp($p_values['ip'])
                 ->setTimeCreated($p_values['time_created']);

        $em->persist($p_entity);
        return $p_entity;
    }

    /**
     * Delete a commenter
     *
     * @param Newscoop\Entity\Comment\Commenter $p_commenter
     * @return void
     */
    public function delete(Commenter $p_commenter)
    {
        $em = $this->getEntityManager();
        $em->remove($p_commenter);
    }
    public function flush()
    {
        $this->getEntityManager()->flush();
    }

}