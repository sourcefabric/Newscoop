<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\MailChimp;

use Newscoop\Entity\User;
use MCAPI;

/**
 */
class MailChimpFacade
{
    /**
     * @var MCAPI
     */
    private $api;

    /**
     * @param MCAPI $api
     */
    public function __construct(MCAPI $api)
    {
        $this->api = $api;
    }

    /**
     * Test if user is subscribed to list
     *
     * @param Newscoop\Entity\User $user
     * @param string $listId
     * @return bool
     */
    public function isSubscribed(User $user, $listId)
    {
        return in_array($listId, $this->getLists($user));
    }

    /**
     * Subscribe user to list
     *
     * @param Newscoop\Entity\User $user
     * @param string $listId
     * @param array $groups
     * @return void
     */
    public function subscribe(User $user, $listId, array $groups = array())
    {
        $groupings = array();
        foreach ($groups as $id => $names) {
            $groupings[] = array(
                'id' => $id,
                'groups' => !empty($names) ? implode(',', $names) : '',
            );
        }

        return $this->api->listSubscribe($listId, $user->getEmail(), array(
            'GROUPINGS' => $groupings,
        ), 'html', false, true, true, true);
    }

    /**
     * Unsubscribe user from list
     *
     * @param Newscoop\Entity\User $user
     * @param string $listId
     * @return void
     */
    public function unsubscribe(User $user, $listId)
    {
        return $this->api->listUnsubscribe($listId, $user->getEmail());
    }

    /**
     * Get lists user is subscribed to
     *
     * @param Newscoop\Entity\User $user
     * @return array
     */
    private function getLists(User $user)
    {
        $lists = $this->api->listsForEmail($user->getEmail());
        return $lists ?: array();
    }

    /**
     * Get groups for given list id
     *
     * @param string $listId
     * @return array
     */
    public function getListGroups($listId)
    {
        return $this->api->listInterestGroupings($listId);
    }

    /**
     * Get groups for given user and list
     *
     * @param Newscoop\Entity\User $user
     * @param string $listId
     * @return array
     */
    public function getUserGroups(User $user, $listId)
    {
        $info = $this->api->listMemberInfo($listId, $user->getEmail());
        if (!$info['success']) {
            return array();
        }

        $groups = array();
        foreach ($info['data'] as $userinfo) {
            foreach ($userinfo['merges']['GROUPINGS'] as $grouping) {
                $groups[$grouping['id']] = explode(',', $grouping['groups']);
            }
        }

        return $groups;
    }
}
