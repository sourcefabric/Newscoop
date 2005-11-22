<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/TimeUnit.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/UrlType.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to add publications."));
	exit;
}

$languages = Language::GetLanguages();
$defaultLanguage = array_pop(Language::GetLanguages(null, $_REQUEST['TOL_Language']));
$urlTypes = UrlType::GetUrlTypes();
$timeUnits = TimeUnit::GetTimeUnits($_REQUEST['TOL_Language']);

$crumbs = array();
$crumbs[] = array(getGS("Publications"), "/$ADMIN/pub/");
$crumbs[] = array(getGS("Add new publication"), "");
echo camp_html_breadcrumbs($crumbs);
?>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add new publication"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="255">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Site"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cSite" VALUE="<?php p(urlencode($_SERVER['HTTP_HOST'])); ?>" SIZE="32" MAXLENGTH="255">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Default language"); ?>:</TD>
	<TD>
    <SELECT NAME="cLanguage" class="input_select">
    <?php 
    foreach ($languages as $language) {
		camp_html_select_option($language->getLanguageId(), $defaultLanguage->getLanguageId(), $language->getNativeName());
    }
    ?>	    </SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("URL Type"); ?>:</TD>
	<TD>
	<SELECT NAME="cURLType" class="input_select">
	<?php
	foreach ($urlTypes as $urlType) {
		camp_html_select_option($urlType->getId(), 0, $urlType->getName());
	}
	?>
	</SELECT>
	</TD>
</TR>

<tr><td colspan=2><HR NOSHADE SIZE="1" COLOR="BLACK"></td></tr>
<tr><td colspan=2><b><?php putGS("Subscriptions defaults"); ?></b></td></tr>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Pay Period"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cPayTime" VALUE="" SIZE="5" MAXLENGTH="5">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Time Unit"); ?>:</TD>
	<TD>
    <SELECT NAME="cTimeUnit" class="input_select">
	<?php 
	foreach ($timeUnits as $timeUnit) {
		camp_html_select_option($timeUnit->getUnit(), 0, $timeUnit->getName());		
	}
	?>	    
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Unit Cost"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cUnitCost" VALUE="" SIZE="10" MAXLENGTH="10">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Currency"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cCurrency" VALUE="" SIZE="10" MAXLENGTH="10">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Paid Period"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cPaid" VALUE="" SIZE="10" MAXLENGTH="10">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Trial Period"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cTrial" VALUE="" SIZE="10" MAXLENGTH="10">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/pub/'">-->
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
