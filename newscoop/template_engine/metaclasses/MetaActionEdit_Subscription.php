<?php

require_once('Date.php');

define('ACTION_EDIT_SUBSCRIPTION_ERR_INTERNAL', 'action_edit_subscription_err_internal');
define('ACTION_EDIT_SUBSCRIPTION_ERR_NO_USER', 'action_edit_subscription_err_no_user');
define('ACTION_EDIT_SUBSCRIPTION_ERR_NO_TYPE', 'action_edit_subscription_err_no_type');
define('ACTION_EDIT_SUBSCRIPTION_ERR_NO_LANGUAGE', 'action_edit_subscription_err_no_language');
define('ACTION_EDIT_SUBSCRIPTION_ERR_NO_SECTION', 'action_edit_subscription_err_no_section');


class MetaActionEdit_Subscription extends MetaAction
{
    private $m_sections = null;

    private $m_languages = null;

    private $m_subscriptionType = null;


    /**
     * Reads the input parameters and sets up the subscription edit action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_defined = true;
        $this->m_name = 'edit_subscription';
        $this->m_properties = array();

        if (!isset($p_input['SubsType'])
        || (strtolower($p_input['SubsType']) != 'trial'
        && strtolower($p_input['SubsType']) != 'paid')) {
            $this->m_error = new PEAR_Error("Invalid subscription type.",
            ACTION_EDIT_SUBSCRIPTION_ERR_NO_TYPE);
            return;
        }
        $this->m_subscriptionType = strtolower($p_input['SubsType']);
        $this->m_properties['is_trial'] = $this->m_subscriptionType == 'trial';
        $this->m_properties['is_paid'] = $this->m_subscriptionType == 'paid';

        if (!isset($p_input['subs_all_languages'])) {
            $this->m_languages = $p_input['subscription_language'];
            if (is_null($this->m_languages) || count($this->m_languages) == 0) {
                $this->m_error = new PEAR_Error("You must select a subscription language or check all languages.",
                ACTION_EDIT_SUBSCRIPTION_ERR_NO_LANGUAGE);
                return;
            }
        } else {
            $this->m_languages = array(0);
        }

        $this->m_properties['subs_by_type'] = 'publication';

        if (isset($p_input['subs_by_type'])
        && strtolower($p_input['subs_by_type']) == 'by_section') {
        	$this->m_properties[''] = 'section';
            $this->m_sections = $p_input['cb_subs'];
            if (is_null($this->m_sections) || count($this->m_sections) == 0) {
            	$this->m_error = new PEAR_Error("You must select at least a section to subscribe to.");
            	return;
            }
        }

        $this->m_error = null;
    }


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {
        $p_context->default_url->reset_parameter('f_'.$this->m_name);
        $p_context->url->reset_parameter('f_'.$this->m_name);

        if (PEAR::isError($this->m_error)) {
            return false;
        }

        $auth = Zend_Auth::getInstance();
        $user = new User($p_context->user->identifier);
        if ($user->getUserId() != $auth->getIdentity()
        || $user->getUserId() == 0) {
            $this->m_error = new PEAR_Error('You must be logged in to create or edit your subscription.',
            ACTION_EDIT_SUBSCRIPTION_ERR_NO_USER);
            return false;
        }

        $subscriptions = Subscription::GetSubscriptions($p_context->publication->identifier,
        $user->getUserId());
        if (count($subscriptions) == 0) {
            $subscription = new Subscription();
            $created = $subscription->create(array(
			'IdUser' => $user->getUserId(),
			'IdPublication' => $p_context->publication->identifier,
			'Active' => 'Y',
			'Type' => $this->m_subscriptionType == 'trial' ? 'T' : 'P'));
            if (!$created) {
                $this->m_error = new PEAR_Error('Internal error (code 1)',
                ACTION_EDIT_SUBSCRIPTION_ERR_INTERNAL);
                exit(1);
            }
        } else {
            $subscription = $subscriptions[0];
        }

        $publication = new Publication($p_context->publication->identifier);
        $subscriptionDays = $this->computeSubscriptionDays($publication,
        $p_context->publication->subscription_time);

        $startDate = new Date();

        $columns = array(
        'StartDate'=>$startDate->getDate(),
        'Days'=>$subscriptionDays,
        'PaidDays'=>($this->m_subscriptionType == 'trial' ? $subscriptionDays : 0),
        'NoticeSent'=>'N'
        );

        if ($this->m_properties['subs_by_type'] == 'publication') {
        	$sectionsList = Section::GetUniqueSections($p_context->publication->identifier);
        	foreach ($sectionsList as $section) {
        		$this->m_sections[] = $section['id'];
        	}
        }
        foreach ($this->m_languages as $languageId) {
            foreach ($this->m_sections as $sectionNumber) {
                $subsSection = new SubscriptionSection($subscription->getSubscriptionId(),
                $sectionNumber, $languageId);
                $subsSection->create($columns);
            }
        }

        $fields = array('SubsType', 'tx_subs', 'nos', 'unitcost', 'unitcostalllang',
        'f_substype', 'cb_subs', 'subs_all_languages', 'suma', 'tpl', 'subscription_language');
        foreach (CampRequest::GetInput() as $field=>$value) {
            if (strncmp('tx_subs', $field, strlen('tx_subs')) == 0) {
                $fields[] = $field;
            }
        }
        foreach ($fields as $fieldName) {
            $p_context->default_url->reset_parameter($fieldName);
            $p_context->url->reset_parameter($fieldName);
        }

        $this->m_error = ACTION_OK;
        return true;
    }


    private function computeSubscriptionDays($p_publication, $p_subscriptionTime) {
        $startDate = new Date();
        if ($p_publication->getTimeUnit() == 'D') {
            return $p_subscriptionTime;
        } elseif ($p_publication->getTimeUnit() == 'W') {
            return 7 * $p_subscriptionTime;
        } elseif ($p_publication->getTimeUnit() == 'M') {
            $endDate = new Date();
            $months = $p_subscriptionTime + $endDate->getMonth();
            $years = (int)($months / 12);
            $months = $months % 12;
            $endDate->setYear($endDate->getYear() + $years);
            $endDate->setMonth($months);
        } elseif ($p_publication->getTimeUnit() == 'Y') {
            $endDate = new Date();
            $endDate->setYear($endDate->getYear() + $p_subscriptionTime);
        }
        $dateCalc = new Date_Calc();
        return $dateCalc->dateDiff($endDate->getDay(), $endDate->getMonth(),
        $endDate->getYear(), $startDate->getDay(), $startDate->getMonth(), $startDate->getYear());
    }
}

?>
