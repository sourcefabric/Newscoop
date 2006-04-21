<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
require_once "HTTP/Client.php";
require_once ($_SERVER['DOCUMENT_ROOT'] . "/classes/BugReporter.php");

// **Display the form, and then post the error to server**

global $Campsite, $ADMIN_DIR;

// --- Show the form when this file is first loaded ---
if ($_SERVER['REQUEST_METHOD'] == "GET") {

    // --- If reporter doesn't exist make one ($reporter might exist already 
    //     if this script is included) ---
    if ($reporter == null) 
        $reporter = new BugReporter ($p_number, $p_string
        , $p_file, $p_line, "Campsite", $Campsite['VERSION']);

    //$reporter->setServer ("http://localhost/trac/autotrac");

    // --- Ping AutoTrac Server ---
    $wasPinged = $reporter->pingServer();

    // --- Print contents of error-page.html ---
    if ($wasPinged) {
        include ($Campsite['HTML_DIR'] . "/$ADMIN_DIR/bugreporter/errormessage.php");
    } else {
        include ($Campsite['HTML_DIR'] . "/$ADMIN_DIR/bugreporter/emailus.php");
    }

  // --- If this information was sent via POST, it's time to send to the server ---
} else if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Todo: find a way to print this menu, currently the user has  no access at this point ---
    #echo "<html><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n<tr><td>\n";
    #require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/menu.php");
    #echo "</td></tr>\n<tr><td>\n";


    $sendWasAttempted = true;

    // --- Confirm proper variables were posted here, if so: post to
    //     Campsite server ---
    if (isset($f_num) && isset($f_str) && isset($f_file) && isset($f_line) && isset($f_time)
        && isset($f_backtrace)){
        
        $f_num = urldecode($f_num);
        $f_str = urldecode($f_str);
        $f_file = urldecode($f_file);
        $f_line = urldecode($f_line);
        $f_time = urldecode($f_time);
        $f_backtrace = urldecode($f_backtrace);

        $reporter = new BugReporter ($f_num, $f_str, $f_file, $f_line
        , "Campsite", $Campsite['VERSION'], $f_time, $f_backtrace);
        //$reporter->setServer ("http://localhost/trac/autotrac");

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
    } else {
        include ($Campsite['HTML_DIR'] . "/$ADMIN_DIR/bugreporter/emailus.php");
    }
}

?>
