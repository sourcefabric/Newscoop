<?php

if (!is_array($GLOBALS['argv'])) {
	echo "Can't read command line arguments\n";
	exit(1);
}

foreach ($GLOBALS['argv'] as $arg_n=>$arg_val)
	if ($arg_n > 0)
		export_conf($arg_val);

function export_conf($module_name)
{
	global $CampsiteVars;

	$module_name = strtolower($module_name);
	$conf_file = strtolower($module_name) . "_conf.php";
	if (!file_exists($conf_file)) {
		echo "Invalid file name $conf_file\n";
		return 1;
	}

	$vars = "";
	require_once($conf_file);
	foreach ($CampsiteVars[$module_name] as $key=>$var_name)
		$vars .= " " . $var_name;
	echo $vars;
}

?>