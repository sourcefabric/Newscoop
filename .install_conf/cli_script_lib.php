<?php

function exec_command($cmd, $err_msg = "", $print_output = true)
{
	exec($cmd, $output, $result);
	if ($result != 0) {
		if (!$print_output)
			$output = array();
		if ($err_msg != "") {
			$my_output[] = $msg;
			$output = array_merge($my_output, $output);
		}
		exit_with_error($output);
	}
}


function create_dir($dir_name, $msg = "")
{
	if ($msg == "")
		$msg = "Unable to create directory $dir_name.";
	if (!is_dir($dir_name) && !mkdir($dir_name))
		exit_with_error($msg);
	return 0;
}


function file_name($file_path)
{
	$slash_pos = strrpos($file_path, '/');
	if (!$slash_pos)
		return $file_path;
	return substr($file_path, $slash_pos + 1);
}

function backup_file($file_path, &$output)
{
	if (!is_file($file_path)) {
		$output = "File $file_path does not exist.";
		return 1;
	}
	$dir_name = dirname($file_path);
	if (!($file_stat = @stat($file_path))) {
		$output = "Unable to read file $file_path data.";
		return 1;
	}
	$file_name = substr($file_path, strlen($dir_name) + 1);
	if ($dot_pos = strrpos($file_name, '.')) {
		$base_name = substr($file_name, 0, $dot_pos);
		$extension = substr($file_name, $dot_pos);
	} else {
		$base_name = $file_name;
		$extension = "";
	}
	$change_time = strftime("%Y-%m-%d-%H", $file_stat['ctime']);
	$new_name = "$base_name-$change_time$extension";

	if (is_file("$dir_name/$new_name"))
		return 0;

	if (!rename($file_path, "$dir_name/$new_name")) {
		$output = "Unable to rename file $file_path";
		return 1;
	}
	return 0;
}

function archive_file($source_file, $dest_dir, $file_name, &$output)
{
	$source_dir = dirname($source_file);
	$source_file_name = substr($source_file, strlen($source_dir) + 1);
	$cmd = "pushd $source_dir > /dev/null && tar czf "
		. escapeshellarg("$dest_dir/$file_name.tar.gz")
		. " " . escapeshellarg($source_file_name) . " && popd > /dev/null";
	exec($cmd, $output, $result);
	return $result;
}

function backup_database($db_name, $dest_file, &$output)
{
	global $Campsite;

	$user = $Campsite['DATABASE_USER'];
	$password = $Campsite['DATABASE_PASSWORD'];
	$cmd = "mysqldump --add-drop-table -c -e -Q -u $user";
	if ($password != "")
		$cmd .= " -p=$password";
	$cmd .= " $db_name > $dest_file";
	exec($cmd, $output, $result);
	return $result;
}

function exit_with_error($error_str)
{
	if (is_array($error_str))
		$error_str = implode("\n", $error_str);
	echo "$error_str\n";
	clean_files();
	exit(1);
}

function clean_files()
{
	global $Campsite;

	$backup_dir = $Campsite['CAMPSITE_DIR'] . "/backup/" . $Campsite['DATABASE_NAME'];
	exec_command("rm -f $backup_dir/*.tar.gz");
}

?>
