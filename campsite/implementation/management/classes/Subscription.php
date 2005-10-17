<?PHP
class Subscription extends DatabaseObject {
	var $m_dbTableName = 'Subscriptions';
	var $m_keyColumnNames = array('Id');
	var $m_columnNames = array(
		'Id',
		'IdUser',
		'IdPublication',
		'Active',
		'ToPay',
		'Currency',
		'Type');
    
	/**
	 * Return the number of subscriptions in the given publication.
	 *
	 * @param int $p_publicationId
	 * @return int
	 */
	function GetNumSubscriptions($p_publicationId = null)
	{
		global $Campsite;
		$queryStr = "SELECT COUNT(*) FROM Subscriptions WHERE IdPublication=$p_publicationId";
		$count = $Campsite['db']->GetOne($queryStr);
		return $count;
	} // fn GetNumSubscriptions
	
	
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