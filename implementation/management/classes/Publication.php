<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');

class Publication extends DatabaseObject {
	var $m_dbTableName = 'Publications';
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('Id', 'Name', 'IdDefaultLanguage', 'PayTime', 'TimeUnit', 'UnitCost', 'Currency', 'TrialTime', 'PaidTime', 'IdDefaultAlias', 'IdURLType');
	
	function Publication($p_publicationId = null) {
		parent::DatabaseObject($this->m_columnNames);
		$this->setProperty('Id', $p_publicationId, false);
		//$this->Id = $p_publicationId;
		if (!is_null($p_publicationId)) {
			$this->fetch();
		}
	} // constructor

	
	function getPublicationId() {
		return $this->getProperty('Id');
	} // fn getPublicationId
	
	
	function getName() {
		return $this->getProperty('Name');
	} // fn getName

	function GetAllPublications() {
		global $Campsite;
		$queryStr = 'SELECT * FROM Publications';
		$query = $Campsite['db']->Execute($queryStr);
		$publications = array();
		while ($row = $query->FetchRow()) {
			$tmpPub =& new Publication();
			$tmpPub->fetch($row);
			$publications[] = $tmpPub;
		}
		return $publications;
	} // fn getAllPublications
	
} // class Publication
?>