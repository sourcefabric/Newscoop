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
require_once($g_documentRoot.'/classes/DbObjectArray.php');
require_once($g_documentRoot.'/classes/Publication.php');

class SubscriptionDefaultTime extends DatabaseObject {
	var $m_dbTableName = 'SubsDefTime';
	var $m_keyColumnNames = array('CountryCode', 'IdPublication');
	var $m_columnNames = array(
		'CountryCode',
		'IdPublication',
		'TrialTime',
		'PaidTime'
		);
    
  	function SubscriptionDefaultTime($p_countryCode = null, $p_publicationId = null)
  	{
  		parent::DatabaseObject($this->m_columnNames);
  		$this->m_data['CountryCode'] = $p_countryCode;
  		$this->m_data['IdPublication'] = $p_publicationId;
  		if ($this->keyValuesExist()) {
  			$this->fetch();
  		}
  	} // constructor
  	
  	
  	/**
  	 * @param array $p_values
  	 * @return boolean
  	 */
  	function create($p_values = null)
  	{
  		$success = parent::create($p_values);
  		$publicationObj =& new Publication($this->m_data['IdPublication']);
		if (function_exists("camp_load_language")) { camp_load_language("api");	}
		$logtext = getGS('The default subscription time for $1 has been added.', 
						 "(".getGS("Publication")." ".$publicationObj->getName() 
						 .':'.$this->m_data['CountryCode'].")"); 
		Log::Message($logtext, null, 4);  		
		return $success;
  	} // fn create
  	
  	
  	/**
  	 * @return string
  	 */
  	function getCountryCode()
  	{
  		return $this->getProperty('CountryCode');
  	} // fn getCountryCode
  	
  	
  	/**
  	 * @return int
  	 */
  	function getPublicationId()
  	{
  		return $this->getProperty('IdPublication');
  	} // fn getPublicationId
  	
  	
  	/**
  	 * @return int
  	 */
  	function getTrialTime()
  	{
  		return $this->getProperty('TrialTime');
  	} // fn getTrialTime
  	
  	
  	/**
  	 * @param int $p_value
  	 */
  	function setTrialTime($p_value)
  	{
  		return $this->setProperty('TrialTime', $p_value);
  	} // fn setTrialTime
  	
  	
  	/**
  	 * @return int
  	 */
  	function getPaidTime()
  	{
  		return $this->getProperty('PaidTime');
  	} // fn getPaidTime
  	
  	
  	/**
  	 * @param int $p_value
  	 */
  	function setPaidTime($p_value)
  	{
  		return $this->setProperty('PaidTime', $p_value);
  	} // fn setPaidTime
  	
  	
  	function GetSubscriptionDefaultTimes($p_countryCode = null, $p_publicationId = null)
  	{
  		$constraints = array();
  		if (!is_null($p_countryCode)) {
  			$constraints[] = array('CountryCode', $p_countryCode);
  		}
  		if (!is_null($p_publicationId)) {
  			$constraints[] = array('IdPublication', $p_publicationId);
  		}
  		return DatabaseObject::Search('SubscriptionDefaultTime', $constraints);
  	}
  	
  	
} // class SubscriptionDefaultTime
?>