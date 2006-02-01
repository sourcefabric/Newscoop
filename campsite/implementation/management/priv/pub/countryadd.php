<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Country.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to manage publications."));
	exit;
}

$Pub = Input::Get('Pub', 'int');
$Language = Input::Get('Language', 'int', 1, true);
$publicationObj =& new Publication($Pub);

$countries = Country::GetCountries($Language);

$crumbs = array(getGS("Subscriptions") => "deftime.php?Pub=$Pub");
camp_html_content_top(getGS("Add new country default subscription time"), array("Pub" => $publicationObj), true, false, $crumbs);
?>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_countryadd.php">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add new country default subscription time"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<INPUT TYPE=HIDDEN NAME=cPub VALUE="<?php p($Pub); ?>">
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Country"); ?>:</TD>
	<TD>
    <SELECT NAME="cCountryCode" class="input_select">
	<?php 
	foreach ($countries as $country) { ?>
	    <OPTION VALUE="<?php  p(htmlspecialchars($country->getCode())); ?>"><?php p(htmlspecialchars($country->getName())); ?>	
	    <?php 
    }
	?>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Trial Period"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cTrialTime" VALUE="1" SIZE="5" MAXLENGTH="5">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Paid Period"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cPaidTime" VALUE="1" SIZE="5" MAXLENGTH="5">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/admin/pub/deftime.php?Pub=<?php  p($Pub); ?>&Language=<?php  p($Language); ?>'">-->
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<?php camp_html_copyright_notice(); ?>
