<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
require_once "HTTP/Client.php";
require_once ($_SERVER['DOCUMENT_ROOT'] . "/classes/BugReporter.php");

// **Display the form, and then post the error to server**

global $Campsite, $ADMIN_DIR, $g_documentRoot, $g_bugReporterDefaultServer;

$server = $g_bugReporterDefaultServer;
// $server = "http://localhost/trac/autotrac";

import_request_variables('p', "f_"); 

// --- If this information is a POST from errormessage.php, send it to
//     the server ---
if (isset($f_isPostFromBugreporter) && $_SERVER['REQUEST_METHOD'] == "POST") {

    $sendWasAttempted = true;

    // --- If not all variables were posted, send a bugreport saying as much to the server ---
    if (!isset($f_num) || !isset($f_str) || !isset($f_file) || !isset($f_line) || !isset($f_time)
        || !isset($f_backtrace)){

        // -- Create an error description explaining which variables did and didn't get sent --

        $included = "";
        $notIncluded = "";

        $description = "Not all variables are being sent by Bugreporter.  \n\n";

        if (!isset($f_num)) {
            $f_num = 0;
            $notIncluded .= "f_num \n";
        } 
        else $included .= "f_num:" . urldecode($f_num) . " \n";

        if (!isset($f_str)) {
            $f_str = "";
            $notIncluded .= "f_str \n";
        } 
        else $included .= "f_str:" . urldecode($f_str) . " \n";

        if (!isset($f_file)) {
            $f_file = "";
            $notIncluded .= "f_file \n";
        } 
        else $included .= "f_file:" . urldecode($f_file) . " \n";

        if (!isset($f_line)) {
            $f_line = 0;
            $notIncluded .= "f_line \n";
        } 
        else $included .= "f_line:" . urldecode($f_line) . " \n";

        if (!isset($f_time)) {
            $f_time = date("r");
            $notIncluded .= "f_time \n";
        } 
        else $included .= "f_time:" . urldecode($f_time) . " \n";
        
        if (!isset($f_backtrace)) {
            $f_backtrace = "";
            $notIncluded .= "f_backtrace \n";
        } 
        else $included .= "f_backtrace:" . urldecode($f_backtrace) . " \n";

        $description .= "{{{\nVariables Included: \n$included\n" 
            . "Variables not included:\n$notIncluded\n}}}";

        $reporter = new BugReporter (0, "", "", ""
                                 , "", "", $f_time, " ");
        $reporter->setServer ($server);

        if (isset ($description))
            $reporter->setDescription(urldecode($description));

        $wasSent = $reporter->sendToServer ();

        // --- Wait, so as not to create timing problems with two sends ---
        usleep (1000000);

    } 

    // -- Attempt to send user's error (regardless of whether above report was also sent) --

    $f_num = urldecode($f_num);
    $f_str = urldecode($f_str);
    $f_file = urldecode($f_file);
    $f_line = urldecode($f_line);
    $f_time = urldecode($f_time);
    $f_backtrace = urldecode($f_backtrace);

    $reporter = new BugReporter ($f_num, $f_str, $f_file, $f_line
                                 , "Campsite", $Campsite['VERSION'], $f_time, $f_backtrace);
    $reporter->setServer ($server);

    if (isset ($f_email))
        $reporter->setEmail(urldecode($f_email));
    if (isset ($f_description))
        $reporter->setDescription(urldecode($f_description));

    $wasSent = $reporter->sendToServer ();

    // --- Verify send was successful, and say thankyou or sorry
    //     accordingly ---
    if ($wasSent == true) {
        include ($Campsite['HTML_DIR'] . "/$ADMIN_DIR/bugreporter/thankyou.php");
    } else include ($Campsite['HTML_DIR'] . "/$ADMIN_DIR/bugreporter/emailus.php");
}

// --- Show the form  ---
else {
    // --- If reporter doesn't exist, make one ($reporter might exist
    //     already if this script is an 'include') ---
    if (!isset($reporter))
        $reporter = new BugReporter ($p_number, $p_string
        , $p_file, $p_line, "Campsite", $Campsite['VERSION']);

    $reporter->setServer ($server);

    // --- Ping AutoTrac Server ---
    $wasPinged = $reporter->pingServer();

    // --- Print contents of error-page.html ---
    if ($wasPinged) {
        include ($Campsite['HTML_DIR'] . "/$ADMIN_DIR/bugreporter/errormessage.php");
    } else {
        include ($Campsite['HTML_DIR'] . "/$ADMIN_DIR/bugreporter/emailus.php");
    }
} 

?>
