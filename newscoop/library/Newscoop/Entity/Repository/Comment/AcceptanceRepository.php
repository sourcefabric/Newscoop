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
    Newscoop\Datatable\Source as DatatableSource,
    Newscoop\Entity\Comment\Acceptance,
    Newscoop\Entity\Publication;

/**
 * Comment Acceptance repository
 */
class AcceptanceRepository extends DatatableSource
{
    /**
     * Save a acceptance criteria
     *
     * @param Newscoop\Entity\Comment\Acceptance $p_acceptance
     * @param array $p_values
     * @return void
     */
    public function save(Acceptance $p_acceptance, array $p_values)
    {

        $em = $this->getEntityManager();

        $publicationRepository = $em->getRepository('Newscoop\Entity\Publication');
        $forum = $publicationRepository->find($p_values['forum']);
        $p_acceptance->setSearch($p_values['search'])
                     ->setSearchType($p_values['search_type'])
                     ->setForum($forum)
                     ->setType($p_values['type'])
                     ->setForColumn($p_values['for_column']);
        $em->persist($p_acceptance);
    }


    /**
     * Method that removes ban for a user
     *
     * @param array $p_params
     */
    public function unban($p_forum, array $p_params)
    {
        foreach($p_params as $key => $value)
        {
            $value = array(
                'forum' => $p_forum,
                'search' => $value,
                'for_column' => $key,
                'type' => 'deny',
                'search_type' => 'normal'
            );
            $acceptances = $this->findBy( $p_params );
            $this->delete($acceptances[0]);
        }
    }

    /**
     * Method that adds ban for a user
     *
     * @param array $p_params
     */
    public function ban($p_forum, array $p_params)
    {
        foreach($p_params as $key => $value)
        {
            $value = array(
                'forum' => $p_forum,
                'search' => $value,
                'for_column' => $key,
                'type' => 'deny',
                'search_type' => 'normal'
            );
            if($this->matched($p_params))
                return;
            $this->save(new Acceptance, $value);
        }
    }

    public function matched(array $p_params)
    {
        // this is for simple match if all search_type are normal
        // @todo implement later regex
        $results = $this->findBy( $p_params );

        if(count($results)>0)
            return true;
        return false;
    }

    /**
     * Method that search for if a user is banned
     *
     * @param array $p_params
     */
    public function isBannned(array $p_params)
    {
        foreach($p_params as $key => $value)
        {
            $value = array(
                'forum' => $p_forum,
                'search' => $value,
                'for_column' => $key,
                'type' => 'deny',
                'search_type' => 'normal'
            );
            if($this->matched($p_params))
                return true;
        }
        return false;
    }

    /**
     * Delete user
     *
     * @param Newscoop\Entity\Comment\Acceptance $user
     * @return void
     */
    public function delete(Acceptance $p_acceptance)
    {
        $em = $this->getEntityManager();
        $em->remove($p_acceptance);
    }

    /**
     * Flush mechanism
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }

}
