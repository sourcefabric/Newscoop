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
require_once($g_documentRoot.'/classes/DbObjectArray.php');
require_once($g_documentRoot.'/classes/Publication.php');

class SubscriptionDefaultTime extends DatabaseObject
{
	var $m_dbTableName = 'SubsDefTime';
	var $m_keyColumnNames = array('CountryCode', 'IdPublication');
	var $m_columnNames = array(
		'CountryCode',
		'IdPublication',
		'TrialTime',
		'PaidTime'
		);

  	public function SubscriptionDefaultTime($p_countryCode = null, $p_publicationId = null)
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
  	public function create($p_values = null)
  	{
  		$success = parent::create($p_values);
  		$publicationObj = new Publication($this->m_data['IdPublication']);
		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}
		$logtext = getGS('The default subscription time for $1 has been added.',
						 "(".getGS("Publication")." ".$publicationObj->getName()
						 .':'.$this->m_data['CountryCode'].")");
		Log::Message($logtext, null, 4);
		return $success;
  	} // fn create


  	/**
  	 * @return string
  	 */
  	public function getCountryCode()
  	{
  		return $this->m_data['CountryCode'];
  	} // fn getCountryCode


  	/**
  	 * @return int
  	 */
  	public function getPublicationId()
  	{
  		return $this->m_data['IdPublication'];
  	} // fn getPublicationId


  	/**
  	 * @return int
  	 */
  	public function getTrialTime()
  	{
  		return $this->m_data['TrialTime'];
  	} // fn getTrialTime


  	/**
  	 * @param int $p_value
  	 */
  	public function setTrialTime($p_value)
  	{
  		return $this->setProperty('TrialTime', $p_value);
  	} // fn setTrialTime


  	/**
  	 * @return int
  	 */
  	public function getPaidTime()
  	{
  		return $this->m_data['PaidTime'];
  	} // fn getPaidTime


  	/**
  	 * @param int $p_value
  	 */
  	public function setPaidTime($p_value)
  	{
  		return $this->setProperty('PaidTime', $p_value);
  	} // fn setPaidTime


  	public static function GetSubscriptionDefaultTimes($p_countryCode = null,
  	                                                   $p_publicationId = null)
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