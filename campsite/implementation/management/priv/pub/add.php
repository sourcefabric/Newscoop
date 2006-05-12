<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/TimeUnit.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/UrlType.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Alias.php");

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
$shortNameUrlType = UrlType::GetByName('short names');
$aliases = array();

$crumbs = array();
$crumbs[] = array(getGS("Publications"), "/$ADMIN/pub/");
$crumbs[] = array(getGS("Add new publication"), "");
echo camp_html_breadcrumbs($crumbs);
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/pub/"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/pub/"><B><?php  putGS("Publication List"); ?></B></A></TD>
</TR>
</TABLE>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php">
<?php include("pub_form.php"); ?>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
