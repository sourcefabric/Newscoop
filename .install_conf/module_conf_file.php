<?php
foreach ($GLOBALS['argv'] as $arg_n=>$arg_val)
	if ($arg_n > 0)
		echo strtolower($arg_val) . "_conf.php";
?>