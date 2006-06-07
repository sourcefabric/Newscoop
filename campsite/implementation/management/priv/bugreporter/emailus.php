<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
camp_load_translation_strings('bug_reporting');
global $ADMIN_DIR;
global $Campsite;

?>
<br />
<table class="table_input" align="left" valign="top" width="800px" style="padding: 5px;">
<tr>
    <td colspan="2">
<?php
if (isset($sendWasAttempted) && $sendWasAttempted){
    echo "<b>";
    putGS("We are sorry, but there was a problem sending your bug report." );
    echo "</b>";
} else {
    echo '<font size="+2"><b>';
    putGS("Campsite has encountered a problem.");
    echo "</b></font>";
    echo '<hr noshade size="1" color="black">';
}
?>
<p>
<?php
    putGS("Please take a minute to send us an email.");
    echo "<br><br>";
    putGS("Simply copy and paste the error report below and send it to:");
    echo (" <b>");
    echo $Campsite["SUPPORT_EMAIL"];
    echo ("</b>");
?>.
</p>
<p>
    <?php
    putGS("Thank you.");
    ?>
</p>
<br />
    </td>
</tr>

<tr>
    <td colspan="2"><b><?php putGS("Error Report") ?></b>
    <hr noshade size="1" color="black"><br /></td>
</tr>
<?php if (isset($sendWasAttempted) && $sendWasAttempted) { ?>
	<tr>
	    <td nowrap><?php putGS("Email:") ?></td>
	    <td><?php echo htmlspecialchars($reporter->getEmail()); ?></td>
	</tr>
	<tr>
	    <td nowrap><?php putGS("Description:") ?></td>
	    <td><?php echo htmlspecialchars($reporter->getDescription()); ?></td>
	</tr>
	<tr>
	    <td nowrap>&nbsp;</td>
	    <td>&nbsp;</td>
	</tr>
<?php } ?>
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
