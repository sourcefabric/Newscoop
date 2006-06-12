<?php
require_once('db_connect.php');
camp_load_translation_strings("home");
require_once('classes/Input.php');
$ADReason = Input::Get('ADReason', 'string', getGS('You do not have the right to access this page.'), true);
$BackLink = Input::Get('Back', 'string', "/$ADMIN/home.php", true);
?>
<p>
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
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='<?php p($BackLink); ?>'">
		</DIV>
	</TD>
</TR>
</TABLE>
</CENTER>
</FORM>

<?php camp_html_copyright_notice(); ?>