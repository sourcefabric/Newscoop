<?PHP
/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable 
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT'] 
// is not defined in these cases.
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}
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
    
		
	function Subscription($p_id = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['Id'] = $p_id;
		if (!is_null($p_id)) {
			$this->fetch();
		}
	} // constructor
	
	
	function delete()
	{
		global $Campsite;
		$deleted = parent::delete();
	    $queryStr = "DELETE FROM SubsSections WHERE IdSubscription=".$this->m_data['Id'];
	    $Campsite['db']->Execute($queryStr);
	    return $deleted;
	} // fn delete
	
	
	function getSubscriptionId()
	{
		return $this->getProperty('Id');
	} // fn getSubscriptionId
	
	
	function getUserId()
	{
		return $this->getProperty('IdUser');
	} // fn getUserId
	
	
	function getPublicationId()
	{
		return $this->getProperty('IdPublication');
	} // fn getPublicationId
	
	
	function getToPay()
	{
		return $this->getProperty('ToPay');
	} // fn getToPay
	
	
	function setToPay($p_value) 
	{
		global $Campsite;
		$success = $this->setProperty('ToPay', $p_value);
		if ($success && ( ($p_value == '0') || ($p_value == 0) ) ) {
			$queryStr ="UPDATE SubsSections SET PaidDays=Days WHERE IdSubscription=".$this->m_data['Id'];
			$Campsite['db']->Execute($queryStr);
		}
		return $success;
	} // fn setToPay
	
	
	function getCurrency()
	{
		return $this->getProperty('Currency');
	} // fn getCurrency
	
	
	function getType()
	{
		return $this->getProperty('Type');
	} // fn getType
	
	
	function isActive()
	{
		$active = $this->getProperty('Active');
		if ($active == 'Y') {
			return true;
		}
		else {
			return false;
		}
	} // fn isActive
	
	
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
	 * @return int
	 */
	function GetNumSubscriptions($p_publicationId = null, $p_userId = null)
	{
		global $Campsite;
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
		$total = $Campsite['db']->GetOne($queryStr);
		return $total;
	} // fn GetNumSubscriptions
	
	
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
        global $Campsite;
    	$query = "SELECT Id FROM Subscriptions WHERE IdPublication = " . $p_publicationId;
    	$subscriberIds = $Campsite['db']->GetAll($query);
    	$numSubscriberIds = count($subscriberIds);
    	if ($numSubscriberIds <= 0) {
    		return 0;
    	}
    	foreach ($subscriberIds as $id) {
    		$delQuery = "DELETE FROM SubsSections WHERE IdSubscription = " . $id
    		     . " AND SectionNumber = " . $p_sectionId;
    		
    		$Campsite['db']->Execute($delQuery);
    	}
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
        global $Campsite;
    	// Retrieve the default trial and paid time of the subscriptions
    	$query = 'SELECT TimeUnit, TrialTime, PaidTime FROM Publications WHERE Id = '
    	         . $p_publicationId;
    	$rows = $Campsite['db']->GetAll($query);
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
    	$rows = $Campsite['db']->GetAll($subs_query);
    	$subscriptionCount = 0;
    	foreach ($rows as $row) {
		    // If the subscriber is not currently subscribed to any sections
			if ($row['StartDate'] == '') {
				$startDate = 'NOW()';
				$days = $defaultDays[$row['Type']];
				$paidDays = $defaultPaidDays[$row['Type']];
			}
			else {
				$startDate = "'" . $row['StartDate'] . "'";
			}
			
			$query = 'INSERT INTO SubsSections '
			         .' SET IdSubscription = ' . $row['Id']
			         .', SectionNumber = ' . $p_sectionId 
			         .', StartDate = ' . $startDate
			         .', Days = ' . $row['Days']
			         .', PaidDays = ' . $row['PaidDays'];
			$Campsite['db']->Execute($query);
			$subscriptionCount++;    			
    	} // foreach
    	
    	return $subscriptionCount;
    } // fn AddSectionToAllSubscriptions
    
} // class Subscription
?>