<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DatabaseObject.php");

class Publication extends DatabaseObject {
	var $m_dbTableName = "Publications";
	var $m_primaryKeyColumnNames = array("Id");
	var $Id;
	var $Name;
	var $IdDefaultLanguage;
	var $PayTime;
	var $TimeUnit;
	var $UnitCost;
   	var $Currency;
	var $TrialTime;
	var $PaidTime;
	var $IdDefaultAlias;
	var $IdURLType;
	
	function Publication($p_publicationId = null) {
		parent::DatabaseObject();
		$this->Id = $p_publicationId;
		if (!is_null($p_publicationId)) {
			$this->fetch();
		}
	} // constructor

	
	function getPublicationId() {
		return $this->Id;
	} // fn getPublicationId
	
	
	function getName() {
		return $this->Name;
	} // fn getName

	function getAllPublications() {
		global $Campsite;
		$queryStr = "SELECT * FROM Publications";
		$query = $Campsite["db"]->Execute($queryStr);
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