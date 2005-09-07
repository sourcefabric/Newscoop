<?php

if (!is_array($GLOBALS['argv'])) {
	echo "Can't read command line arguments\n";
	exit(1);
}

$param = isset($GLOBALS['argv'][1]) ? trim($GLOBALS['argv'][1]) : "";
if ($param == "") {
	echo "please specify the executable name\n";
	exit(1);
}

$paths = explode(":", getenv("PATH"));
foreach ($paths as $index=>$path) {
	$file_path = "$path/$param";
	if (is_executable($file_path)) {
		echo $file_path;
		exit(0);
	}
	if (is_link($file_path)) {
		$target_path = $file_path;
		do {
			if (is_executable($target_path)) {
				echo $file_path;
				exit(0);
			}
			$target_path = readlink($target_path);
		} while (is_link($target_path));
	}
}

exit(1);

?>
