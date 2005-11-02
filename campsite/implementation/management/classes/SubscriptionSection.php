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

class SubscriptionSection extends DatabaseObject {
	var $m_dbTableName = 'SubsSections';
	var $m_keyColumnNames = array('IdSubscription', 'SectionNumber');
	var $m_columnNames = array(
		'IdSubscription',
		'SectionNumber',
		'StartDate',
		'Days',
		'PaidDays',
		'NoticeSent');
    
		
	function SubscriptionSection($p_subscriptionId = null, $p_sectionNumber = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['IdSubscription'] = $p_subscriptionId;
		$this->m_data['SectionNumber'] = $p_sectionNumber;
		if ($this->keyValuesExist()) {
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
		return $this->getProperty('IdSubscription');
	} // fn getSubscriptionId
	
	
	function getSectionNumber()
	{
		return $this->getProperty('SectionNumber');
	} // fn getSectionNumber
	
	
	function getStartDate()
	{
		return $this->getProperty('StartDate');
	} // fn getStartDate
	
	
	function setStartDate($p_value)
	{
		return $this->setProperty('StartDate', $p_value);
	}
	
	
	function getDays()
	{
		return $this->getProperty('Days');
	} // fn getDays
	
	
	function setDays($p_value)
	{
		return $this->setProperty('Days', $p_value);
	}
	
	
	function getPaidDays()
	{
		return $this->getProperty('PaidDays');
	} // fn getPaidDays
	
	
	function setPaidDays($p_value)
	{
		return $this->setProperty('PaidDays', $p_value);
	}
	
	
	/**
	 * @return boolean
	 */
	function noticeSent()
	{
		$sent = $this->getProperty('NoticeSent');
		if ($sent == 'Y') {
			return true;
		}
		else {
			return false;
		}
	} // fn noticeSent
	
	
	/**
	 *
	 * @param int $p_subscriptionId
	 * @param int $p_publicationId
	 * @param array $p_values
	 * @return boolean
	 */
	function AddSubscriberToPublication($p_subscriptionId, $p_publicationId, $p_values = null)
	{
		global $Campsite;
		$created = true;
		$queryStr = "SELECT DISTINCT Number FROM Sections where IdPublication=$p_publicationId";
		$sectionIds = $Campsite['db']->GetCol($queryStr);
		foreach ($sectionIds as $sectionId) {
			$subscriptionSection =& new SubscriptionSection($p_subscriptionId, $sectionId);
			$created &= $subscriptionSection->create($p_values);
		}
		return $created;
	}
	
	
	function GetSubscriptionSections($p_subscriptionId, $p_sectionId = null)
	{
		$queryStr = "SELECT DISTINCT SubsSections.*, Sections.Name, Subscriptions.Type "
			." FROM SubsSections, Sections, Subscriptions "
			." WHERE SubsSections.IdSubscription=$p_subscriptionId "
			." AND Subscriptions.Id = $p_subscriptionId"
			." AND Subscriptions.IdPublication = Sections.IdPublication "
			." AND SubsSections.SectionNumber = Sections.Number ";
		if (!is_null($p_sectionId)) {
			$queryStr .= " AND SubsSections.SectionNumber = $p_sectionId";
		}
		$queryStr .= " ORDER BY SectionNumber";
		$sections = DbObjectArray::Create('SubscriptionSection', $queryStr);
		return $sections;
	}
	
} // class SubscriptionSection
?>