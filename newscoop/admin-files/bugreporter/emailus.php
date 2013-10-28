<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
$translator = \Zend_Registry::get('container')->getService('translator');
global $ADMIN_DIR;
global $Campsite;

?>
<br />
<table border="0" cellpadding="0" cellspacing="0" class="box_table">
<tr>
    <td colspan="2">
<?php
if (isset($sendWasAttempted) && $sendWasAttempted){
    echo "<b>";
    echo $translator->trans("We are sorry, but there was a problem sending your bug report.", array(), 'bug_reporting');
    echo "</b>";
} else {
    echo '<font size="+2"><b>';
    echo $translator->trans("Newscoop has encountered a problem.", array(), 'bug_reporting');
    echo "</b></font>";
    echo '<hr noshade size="1" color="black">';
}
?>
<p>
<?php
    echo $translator->trans("Please take a minute to send us an email.");
    echo "<br><br>";
    echo $translator->trans("Simply copy and paste the error report below and send it to:", array(), 'bug_reporting');
    echo (" <b>");
    echo $Campsite["SUPPORT_EMAIL"];
    echo ("</b>");
?>.
</p>
<p>
    <?php
    echo $translator->trans("Thank you.");
    ?>
</p>
<br />
    </td>
</tr>

<tr>
    <td colspan="2"><b><?php echo $translator->trans("Error Report", array(), 'bug_reporting'); ?></b>
    <hr noshade size="1" color="black"><br /></td>
</tr>
<?php if (isset($sendWasAttempted) && $sendWasAttempted) { ?>
	<tr>
	    <td nowrap><?php echo $translator->trans("Email:"); ?></td>
	    <td><?php echo htmlspecialchars($reporter->getEmail()); ?></td>
	</tr>
	<tr>
	    <td nowrap><?php echo $translator->trans("Description:", array(), 'bug_reporting'); ?></td>
	    <td><?php echo htmlspecialchars($reporter->getDescription()); ?></td>
	</tr>
	<tr>
	    <td nowrap>&nbsp;</td>
	    <td>&nbsp;</td>
	</tr>
<?php } ?>
<tr>
    <td nowrap><?php echo $translator->trans("Error ID:", array(), 'bug_reporting'); ?></td>
    <td><?php echo $reporter->getId(); ?></td>
</tr>
<tr>
    <td nowrap><?php echo $translator->trans("Error String:", array(), 'bug_reporting'); ?></td>
    <td><?php echo $reporter->getStr(); ?></td>
</tr>
<tr>
    <td nowrap><?php echo $translator->trans("Time:"); ?></td>
    <td><?php echo $reporter->getTime(); ?></td>
</tr>
<tr align="left">
    <td valign="top" nowrap><?php echo $translator->trans("Backtrace:", array(), 'bug_reporting'); ?></td>
    <td>
<pre>
<?php echo $reporter->getBacktraceString(); ?>
</pre>
    </td>
</tr>
</table>
