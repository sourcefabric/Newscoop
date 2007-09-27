<?php
/**
 * @package Campsite
 */

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
require_once($g_documentRoot.'/classes/Language.php');
require_once($g_documentRoot.'/classes/Section.php');

/**
 * @package Campsite
 */
class IPAccess extends DatabaseObject {
	var $m_dbTableName = 'SubsByIP';
	var $m_keyColumnNames = array('IdUser', 'StartIP');
	var $m_columnNames = array(
		'IdUser',
		'StartIP',
		'Addresses');

	function __string2array($p_IPaddress)
	{
		$IPaddressArray = array();
		$IPaddressArray[] = strtok($p_IPaddress, '.');
		for ($i = 1; $i < 4; $i++) {
			$IPaddressArray[] = strtok('.');
		}
		return $IPaddressArray;
	}

	function __array2int($p_IPAddressArray)
	{
		if (!is_array($p_IPAddressArray) || sizeof($p_IPAddressArray) < 4) {
			return null;
		}
		$IPAddress = 0;
		for ($i = 0; $i < 4; $i++) {
			$IPAddress += $p_IPAddressArray[$i] * pow(256, 3-$i);
		}
		return $IPAddress;
	}

	function __int2array($p_IPAddress)
	{
		$IPAddressArray = array();
		for ($i = 3; $i >= 0; $i--) {
			$IPAddressArray[] = (int)($p_IPAddress / pow(256, $i));
			$p_IPAddress -= $IPAddressArray[3-$i] * pow(256, $i);
		}
		return $IPAddressArray;
	}

	function __array2string($p_IPAddressArray)
	{
		if (!is_array($p_IPAddressArray) || sizeof($p_IPAddressArray) < 4) {
			return null;
		}
		$IPAddress = '';
		for ($i = 0; $i < 4; $i++) {
			$IPAddress = "$IPAddress.".$p_IPAddressArray[$i];
		}
		$IPAddress = substr($IPAddress, 1);
		return $IPAddress;
	}

	/**
	 * IP based access has a start address and a number of addresses.
	 * The IP access group is assigned to a reader.
	 * @param int $p_userId
	 * @param int $p_startIP
	 * @param int $p_addresses
	 */
	function IPAccess($p_userId = null, $p_startIP = null, $p_addresses = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['IdUser'] = $p_userId;
		$startIP = null;
		if (!is_null($p_startIP)) {
			$startIP = $p_startIP;
			if (!is_array($startIP)) {
				$startIP = $this->__string2array($startIP);
			}
			$startIP = $this->__array2int($startIP);
		}
		$this->m_data['StartIP'] = $startIP;
		$this->m_data['Addresses'] = $p_addresses;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	}

	function create($p_userId, $p_startIP, $p_addresses = 1)
	{
		$startIP = null;
		$startIPstring = '';
		if (!is_null($p_startIP)) {
			if (!is_array($p_startIP)) {
				$startIP = $this->__string2array($p_startIP);
				$startIPstring = $p_startIP;
			} else {
				$startIPstring = $this->__array2string($p_startIP);
			}
			$startIP = $this->__array2int($p_startIP);
		}
	    $tmpValues = array('IdUser'=>$p_userId, 'StartIP'=>$startIP, 'Addresses'=>$p_addresses);
	    $result = parent::create($tmpValues);
	    if ($result) {
	    	$user =& new User($p_userId);
			$logtext = getGS('IP Group $1 added for user $2', "$startIPstring:$p_addresses",
							 $user->getUserName());
			Log::Message($logtext, null, 57);
	    }
		return $result;
	}

	function delete()
	{
		$startIPstring = $this->getStartIPstring();
		$addresses = $this->getAddresses();
		$result = parent::delete();
		if ($result) {
			$logtext = getGS('The IP address group $1 has been deleted.', "$startIPstring:$addresses");
			Log::Message($logtext, null, 58);
		}
		return $result;
	}

	function getUserId()
	{
		return $this->m_data['IdUser'];
	}

	function getStartIP()
	{
		return $this->m_data['StartIP'];
	}

	function getStartIParray()
	{
		return $this->__int2array($this->m_data['StartIP']);
	}

	function getStartIPstring()
	{
		return $this->__array2string($this->__int2array($this->m_data['StartIP']));
	}

	function getAddresses()
	{
		return $this->m_data['Addresses'];
	}

	function GetUserIPAccessList($p_userId)
	{
		global $g_ado_db;

		$queryStr = "SELECT * FROM SubsByIP WHERE IdUser = $p_userId";
		$rows = $g_ado_db->GetAll($queryStr);
		$IPAccessList = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$tmpObj =& new IPAccess();
				$tmpObj->fetch($row);
				$IPAccessList[] = $tmpObj;
			}
		}
		return $IPAccessList;
	}

}
?>