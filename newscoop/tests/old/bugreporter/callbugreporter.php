<?php

// **This file disables post and then calls the bugreporter**

// --- set new error handler ---
$oldErrorHandler = set_error_handler ("call_bug_reporter");

// --- trigger error ---
trigger_error ("A triggered error");

// --- put back old error handler ---
set_error_handler ($oldErrorHandler);

function camp_call_bug_reporter($p_number, $p_string, $p_file, $p_line)
{
    global $ADMIN_DIR, $ADMIN, $Campsite;
    require_once ($GLOBALS['g_campsiteDir'] . "/classes/BugReporter.php");

    // --- Don't print the previous screen (in which the error occurred) ---
    ob_end_clean();

    echo "<html><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n<tr><td>\n";
    require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/menu.php");
    echo "</td></tr>\n<tr><td>\n";

    $reporter = new BugReporter ($p_number, $p_string, $p_file, $p_line, "Campsite", $Campsite['VERSION']);
    $reporter->setPingStatus(false);

    include ($Campsite['HTML_DIR'] . "/bugreporter/senderrorform.php");

    $buffer = ob_get_contents();

    echo ($buffer);

    exit();
}


?>
