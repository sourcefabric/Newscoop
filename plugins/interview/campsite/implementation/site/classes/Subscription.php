<?PHP
/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/DatabaseObject.php');

class Subscription extends DatabaseObject {
	var $m_dbTableName = 'Subscriptions';
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array(
		'Id',
		'IdUser',
		'IdPublication',
		'Active',
		'ToPay',
		'Currency',
		'Type');


	/**
	 * A user's subscription to a publication.
	 *
	 * @param int $p_id
	 * @return Subscription
	 */
	function Subscription($p_id = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['Id'] = $p_id;
		if (!is_null($p_id)) {
			$this->fetch();
		}
	} // constructor


	/**
	 * Delete this subscription and all cooresponding section subscriptions.
	 *
	 * @return boolean
	 */
	function delete()
	{
		global $g_ado_db;
		$deleted = parent::delete();
	    $queryStr = "DELETE FROM SubsSections WHERE IdSubscription=".$this->m_data['Id'];
	    $g_ado_db->Execute($queryStr);
	    return $deleted;
	} // fn delete


	/**
	 * Unique ID for this subscription.
	 *
	 * @return int
	 */
	function getSubscriptionId()
	{
		return $this->m_data['Id'];
	} // fn getSubscriptionId


	/**
	 * The user who is subscribed.
	 *
	 * @return int
	 */
	function getUserId()
	{
		return $this->m_data['IdUser'];
	} // fn getUserId


	/**
	 * The publication to which the user is subscribed.
	 *
	 * @return int
	 */
	function getPublicationId()
	{
		return $this->m_data['IdPublication'];
	} // fn getPublicationId


	/**
	 * @return float
	 */
	function getToPay()
	{
		return $this->m_data['ToPay'];
	} // fn getToPay


	/**
	 * @param float $p_value
	 * @return boolean
	 */
	function setToPay($p_value)
	{
		global $g_ado_db;
		$success = $this->setProperty('ToPay', $p_value);
		if ($success && ( ($p_value == '0') || ($p_value == 0) ) ) {
			$queryStr ="UPDATE SubsSections SET PaidDays=Days WHERE IdSubscription=".$this->m_data['Id'];
			$g_ado_db->Execute($queryStr);
		}
		return $success;
	} // fn setToPay


	/**
	 * @return string
	 */
	function getCurrency()
	{
		return $this->m_data['Currency'];
	} // fn getCurrency


	/**
	 * Returns 'T' for Trial subscription or 'P' for paid subscription.
	 *
	 * @return string
	 */
	function getType()
	{
		return $this->m_data['Type'];
	} // fn getType


	/**
	 * Return TRUE if the subscription is active.
	 *
	 * @return boolean
	 */
	function isActive()
	{
		$active = $this->m_data['Active'];
		if ($active == 'Y') {
			return true;
		} else {
			return false;
		}
	} // fn isActive


	/**
	 * Set whether the subscription is active.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
	function setIsActive($p_value)
	{
		if ($p_value) {
			return $this->setProperty('Active', 'Y');
		} else {
			return $this->setProperty('Active', 'N');
		}
	} // fn setIsActive


	/**
	 * Return the number of subscriptions in the given publication.
	 *
	 * @param int $p_publicationId
	 * @param int $p_userId
	 * @return int
	 */
	function GetNumSubscriptions($p_publicationId = null, $p_userId = null)
	{
		global $g_ado_db;
		$queryStr = "SELECT COUNT(*) FROM Subscriptions";
		$constraints = array();
		if (!is_null($p_publicationId)) {
			$constraints[] = array('IdPublication', $p_publicationId);
		}
		if (!is_null($p_userId)) {
			$constraints[] = array('IdUser', $p_userId);
		}
		if (count($constraints) > 0) {
			$tmpArray = array();
			foreach ($constraints as $constraint) {
				$tmpArray[] = $constraint[0]."='".$constraint[1]."'";
			}
			$queryStr .= " WHERE ".implode(" AND ", $tmpArray);
		}
		$total = $g_ado_db->GetOne($queryStr);
		return $total;
	} // fn GetNumSubscriptions


	/**
	 * Fetch the subscription objects that match the search criteria.
	 *
	 * @param int $p_publicationId
	 * @param int $p_userId
	 * @param array $p_sqlOptions
	 * @return array
	 */
	function GetSubscriptions($p_publicationId = null, $p_userId = null, $p_sqlOptions = null)
	{
		$constraints = array();
		if (!is_null($p_publicationId)) {
			$constraints[] = array('IdPublication', $p_publicationId);
		}
		if (!is_null($p_userId)) {
			$constraints[] = array('IdUser', $p_userId);
		}
		return DatabaseObject::Search('Subscription', $constraints, $p_sqlOptions);
	} // fn GetSubscriptions


	/**
	 * Delete all the subscriptions to this section.
	 *
	 * @param int $p_publicationId
	 * @param int $p_sectionId
	 * @return int
	 *     The number of subscriptions deleted.
	 */
    function DeleteSubscriptionsInSection($p_publicationId, $p_sectionId)
    {
        global $g_ado_db;
    	$query = "SELECT Id FROM Subscriptions WHERE IdPublication = " . $p_publicationId;
    	$subscriberIds = $g_ado_db->GetCol($query);
    	$numSubscriberIds = count($subscriberIds);
    	if ($numSubscriberIds <= 0) {
    		return 0;
    	}
		$delQuery = "DELETE FROM SubsSections "
					." WHERE IdSubscription IN (".implode(",", $subscriberIds).")"
		     		." AND SectionNumber = " . $p_sectionId;
		$g_ado_db->Execute($delQuery);
    	return $numSubscriberIds;
    } // fn DeleteSubscriptionsInSection


    /**
     * Add the given section to all subscriptions.  Return the number of
     * subscriptions affected.
     *
     * @param int $p_publicationId
     * @param int $p_sectionId
     * @return int
     */
    function AddSectionToAllSubscriptions($p_publicationId, $p_sectionId)
    {
        global $g_ado_db;
    	// Retrieve the default trial and paid time of the subscriptions
    	$query = 'SELECT TimeUnit, TrialTime, PaidTime FROM Publications WHERE Id = '
    	         . $p_publicationId;
    	$rows = $g_ado_db->GetAll($query);
    	if (count($rows) < 0) {
    		return -1;
    	}
    	if (count($rows) == 0) {
    		return 0;
    	}
    	$row = $rows[0];

    	// Convert the time into a number of days
    	switch ($row['TimeUnit']){
    	case 'D':
    		$trialDays = $row['TrialTime'];
    		$paidDays = $row['PaidTime'];
    		break;
    	case 'W':
    		$trialDays = $row['TrialTime'] * 7;
    		$paidDays = $row['PaidTime'] * 7;
    		break;
    	case 'M':
    		$trialDays = $row['TrialTime'] * 30;
    		$paidDays = $row['PaidTime'] * 30;
    		break;
    	case 'Y':
    		$trialDays = $row['TrialTime'] * 365;
    		$paidDays = $row['PaidTime'] * 365;
    		break;
    	}

    	$defaultDays['T'] = $defaultPaidDays['T'] = $trialDays;
    	$defaultDays['P'] = $defaultPaidDays['P'] = $paidDays;

    	// Read active subscriptions for the given publication
    	$subs_query = 'SELECT Subscriptions.Id, Subscriptions.Type, SubsSections.StartDate,'
    	     .' SubsSections.Days, SubsSections.PaidDays '
    	     .' FROM Subscriptions '
    	     .' LEFT JOIN SubsSections ON Subscriptions.Id = SubsSections.IdSubscription '
    	     .' WHERE Subscriptions.IdPublication = ' . $p_publicationId
    	     .' AND Subscriptions.Active = \'Y\' '
    	     .' GROUP BY SubsSections.IdSubscription'
    	     .' ORDER BY Subscriptions.Id ASC';
    	$rows = $g_ado_db->GetAll($subs_query);
    	$subscriptionCount = 0;
    	foreach ($rows as $row) {
		    // If the subscriber is not currently subscribed to any sections
			if ($row['StartDate'] == '') {
				$startDate = 'NOW()';
				$days = $defaultDays[$row['Type']];
				$paidDays = $defaultPaidDays[$row['Type']];
			} else {
				$startDate = "'" . $row['StartDate'] . "'";
			}

			$query = 'INSERT INTO SubsSections '
			         .' SET IdSubscription = ' . $row['Id']
			         .', SectionNumber = ' . $p_sectionId
			         .', StartDate = ' . $startDate
			         .', Days = ' . $row['Days']
			         .', PaidDays = ' . $row['PaidDays'];
			$g_ado_db->Execute($query);
			$subscriptionCount++;
    	} // foreach

    	return $subscriptionCount;
    } // fn AddSectionToAllSubscriptions

} // class Subscription
?>