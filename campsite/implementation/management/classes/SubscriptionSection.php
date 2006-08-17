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
	var $m_keyColumnNames = array('IdSubscription', 'SectionNumber', 'IdLanguage');
	var $m_columnNames = array(
		'IdSubscription',
		'SectionNumber',
		'IdLanguage',
		'StartDate',
		'Days',
		'PaidDays',
		'NoticeSent');


	/**
	 * Subscribers can be subscribed to individual sections of a publication.
	 *
	 * @param int $p_subscriptionId
	 * @param int $p_sectionNumber
	 * @param int $p_languageId
	 * @return SubscriptionSection
	 */
	function SubscriptionSection($p_subscriptionId = null, $p_sectionNumber = null, $p_languageId = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['IdSubscription'] = $p_subscriptionId;
		$this->m_data['SectionNumber'] = $p_sectionNumber;
		$this->m_data['IdLanguage'] = $p_languageId;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor


	/**
	 * @return int
	 */
	function getSubscriptionId()
	{
		return $this->m_data['IdSubscription'];
	} // fn getSubscriptionId


	/**
	 * @return int
	 */
	function getSectionNumber()
	{
		return $this->m_data['SectionNumber'];
	} // fn getSectionNumber


	/**
	 * @return int
	 */
	function getLanguageId()
	{
		return $this->m_data['IdLanguage'];
	} // fn getSubscriptionId


	/**
	 * Return the starting date in the form YYYY-MM-DD.
	 *
	 * @return string
	 */
	function getStartDate()
	{
		return $this->m_data['StartDate'];
	} // fn getStartDate


	/**
	 * Set the start date, must be in the form YYYY-MM-DD.
	 *
	 * @param string $p_value
	 * @return boolean
	 */
	function setStartDate($p_value)
	{
		return $this->setProperty('StartDate', $p_value);
	} // fn setStartDate


	/**
	 * The length of the subscription in days.
	 *
	 * @return int
	 */
	function getDays()
	{
		return $this->m_data['Days'];
	} // fn getDays


	/**
	 * Set length of the subscription in days.
	 *
	 * @param int $p_value
	 * @return boolean
	 */
	function setDays($p_value)
	{
		return $this->setProperty('Days', $p_value);
	}


	/**
	 * @return int
	 */
	function getPaidDays()
	{
		return $this->m_data['PaidDays'];
	} // fn getPaidDays


	/**
	 * @param int $p_value
	 * @return boolean
	 */
	function setPaidDays($p_value)
	{
		return $this->setProperty('PaidDays', $p_value);
	} // fn setPaidDays


	/**
	 * @return boolean
	 */
	function noticeSent()
	{
		$sent = $this->m_data['NoticeSent'];
		if ($sent == 'Y') {
			return true;
		} else {
			return false;
		}
	} // fn noticeSent


	/**
	 *
	 * @param int $p_subscriptionId
	 * @param int $p_publicationId
	 * @param int $p_languageId
	 * @param array $p_values
	 * @return boolean
	 */
	function AddSubscriberToPublication($p_subscriptionId, $p_publicationId, $p_languageId, $p_values = null)
	{
		global $g_ado_db;
		$created = true;
		$queryStr = "SELECT DISTINCT Number FROM Sections where IdPublication=$p_publicationId";
		$sectionIds = $g_ado_db->GetCol($queryStr);
		foreach ($sectionIds as $sectionId) {
			$subscriptionSection =& new SubscriptionSection($p_subscriptionId, $sectionId, $p_languageId);
			$created &= $subscriptionSection->create($p_values);
		}
		return $created;
	} // fn AddSubscriberToPublication


	/**
	 * Return an array of SubscriptionSection objects matching the
	 * search criteria.
	 *
	 * @param int $p_subscriptionId
	 * @param int $p_sectionId
	 * @param int $p_languageId
	 * @return array
	 */
	function GetSubscriptionSections($p_subscriptionId, $p_sectionId = null, $p_languageId = null)
	{
		$queryStr = "SELECT SubsSections.*, Sections.Name, Subscriptions.Type, "
			."Languages.Name as LangName, Languages.OrigName as LangOrigName"
			." FROM Subscriptions, Sections, SubsSections LEFT JOIN Languages "
			." ON SubsSections.IdLanguage = Languages.Id "
			." WHERE Subscriptions.Id = $p_subscriptionId "
			." AND Subscriptions.Id = SubsSections.IdSubscription "
			." AND Subscriptions.IdPublication = Sections.IdPublication "
			." AND SubsSections.SectionNumber = Sections.Number ";
		if (!is_null($p_sectionId)) {
			$queryStr .= " AND SubsSections.SectionNumber = $p_sectionId";
		}
		if (!is_null($p_languageId)) {
			$queryStr .= " AND SubsSections.IdLanguage = $p_languageId";
		}
		$queryStr .= " GROUP BY SectionNumber, IdLanguage ORDER BY SectionNumber, LangName";
		$sections = DbObjectArray::Create('SubscriptionSection', $queryStr);
		return $sections;
	} // fn GetSubscriptionSections


	/**
	 * Return the number of sections matching the search criteria.
	 *
	 * @param int $p_subscriptionId
	 * @param int $p_sectionId
	 * @param int $p_languageId
	 * @return int
	 */
	function GetNumSections($p_subscriptionId, $p_sectionId = null, $p_languageId = null)
	{
		global $g_ado_db;
		$queryStr = "SELECT count(*) FROM SubsSections WHERE IdSubscription = $p_subscriptionId";
		if (!is_null($p_sectionId)) {
			if (is_array($p_sectionId)) {
				$queryStr .= " AND SectionNumber IN (" . implode(", ", $p_sectionId) . ")";
			} else {
				$queryStr .= " AND SectionNumber = $p_sectionId";
			}
		}
		if (!is_null($p_languageId)) {
			if (is_array($p_languageId)) {
				$queryStr .= " AND IdLanguage IN (" . implode(", ", $p_languageId) . ")";
			} else {
				$queryStr .= " AND IdLanguage = $p_languageId";
			}
		} else {
			$queryStr .= " AND IdLanguage != 0";
		}
		$total = $g_ado_db->GetOne($queryStr);
		return $total;
	} // fn GetNumSections

} // class SubscriptionSection
?>