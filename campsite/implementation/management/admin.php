<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
global $ADMIN_DIR;
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/lib_campsite.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

camp_set_error_handler("camp_report_bug");

/**
 * This file is basically a hack so that we could implement the
 * new interface without having to rewrite everything.
 */

$no_menu_scripts = array(
    '/login.php',
    '/do_login.php',
    '/issues/preview.php',
	'/issues/empty.php',
	'/ad_popup.php',
	'/articles/preview.php',
	'/articles/autopublish.php',
	'/articles/autopublish_do_add.php',
	'/articles/images/popup.php',
	'/articles/images/view.php',
	'/articles/topics/popup.php',
	'/articles/files/popup.php',
	'/articles/empty.php',
	'/comments/ban.php',
	'/comments/do_ban.php');

$request_uri = $_SERVER['REQUEST_URI'];
$call_script = substr($request_uri, strlen("/$ADMIN"));

// Remove any GET parameters
if (($question_mark = strpos($call_script, '?')) !== false) {
	$call_script = substr($call_script, 0, $question_mark);
}

// Remove all attempts to get at other parts of the file system
$call_script = str_replace('/../', '/', $call_script);

$extension = '';
if (($extension_start = strrpos($call_script, '.')) !== false) {
	$extension = strtolower(substr($call_script, $extension_start));
}

global $g_user;

if (($extension == '.php') || ($extension == '')) {
	header("Content-type: text/html; charset=UTF-8");

	// If they arent trying to login in...
	if (($call_script != '/login.php') && ($call_script != '/do_login.php')) {
		// Check if the user is logged in already
		list($access, $g_user) = camp_check_admin_access($_REQUEST);
		if (!$access) {
			// If not logged in, show the login screen.
			header("Location: /$ADMIN/login.php");
			return;
		}
	}

	// Load common translation strings
	camp_load_translation_strings('globals');

	// If its not a PHP file, assume its a directory.
   	if ($extension != '.php') {
		// If its a directory
		if (($call_script != '') && ($call_script[strlen($call_script)-1] != '/') ) {
			$call_script .= '/';
		}
		$call_script .= 'index.php';
	}
	$needs_menu = ! in_array($call_script, $no_menu_scripts);

	// Verify the file exists
	$path_name = $Campsite['HTML_DIR'] . "/$ADMIN_DIR/$call_script";
	if (!file_exists($path_name)) {
		header("HTTP/1.1 404 Not found");
		exit;
	}

	// Clean up the global namespace before we call the script
	unset($access);
	unset($extension);
	unset($extension_start);
	unset($question_mark);
	unset($no_menu_scripts);
	unset($request_uri);

	require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/init_content.php");

	// Get the main content
	ob_start();
	require_once($path_name);
	$content = ob_get_clean();

	// We create the top menu AFTER the main content because
	// of the localizer screen.  It will update the strings, which
	// need to be reflected immediately in the menu.
	$_top_menu = '';
	if ($needs_menu) {
		ob_start();
		echo "<html><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n<tr><td>\n";
		require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/menu.php");
		echo "</td></tr>\n<tr><td>\n";
		$_top_menu = ob_get_clean();
	}

	echo $_top_menu . $content;

	if ($needs_menu) {
		echo "</td></tr>\n</table>\n</html>\n";
	}

	camp_html_clear_msgs();
} else {
    readfile($Campsite['HTML_DIR'] . "/$ADMIN_DIR/$call_script");
}


/**
 * Sets a user-defined error function.
 *
 *  The function set_error_handler() works differently in PHP 4 & 5.
 *  This function is a wrapper interface, to both versions of
 *  set_error_handler.
 *
 * @param $p_function The function to execute on error
 * @return void
 */
function camp_set_error_handler($p_function) {

    // --- In PHP 5, the error handler-default is set at E_STRICT,
    //     which captures all legacy based errors.  Unfortunately, this is
    //     completely incompatible with PHP 4. ---
    if ( version_compare( phpversion(), "5.0.0", ">=" ) ) {
        set_error_handler($p_function, E_ALL);

    } else {
        // -- Meanwhile, the error-handler flag argument is not
        //    available in PHP 4, which always assumes it's value to be
        //    E_ALL --
        set_error_handler($p_function);
    }
} // fn camp_set_error_handler


/**
 * Called for all Campsite errors.
 */
function camp_report_bug($p_number, $p_string, $p_file, $p_line)
{
    global $ADMIN_DIR, $Campsite;
    require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/bugreporter/bug_handler_main.php");
    camp_bug_handler_main($p_number, $p_string, $p_file, $p_line);
} // fn camp_report_bug
?>