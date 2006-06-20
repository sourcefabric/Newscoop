<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
camp_load_translation_strings('bug_reporting');
global $ADMIN_DIR;
global $Campsite;

?>
<br />
<table class="table_input" align="left" valign="top" width="650px" style="padding: 5px;">
<tr>
    <td colspan="2">
		<?php
		    echo '<font size="+2"><b>';
		    putGS("There was a problem sending your feedback.");
		    echo "</b></font>";
		    echo '<hr noshade size="1" color="black">';
		?>
		<p>
		<?php
		    putGS("Please take a minute to send us an email.");
		    echo "<br><br>";
		    putGS("Simply copy and paste your message below and send it to: $1.",
		    	  "<b>".$Campsite["SUPPORT_EMAIL"]."</b>");
		?>
		</p>
		<p>
		    <?php
		    putGS("Thank you.");
		    ?>
		</p>
    </td>
</tr>

<tr>
	<td style="padding-top: 1em;">
		<table>
		<tr>
		    <td nowrap><?php putGS("Email:") ?></td>
		    <td align="left"><?php echo htmlspecialchars($reporter->getEmail()); ?></td>
		</tr>
		<tr>
		    <td nowrap><?php putGS("Subject:") ?></td>
		    <td align="left"><?php echo htmlspecialchars($reporter->getDescription()); ?></td>
		</tr>
		<tr align="left">
		    <td valign="top" nowrap><?php putGS("Comment:") ?></td>
		    <td>
				<?php echo $reporter->getBacktraceString(); ?>
		    </td>
		</tr>
		</table>
	</td>
</tr>
</table>