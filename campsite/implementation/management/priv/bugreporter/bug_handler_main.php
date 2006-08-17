<?PHP
require_once ($_SERVER['DOCUMENT_ROOT'] . "/classes/BugReporter.php");

/**
 * Called for all Campsite errors.
 *
 * If the flag $Campsite['DEBUG'] is set to false, this function will
 * return minor errors (ie notices and warnings) without having
 * processed them.  Errors with fsockopen() are returned without being
 * processed regardless of the $Campsite['DEBUG'] flag.
 *
 * @param int    $p_number The error number.
 * @param string $p_string The error message.
 * @param string $p_file The name of the file in which the error occurred.
 * @param int    $p_line The line number in which the error occurred.
 * @return void
 */
function camp_bug_handler_main($p_number, $p_string, $p_file, $p_line)
{
    global $ADMIN_DIR;
    global $ADMIN;
    global $Campsite;
	global $g_bugReporterDefaultServer;

	$server = $g_bugReporterDefaultServer;

    // --- Return on unimportant errors ---
    if (!$Campsite['DEBUG']) {
        switch ($p_number)
            {
            case E_NOTICE:
            case E_WARNING:
            case E_USER_NOTICE:
            case E_USER_WARNING:
                return;
            }
        }

    // --- Return on socket errros ---
    if (preg_match ('/^fsockopen/i', $p_string)){
        return;
    }

    // --- Return on unlink errros ---
    if (preg_match ('/^unlink/i', $p_string)){
        return;
    }

    // --- Return on upload file errros ---
    if (preg_match ('/^move_uploaded_file/i', $p_string)){
        return;
    }

    // -- Return on getimagesize errors --
    if (preg_match ('/^getimagesize/i', $p_string)){
        return;
    }

    // -- Return on rmdir errors --
    if (preg_match ('/^rmdir/i', $p_string)){
        return;
    }

    // -- Return on mkdir errors --
    if (preg_match ('/^mkdir/i', $p_string)){
        return;
    }

    // -- Return on fopen errors --
    if (preg_match ('/^fopen/i', $p_string)){
        return;
    }

    // --- Don't print out the previous screen (in which the error occurred). ---
    ob_end_clean();

    echo "<html><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n<tr><td>\n";
    require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/menu.php");
    echo "</td></tr>\n<tr><td>\n";

	// --- If reporter doesn't exist, make one ($reporter might exist
	//     already if this script is an 'include') ---

	// Remove the code name from the version number.
    $version = split(" ", $Campsite['VERSION']);
    $version = array_shift($version);

	if (!isset($reporter)) {
	    $reporter = new BugReporter($p_number, $p_string, $p_file, $p_line,
	    							"Campsite", $version);
	}

	$reporter->setServer($server);

	// --- Ping AutoTrac Server ---
	$wasPinged = $reporter->pingServer();

	// --- Print results ---
	if ($wasPinged) {
	    include($Campsite['HTML_DIR'] . "/$ADMIN_DIR/bugreporter/errormessage.php");
	} else {
	    include($Campsite['HTML_DIR'] . "/$ADMIN_DIR/bugreporter/emailus.php");
	}
    exit();
}
?>