<br />

<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}
require_once($g_documentRoot.'/classes/Language.php');

if (isset($sendWasAttempted) && $sendWasAttempted=="true"){
    echo ("<p>");
    putGS ("We are sorry, but there was a problem sending your bug report." );
    echo ("</p>");
}

else {
    echo ("<h1>");
    putGS ("Campsite has encountered a problem");
    echo ("</h1>");
}
        


?>

<p><?php
    putGS ("Please take a minute to write an explanation of what occurred and email your report to us at");
    echo ("<b>");
    putGS ("info@campware.org"); 
    echo ("</b>");
?>.
</p>
<p>
    <?php
    putGS ("Thank you.");
    ?>
</p>

