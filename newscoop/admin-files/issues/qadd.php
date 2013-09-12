<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");

$translator = \Zend_Registry::get('container')->getService('translator');
// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
	camp_html_display_error($translator->trans('You do not have the right to add issues.', array(), 'issues'));
	exit;
}

$Pub = Input::Get('Pub', 'int');
if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid Input: $1', array('$1' => Input::GetErrorString()), 'issues'));
	exit;
}
$publicationObj = new Publication($Pub);
$allLanguages = Language::GetLanguages(null, null, null, array(), array(), true);
$newIssueId = Issue::GetUnusedIssueId($Pub);

camp_html_content_top($translator->trans('Add new issue'), array('Pub' => $publicationObj), true, false, array($translator->trans("Issues") => "/$ADMIN/issues/?Pub=$Pub"));

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="box_table">
<TR>
    <TD VALIGN="TOP"><A HREF="/<?php echo $ADMIN; ?>/issues/add_prev.php?Pub=<?php p($Pub); ?>"><IMG SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/tol.gif" BORDER="0"></A></TD>
    <TD><B><A HREF="/<?php echo $ADMIN; ?>/issues/add_prev.php?Pub=<?php p($Pub); ?>"><?php echo $translator->trans('Use the structure of the previous issue', array(), 'issues'); ?></A></B></TD>
</TR>
<TR>
	<TD></TD>
	<TD VALIGN="TOP">
		<LI><?php echo $translator->trans('Copy the entire structure in all languages from the previous issue except for content.', array(), 'issues'); ?><LI><?php echo $translator->trans('You may modify it later if you wish.', array(), 'issues'); ?></LI>
	</TD>
<TR>
<?php
	if (SaaS::singleton()->hasPermission('ManageIssueTemplates')) {
?>
<TR>
    <TD VALIGN="TOP"><A HREF="/<?php echo $ADMIN; ?>/issues/add_new.php?Pub=<?php  p($Pub); ?>"><IMG SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/tol.gif" BORDER="0"></A></TD>
    <TD><B><A HREF="/<?php echo $ADMIN; ?>/issues/add_new.php?Pub=<?php  p($Pub); ?>"><?php echo $translator->trans('Create a new structure', array(), 'issues'); ?></A></B></TD>
</TR>
<TR>
	<TD></TD>
	<TD VALIGN="TOP">
		<LI><?php echo $translator->trans('Create a complete new structure.', array(), 'issues'); ?><LI><?php echo $translator->trans('You must define an issue type for each language and then sections for them.', array(), 'issues'); ?></LI>
	</TD>
<TR>
<?php
	}
?>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
