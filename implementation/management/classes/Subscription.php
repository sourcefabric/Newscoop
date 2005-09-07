<?PHP
class Subscription {
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
	 * Delete all the subscriptions to this section.
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
    }
    
} // class Subscription
?>