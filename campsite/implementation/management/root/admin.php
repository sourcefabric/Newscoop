<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');

$no_menu_scripts = array('/login.php', '/do_login.php', '/popup/index.php',
	'/popup/art.php', '/popup/empty.php', '/popup/i1.php', '/popup/i2.php',
	'/popup/i3.php', '/popup/i4.php', '/popup/img.php', '/popup/index.php',
	'/popup/iss.php', '/popup/lang.php', '/popup/pub.php', '/popup/sect.php'
	);

$request_uri = $_SERVER['REQUEST_URI'];
$call_script = substr($request_uri, strlen("/$ADMIN"));

// Remove any GET parameters
if (($question_mark = strpos($call_script, '?')) !== false) {
	$call_script = substr($call_script, 0, $question_mark);
}

$is_image = (strstr($call_script, '/img/') !== false);
//$extension = strtolower(substr($call_script, strlen($call_script) - 4));
$extension = '';
if (($extension_start = strrpos($call_script, '.')) !== false) {
	$extension = strtolower(substr($call_script, $extension_start));
}
	
// Is it an image?
if ($is_image) {
	$extension = substr(strrchr($call_script, '.'), 1);
	header("Content-type: image/$extension");
	header("Cache-control: private");
	readfile($Campsite['HTML_DIR'] . "/$ADMIN_DIR/$call_script");
} 
elseif (($extension == '.php') || ($extension == '')) {
	// Requested file is not an image

	// If they arent trying to login in...
	if (($call_script != '/login.php') && ($call_script != '/do_login.php')) {
		// Check if the user is logged in already
		require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
		load_common_include_files($ADMIN_DIR);
		list($access, $User) = check_basic_access($_REQUEST);
		if (!$access) {
			// If not logged in, show the login screen.
			header("Location: /$ADMIN/login.php");
			return;
		}
	}
		
	// If its not a PHP file, assume its a directory.
	if ($extension != '.php') {
		// If its a directory
		if (($call_script != '') && ($call_script[strlen($call_script)-1] != '/') ) {
			$call_script .= '/';
		}
		$call_script .= 'index.php';
	}
	$needs_menu = ! in_array($call_script, $no_menu_scripts);

	$menu = '';
	if ($needs_menu) {
		ob_start();
		echo "<html><table width=\"100%\">\n<tr><td>\n";
		require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/menu.php");
		echo "</td></tr>\n<tr><td>\n";
		$menu = ob_get_clean();
	}
	ob_start();
	require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/$call_script");
	$content = ob_get_clean();
	echo $menu . $content;
	
	if ($needs_menu) {
		echo "</td></tr>\n</table>\n</html>\n";
	}
}
else {
	readfile($Campsite['HTML_DIR'] . "/$ADMIN_DIR/$call_script");	
}
?>