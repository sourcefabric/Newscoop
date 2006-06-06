<br />

<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}
require_once($g_documentRoot.'/classes/Language.php');
?>
<table class="table_input" align="left" valign="top" width="800px">
<tr>
    <td colspan="2">
<?php
if (isset($sendWasAttempted) && $sendWasAttempted=="true"){

    echo ("<b>");
    putGS ("We are sorry, but there was a problem sending your bug report." );
    echo ("</b>");
} else {
    echo ('<font size="+2"><b>');
    putGS ("Campsite has encountered a problem");
    echo ("</b></font>");
    echo ('<hr noshade size="1" color="black">');
}
?>
<p>
<?php
    putGS ("Please take a minute to send us an email.");
    putGS ("Include the error report below, as well as a brief explanation of what you were doing when the error occurred.");
    putGS ("Send the email to");
    echo ("<b>");
    echo ("campsite-support@lists.campware.org"); 
    echo ("</b>");
?>.
</p>
<p>
    <?php
    putGS ("Thank you.");
    ?>
</p>
<br />
    </td>
</tr>



<tr>
    <td colspan="2"><b><?php putGS("Error Report") ?></b>
                        <hr noshade size="1" color="black"><br /></td>
</tr>
<?php if (isset($sendWasAttempted) && $sendWasAttempted=="true"){
    include ("emailanddescription.php");
} ?>
<!--
<tr>
    <td nowrap><?php putGS("Email:") ?></td>
    <td><?php echo $reporter->getEmail(); ?></td>
</tr>
<tr>
    <td nowrap><?php putGS("Description:") ?></td>
    <td><?php echo $reporter->getDescription(); ?></td>
</tr>
<tr>
    <td nowrap>&nbsp;</td>
    <td>&nbsp;</td>
</tr>
-->
<tr>
    <td nowrap><?php putGS("Error ID:") ?></td>
    <td><?php echo $reporter->getId(); ?></td>
</tr>
<tr>
    <td nowrap><?php putGS("Error String:") ?></td>
    <td><?php echo $reporter->getStr(); ?></td>
</tr>
<tr>
    <td nowrap><?php putGS("Time:") ?></td>
    <td><?php echo $reporter->getTime(); ?></td>
</tr>
<tr align="left">
    <td valign="top" nowrap><?php putGS("Backtrace:") ?></td>
    <td>
        <pre>
<?php echo $reporter->getBacktraceString(); ?>
        </pre>
    </td>
</tr>
</table>
