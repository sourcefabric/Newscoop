<?php

if (!create_instance($GLOBALS['argv'], $errors))
	foreach($errors as $index=>$error)
		echo "$error\n";


function create_instance($arguments, &$errors)
{
	// read parameters
	if (!$defined_parameters = read_parameters($arguments, $errors))
		return false;

	$etc_dir = $defined_parameters['--etc_dir'];
	// check if etc directory was valid
	if (!is_dir($etc_dir)) {
		echo "Invalid etc directory " . $defined_parameters['--etc_dir'] . "\n";
		return false;
	}

	// check if install_conf.php and parser_conf.php files exist
	if (!is_file($etc_dir . "/install_conf.php")
		|| !is_file($etc_dir . "/parser_conf.php")) {
		echo "Configuration file(s) are missing\n";
		return false;
	}

	require_once($etc_dir . "/install_conf.php");
	require_once($etc_dir . "/parser_conf.php");

	if (!is_array($CampsiteVars['install']) || !is_array($CampsiteVars['parser'])
		|| !is_array($Campsite)) {
		echo "Invalid configuration file(s) format\n";
		return false;
	}

	foreach ($defined_parameters as $p_name=>$p_value)
		echo "$p_name = $p_value\n";
	return true;
}


function read_parameters($arguments, &$errors)
{
	$parameters = array('--etc_dir', '--db_server_address', '--db_server_port',
		'--db_name', '--db_user', '--db_password', '--parser_port',
		'--parser_max_threads', '--smtp_server_address',
		'--smtp_server_port', '--apache_user', '--apache_group');
	
	$mandatory_parameters = array('--etc_dir'=>false, '--db_server_address'=>false,
		'--db_name'=>false, '--db_user'=>false);

	$errors = array();
	for ($arg_n = 1; $arg_n < sizeof($arguments); $arg_n++) {
		// read the parameter name
		$param_name = $arguments[$arg_n];
		if (!in_array($param_name, $parameters)) {
			$errors[] = "Invalid parameter '$param_name'";
			continue;
		}
		// read the parameter value
		$arg_n++;
		if ($arg_n >= sizeof($arguments)) {
			$errors[] = "Value not specified for argument '$param_name'";
			break;
		}
		$param_val = $arguments[$arg_n];
	
		// set the parameter value in $defined_parameters array
		$defined_parameters[$param_name] = $param_val;
		if (array_key_exists($param_name, $mandatory_parameters))
			$mandatory_parameters[$param_name] = true;
	}
	// check if all mandatory parameters were specified
	foreach ($mandatory_parameters as $mp_name=>$mp_value)
		if ($mp_value == false)
			$errors[] = "'$mp_name' not specified; it is mandatory";

	if (sizeof($errors) > 0)
		return false;
	return $defined_parameters;
}

?>
