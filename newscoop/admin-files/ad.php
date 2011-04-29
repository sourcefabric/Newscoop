<?php
require_once($GLOBALS['g_campsiteDir']."/db_connect.php");
camp_load_translation_strings("home");
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
$ADReason = Input::Get('ADReason', 'string', getGS('You do not have the right to access this page.'), true);
$BackLink = Input::Get('Back', 'string', "/$ADMIN/home.php", true);

?>
<p>
<FORM>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER" style="margin-top: 50px; margin-bottom: 50px;">
<TR>
	<TD COLSPAN="2">
		<B><font color="red"><?php  putGS("Error"); ?> </font></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<font color="red">
		<li><?php  print htmlspecialchars($ADReason); ?></li>
		</font>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<DIV ALIGN="CENTER">
		<!--

 		-->
		<?php
			if($BackLink != "/$ADMIN/home.php") {
				?>
					<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='<?php p($BackLink); ?>'">
			<?php } else { ?>
					<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="window.history.back()">
		<?php } ?>
		</DIV>
	</TD>
</TR>
</TABLE>
</CENTER>
</FORM>

<?php camp_html_copyright_notice(); ?>