<?

class Template extends DatabaseObject {
	var $m_dbTableName = "Templates";
	var $m_primaryKeyColumnNames = array("Id");
	var $Id;
	var $Name;
	var $Type;
	var $Level;
	
	function Template($p_templateId = null) {
		parent::DatabaseObject();
		$this->Id = $p_templateId;
		if (!is_null($p_templateId)) {
			$this->fetch();
		}
	} // constructor
	
	function getTemplateId() {
		return $this->Id;
	}
	
	function getName() {
		return $this->Name;
	}
	
	function getType() {
		return $this->Type;
	}
	
	function getLevel() {
		return $this->Level;
	}
	
	function getAbsoluteUrl() {
		global $Campsite;
		return $Campsite["website_url"]."/look/".$this->Name;
	}
	
} // class Template
?>