<?php

foreach ($GLOBALS['argv'] as $arg_n=>$arg_val)
	if ($arg_n > 0)
		export_conf($arg_val);

function export_conf($module_name)
{
	$module_name = strtolower($module_name);
	$conf_file = strtolower($module_name) . "_conf.php";
	if (!file_exists($conf_file)) {
		echo "Invalid file name $conf_file\n";
		return 1;
	}

	require_once($conf_file);
	foreach ($campsite_local_vars[$module_name] as $key=>$var_name)
		$vars .= " " . $var_name;
	echo $vars;
}

?>