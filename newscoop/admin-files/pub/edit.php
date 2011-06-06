<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/TimeUnit.php");
require_once($GLOBALS['g_campsiteDir']."/classes/UrlType.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Template.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Alias.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Language.php");
require_once($GLOBALS['g_campsiteDir']."/include/phorum_load.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_forum.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_setting.php');
camp_load_translation_strings("api");

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to edit publication information."));
	exit;
}

$f_publication_id = Input::Get('Pub', 'int');
$TOL_Language = camp_session_get('TOL_Language', 'en');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$languages = Language::GetLanguages(null, null, null, array(), array(), true);
$urlTypes = UrlType::GetUrlTypes();
$allTemplates = Template::GetAllTemplates(null, true, true, true);
$timeUnits = TimeUnit::GetTimeUnits($TOL_Language);
$publicationObj = new Publication($f_publication_id);
$aliases = Alias::GetAliases(null, $f_publication_id);
$forum = new Phorum_forum($publicationObj->getForumId());

$pubTimeUnit = new TimeUnit($publicationObj->getTimeUnit(), $publicationObj->getLanguageId());
if (!$pubTimeUnit->exists()) {
	$pubTimeUnit = new TimeUnit($publicationObj->getTimeUnit(), 1);
}

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

echo camp_html_content_top(getGS("Configure publication"), array("Pub" => $publicationObj));
?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/pub/"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/pub/"><B><?php  putGS("Publication List"); ?></B></A></TD>
	<TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($f_publication_id); ?>"><B><?php  putGS("Go To Issues"); ?></B></A></TD>
	<TD ><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($f_publication_id); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/go_to.png" BORDER="0"></A></TD>
</TR>
</TABLE>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-bottom: 1em;">
<TR>
<?php  if ($g_user->hasPermission("ManagePub")) { ?>    <P>
	<TD>
		<A HREF="/<?php echo $ADMIN; ?>/pub/add.php?Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A>
	</TD>
	<TD>
		<A HREF="/<?php echo $ADMIN; ?>/pub/add.php?Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><B><?php  putGS("Add new publication"); ?></B></A>
	</TD>
<?php  } ?>
<?php
if ($g_user->hasPermission("DeletePub")) {
?>
    <TD style="padding-left: 10px;"><A HREF="/<?php echo $ADMIN; ?>/pub/do_del.php?Pub=<?php p($f_publication_id); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the publication $1?', htmlspecialchars($publicationObj->getName())); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0"></A></TD>
    <TD><A HREF="/<?php echo $ADMIN; ?>/pub/do_del.php?Pub=<?php p($f_publication_id); ?>&<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the publication $1?', htmlspecialchars($publicationObj->getName())); ?>');"><B><?php  putGS("Delete"); ?></B></A></TD>
<?php } ?>
</TR>
</TABLE>

<?php camp_html_display_msgs(0); ?>

<FORM METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/pub/do_edit.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<?php include("pub_form.php"); ?>
</FORM>
<P>
<?php camp_html_copyright_notice(); ?>
