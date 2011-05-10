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
    Newscoop\Entity\Comment\Commenter,
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

        if(is_numeric($p_values['forum']))
        {
            $publicationRepository = $em->getRepository('Newscoop\Entity\Publication');
            $forum = $publicationRepository->find($p_values['forum']);
        }
        else
            $forum = $p_values['forum'];
        $p_acceptance->setSearch($p_values['search'])
                     ->setSearchType($p_values['search_type'])
                     ->setType($p_values['type'])
                     ->setForColumn($p_values['for_column']);
        if(!is_null($forum)) {
            $p_acceptance->setForum($forum);
        }
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
            $value = $this->setEntity($value);
            $acceptance = $this->findBy( $value );
            if(count($acceptance)>0)
                $this->delete($acceptance[0]);
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
            if($this->matched($value))
                return;
            $this->save(new Acceptance, $value);

        }
    }

    /**
     * Get the setters for the entity
     *
     * @param array $p_params
     * @return array $p_params
     */
    private function setEntity(array $p_params)
    {
        // properies don't go to setters or getters
        if(!is_numeric($p_params['for_column']))
        {
            $rev = array_flip(Acceptance::$for_column_enum);
            $p_params['for_column'] = $rev[$p_params['for_column']];
        }
        if(!is_numeric($p_params['type']))
        {
            $rev = array_flip(Acceptance::$type_enum);
            $p_params['type'] = $rev[$p_params['type']];
        }
        if(!is_numeric($p_params['search_type']))
        {
            $rev = array_flip(Acceptance::$search_type_enum);
            $p_params['search_type'] = $rev[$p_params['search_type']];
        }
        if(!is_numeric($p_params['forum']) && !is_null($p_params['forum']))
        {
            $p_params['forum'] = $p_params['forum']->getId();
        }
        return $p_params;
    }

    public function matched(array $p_params)
    {
        // this is for simple match if all search_type are normal
        // @todo implement later regex
        $p_params = $this->setEntity($p_params);
        $results = $this->findBy( $p_params );
        if(count($results)>0)
            return true;
        return false;
    }

    /**
     * Method that search for if a params are banned
     *
     * @param $p_name
     * @param $p_email
     * @param $p_ip
     * @param $p_forum
     * @return bool
     */
    public function checkParamsBanned($p_name, $p_email, $p_ip, $p_forum)
    {
        $params = array( 'name' => $p_name, 'email' => $p_email, 'ip' => $p_ip);
        $return = $this->checkBanned($params, $p_forum);
        if (!empty($return['name']) || !empty($return['email']) || !empty($return['ip']))
            return true;
        else
            return false;
    }

    /**
     * Method that search for if a commenter is banned
     *
     * @param $p_commenter
     * @param $p_forum
     * @return array
     */
    public function isBanned($p_commenter, $p_forum)
    {
        $params = array(
            'name' => $p_commenter->getName(),
            'email' => $p_commenter->getEmail(),
            'ip' => $p_commenter->getIp()
        );
        return $this->checkBanned($params, $p_forum);
    }

    /**
     * Method that checks for if a commenter is banned
     *
     * @param mixed $p_params
     */
    public function checkBanned($p_params, $p_forum)
    {
            $return = array();
            $name = array(
                'forum' => $p_forum,
                'search' => $p_params['name'],
                'for_column' => 'name',
                'type' => 'deny',
                'search_type' => 'normal'
            );
            $return['name'] = $this->matched($name);

            $email = array(
                'forum' => $p_forum,
                'search' => $p_params['email'],
                'for_column' => 'email',
                'type' => 'deny',
                'search_type' => 'normal'
            );
            $return['email'] = $this->matched($email);

            $ip = array(
                'forum' => $p_forum,
                'search' => $p_params['ip'],
                'for_column' => 'ip',
                'type' => 'deny',
                'search_type' => 'normal'
            );
            $return['ip'] = $this->matched($ip);

            return $return;
    }

    /**
     * Method that save banned for a commenter
     *
     * @param Commenter $p_commenter
     */
    public function saveBanned(Commenter $p_commenter, $p_forum, $p_values)
    {
        $unban = array();
        $ban = array();
        if($p_values['name'])
            $ban['name'] = $p_commenter->getName();
        else
            $unban['name'] = $p_commenter->getName();

        if($p_values['email'])
            $ban['email'] = $p_commenter->getEmail();
        else
            $unban['email'] = $p_commenter->getEmail();

        if($p_values['ip'])
            $ban['ip'] = $p_commenter->getIp();
        else
            $unban['ip'] = $p_commenter->getIp();

        $this->ban($p_forum, $ban);
        $this->unban($p_forum, $unban);
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
