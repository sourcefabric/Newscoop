<?php
/**
 * @package Campsite
 */


function addslashes_walk(&$p_item, $p_key, $p_userData = null)
{
	$p_item = str_replace("\\", "\\\\", $p_item);
	$p_item = str_replace("'", "\\'", $p_item);
	return true;
}

class ModuleConfiguration
{
	var $m_moduleName;
	var $m_directory;
	var $m_variables;
	var $m_variablesList;

	function ModuleConfiguration($module_name = "", $directory = "")
	{
		if ($module_name != "")
			return $this->read($module_name, $directory);
	}

	/**
	 * Return configuration file name of a given module
	 *
	 * @param string $p_moduleName
	 *
	 * @return string or false in case of error
	 */
	function configurationFileName($p_moduleName = "")
	{
		$p_moduleName = $p_moduleName != "" ? $p_moduleName : $this->m_moduleName;
		if (!ModuleConfiguration::validModuleName($p_moduleName))
			return false;

		return $p_moduleName . "_conf.php";
	}

	/**
	 * Return configuration file path of a given module, directory
	 *
	 * @param string $p_moduleName
	 * @param string $p_directory
	 *
	 * @return string or false in case of error
	 */
	function configurationFilePath($p_moduleName = "", $p_directory = "")
	{
		$p_moduleName = $p_moduleName != "" ? $p_moduleName : $this->m_moduleName;
		if (!ModuleConfiguration::validModuleName($p_moduleName))
			return false;
		$p_directory = $p_directory != "" ? $p_directory : $this->m_directory;

		if ($p_directory != "" && $p_directory[strlen($p_directory)-1] != "/")
			$p_directory .= "/";
		$p_directory .= ModuleConfiguration::configurationFileName($p_moduleName);
		return $p_directory;
	}

	/**
	 * Read the configuration file corresponding to a specific module
	 *
	 * @param string $p_moduleName
	 * @param string $p_directory
	 *
	 * @return 0 or string in case of error
	 */
	function read($p_moduleName, $p_directory)
	{
		global $Campsite, $CampsiteVars;

		if (!ModuleConfiguration::validModuleName($p_moduleName))
			return "Invalid module name";

		// compute the configuration file path and include the file
		$file_path = ModuleConfiguration::configurationFilePath($p_moduleName, $p_directory);
		if (!file_exists($file_path))
			return "Invalid configuration file path or module name";
		include($file_path);

		// verify if the configuration file was correct
		if (!is_array($CampsiteVars) || !is_array($CampsiteVars[$p_moduleName]))
			return "Invalid configuration file format: variable list missing";
		if (!is_array($Campsite))
			return "Invalid configuration file format: variables missing";

		// initialize internal variables
		$this->m_moduleName = $p_moduleName;
		$this->m_directory = trim($p_directory);
		$this->m_variablesList = $CampsiteVars[$p_moduleName];
		foreach ($this->m_variablesList as $key=>$var_name)
			$this->m_variables[$var_name] = $Campsite[$var_name];

		return 0;
	}

	/**
	 * Create a module configuration object for a given module name and array of variables
	 *
	 * @param string $p_moduleName
	 * @param array $p_variables
	 *
	 * @return 0 or string in case of error
	 */
	function create($p_moduleName, $p_variables)
	{
		if (!ModuleConfiguration::validModuleName($p_moduleName))
			return "Invalid module name";

		$this->m_moduleName = $p_moduleName;
		$this->m_variables = $p_variables;
		$this->m_variablesList = array_keys($p_variables);
		return 0;
	}

	/**
	 * Save the configuration file corresponding to module object
	 *
	 * @param string $p_destDirectory
	 *
	 * @return 0 or string in case of error
	 */
	function save($p_destDirectory = "")
	{
		// check if the object was initialized correctly
		if (!ModuleConfiguration::validModuleName($this->m_moduleName))
			return "Invalid module name";
		if (!is_array($this->m_variablesList) || !is_array($this->m_variables))
			return "Variables not initialized";

		$moduleName = $this->m_moduleName;
		$directory = $p_destDirectory != "" ? $p_destDirectory : $this->m_directory;

		// compute the configuration file path and create the file
		$variablesList = $this->m_variablesList;
		array_walk($variablesList, 'addslashes_walk');
		$file_path = ModuleConfiguration::configurationFilePath($moduleName, $directory);
		if (!$file = @fopen($file_path, "w+"))
			return "Unable to create configuration file \"$file_path\"";
		fputs($file, "<?php\n\n");
		foreach($this->m_variables as $var_name=>$value)
			fputs($file, "\$Campsite['" . addslashes($var_name) . "'] = '"
				. addslashes($value) . "';\n");
		fputs($file, "\n\$CampsiteVars['" . addslashes($moduleName) . "'] = array('"
		      . implode("', '", $variablesList) . "');\n\n?>");
		fclose($file);
		return 0;
	}

	/**
	 * Return the module name
	 *
	 * @return string
	 */
	function moduleName()
	{
		return $this->m_moduleName;
	}

	/**
	 * Return the variables list as array
	 *
	 * @return array or false in case of error
	 */
	function variablesList()
	{
		if (!is_array($this->m_variables) || !is_array($this->m_variablesList))
			return false;
		if (!is_array($this->m_variablesList))
			return false;
		return $this->m_variablesList;
	}

	/**
	 * Return the value of a given variable
	 *
	 * @param string $p_variableName
	 *
	 * @return string or false in case of error
	 */
	function valueOf($p_variableName)
	{
		if (!is_array($this->m_variables) || !is_array($this->m_variablesList))
			return false;
		if (!in_array($p_variableName, $this->m_variablesList))
			return false;
		return $this->m_variables[$p_variableName];
	}

	/**
	 * Set the value of a given variable
	 *
	 * @param string $p_variableName
	 * @param string $p_value
	 *
	 * @return bool
	 */
	function setValueOf($p_variableName, $p_value)
	{
		if (!is_array($this->m_variables) || !is_array($this->m_variablesList))
			return false;
		if (!in_array($p_variableName, $this->m_variablesList))
			return false;
		$this->m_variables[$p_variableName] = $p_value;
		return true;
	}

	/**
	 * Return the true if the module name was valid
	 *
	 * @param string $p_moduleName
	 *
	 * @return bool
	 */
	function validModuleName(&$p_moduleName)
	{
		$p_moduleName = trim(strtolower($p_moduleName));
		if ($p_moduleName == "")
			return false;
		$invalid_chars = array("/");
		for ($i = 0; $i < strlen($p_moduleName); $i++)
			if (in_array($p_moduleName[$i], $invalid_chars) || $p_moduleName[$i] < " ")
				return false;
		return true;
	}
};

?>
