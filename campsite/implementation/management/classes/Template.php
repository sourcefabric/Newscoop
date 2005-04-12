<?php

class Template extends DatabaseObject {
	var $m_dbTableName = 'Templates';
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('Id', 'Name', 'Type', 'Level');
	
	function Template($p_templateId = null) {
		parent::DatabaseObject($this->m_columnNames);
		$this->setProperty('Id', $p_templateId, false);
		if (!is_null($p_templateId)) {
			$this->fetch();
		}
	} // constructor
	
	function getTemplateId() {
		return $this->getProperty('Id');
	}
	
	function getName() {
		return $this->getProperty('Name');
	}
	
	function getType() {
		return $this->getProperty('Type');
	}
	
	function getLevel() {
		return $this->getProperty('Level');
	}
	
	function getAbsoluteUrl() {
		global $Campsite;
		return $Campsite['WEBSITE_URL'].'/look/'.$this->getProperty('Name');
	}

	/**
	 * Returns true if the template path was valid
	 * @param string p_path
	 * @return bool
	 */
	function IsValidPath($p_path)
	{
		global $Campsite;

		foreach (split("/", $p_path) as $index=>$dir) {
			if ($dir == "..")
				return false;
		}

		if (!is_dir($Campsite['HTML_DIR'] . "/look/$p_path"))
			return false;

		return true;
	}
} // class Template
?>