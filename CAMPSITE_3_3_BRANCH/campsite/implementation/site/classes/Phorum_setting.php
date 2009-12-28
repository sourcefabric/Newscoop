<?php

class Phorum_setting extends DatabaseObject {
	var $m_keyColumnNames = array('name');
	var $m_keyIsAutoIncrement = false;
	var $m_columnNames = array(
		"name",
		"type",
		"data");

	public function Phorum_setting($p_name, $p_type)
	{
		global $PHORUM;
		$this->m_dbTableName = $PHORUM['settings_table'];
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['name'] = $p_name;
		$this->fetch();

		if (is_null($this->m_data['type'])) {
			$this->m_data['type'] = $p_type;
			$this->create();
		}
	} // constructor


	/**
	 * Create a forum.
	 *
	 * @return boolean
	 */
	public function create()
	{
		parent::create($this->m_data);
	} // fn create

	public function get()
	{
		if ($this->m_data['type'] == 'S' && is_string($this->m_data['data'])) {
			if (empty($this->m_data['data'])) {
				return array();
			}
			return unserialize($this->m_data['data']);
		}
		return $this->m_data['data'];
	}

	public function update($p_value)
	{
		if ($this->m_data['type'] == 'S') {
			$current = $this->get();
			$current = is_array($current) ? $current : array();
			$merged  = array_merge($current, $p_value);
			$this->setProperty('data', serialize($merged));
		} else {
			$this->setProperty('data', $p_value);
		}
	}

} // class Phorum_setting

?>