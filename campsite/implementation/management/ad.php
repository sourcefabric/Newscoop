<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
    require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
    $globalfile=selectLanguageFile($Campsite['HTML_DIR'] . "/$ADMIN_DIR",'globals');
    $localfile=selectLanguageFile("$ADMIN_DIR","locals");
    @include_once($globalfile);
    @include_once($localfile);
    require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
?>

<?php  todef('ADReason',getGS('You do not have the right to access this page.'));?><CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <font color="red"><?php  putGS("Access denied"); ?> </font></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><font color=red><li><?php  print encHTML($ADReason); ?></li></font></BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/home.php'">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>

<HR NOSHADE SIZE="1" COLOR="BLACK">
<table cellpadding="0" cellspacing="0" width="100%"><tr><td style="padding-left: 5px;"><a STYLE="font-size:8pt;color:#000000" href="http://www.campware.org" target="campware">Campsite 2.2.0 &copy 1999-2005 MDLF, maintained and distributed under GNU GPL by CAMPWARE</a></td></tr></table>
</BODY>

</HTML>


