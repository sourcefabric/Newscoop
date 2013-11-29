<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\MailChimp;

use Newscoop\NewscoopBundle\Services\SystemPreferencesService;

/**
 */
class ListApi
{
    /**
     * @var MCAPI
     */
    private $api;

    /**
     * @var string
     */
    private $listId;

    /**
     * @param MCAPI                    $api
     * @param SystemPreferencesService $service
     */
    public function __construct(ApiFactory $apiFactory, SystemPreferencesService $service)
    {
        $this->api = $apiFactory->createApi();
        $this->listId = $service->get('mailchimp_listid');
    }

    /**
     * Get list view
     *
     * @return ListView
     */
    public function getListView()
    {
        $view = new ListView();
        if (empty($this->listId)) {
            return $view;
        }

        $view->groups = $this->api->listInterestGroupings($this->listId);
        if ($view->groups) {
            $view->id = $this->listId;
            foreach ($view->groups as $i => $group) {
                $names = array_map(function ($group) { 
                    return $group['name'];
                }, $group['groups']);
                $view->groups[$i]['groups'] = array_combine($names, $names);
            }
        } else {
            $view->groups = array();
        }

        return $view;
    }

    /**
     * Get member view for given email
     *
     * @param string $email
     * @return MemberView
     */
    public function getMemberView($email)
    {
        $view = new MemberView();
        if (empty($this->listId)) {
            return $view;
        }

        $info = $this->api->listMemberInfo($this->listId, (array) $email);
        if ($info['success']) {
            $data = $info['data'][0];
            $view->email = $data['email'];
            $view->subscriber = $data['status'] !== 'unsubscribed';
            if ($view->subscriber) {
                foreach ($data['merges']['GROUPINGS'] as $group) {
                    $view->groups[$group['name']] = array_map('trim', explode(',', $group['groups']));
                }
            }
        }

        return $view;
    }

    /**
     * Subscribe email to list
     *
     * @param string $email
     * @param array $groups
     * @return void
     */
    public function subscribe($email, array $values)
    {
        if (empty($this->listId)) {
            return;
        }

        if (empty($values['subscriber'])) {
            return $this->api->listUnsubscribe($this->listId, $email);
        } else {
            unset($values['subscriber']);
        }

        $groupings = array();
        foreach ($values as $name => $groups) {
            if (empty($groups)) {
                continue;
            }

            $groupings[] = array(
                'name' => $name,
                'groups' => is_array($groups) ? implode(',', $groups) : $groups,
            );
        }

        return $this->api->listSubscribe($this->listId, $email, array(
            'GROUPINGS' => $groupings,
        ), 'html', false, true, true, true);
    }
}