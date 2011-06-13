<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/TimeUnit.php");
require_once($GLOBALS['g_campsiteDir']."/classes/UrlType.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Template.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Language.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Alias.php");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/camp_html.php");
camp_load_translation_strings("api");

// Check permissions
if (!$g_user->hasPermission('ManagePub') || !SaaS::singleton()->hasPermission("AddPub")) {
	camp_html_display_error(getGS("You do not have the right to add publications."));
	exit;
}

$languages = Language::GetLanguages(null, null, null, array(), array(), true);
$defaultLanguage = array_pop(Language::GetLanguages(null, camp_session_get('TOL_Language', 'en'), null, array(), array(), true));
$urlTypes = UrlType::GetUrlTypes();
$allTemplates = Template::GetAllTemplates(null, true, true, true);
$timeUnits = TimeUnit::GetTimeUnits(camp_session_get('TOL_Language', 'en'));
$shortNameUrlType = UrlType::GetByName('short names');
$aliases = array();

$crumbs = array();
$crumbs[] = array(getGS("Publications"), "/$ADMIN/pub/");
$crumbs[] = array(getGS("Add new publication"), "");
echo camp_html_breadcrumbs($crumbs);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/pub/"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/pub/"><B><?php  putGS("Publication List"); ?></B></A></TD>
</TR>
</TABLE>
<?php camp_html_display_msgs(); ?>
<p>
<FORM NAME="publication_add" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/pub/do_add.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<?php include("pub_form.php"); ?>
</FORM>
<P>
<script>
document.publication_add.f_name.focus();
</script>
<?php camp_html_copyright_notice(); ?>
