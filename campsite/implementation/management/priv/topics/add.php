<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/topics/topics_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageTopics')) {
	camp_html_display_error(getGS("You do not have the right to add topics."));
	exit;
}

$f_topic_parent_id = Input::Get('f_topic_parent_id', 'int', 0);
$topic =& new Topic($f_topic_parent_id, 1);
$Path = camp_topic_path($topic);

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Topics"), "/$ADMIN/topics/");
$crumbs[] = array(getGS("Add new topic"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Topic"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php p($Path);?></TD>
</TR>
</TABLE>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php"  >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add new topic"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_name" SIZE="32" MAXLENGTH="255">
	<INPUT TYPE="HIDDEN" NAME="f_topic_parent_id" VALUE="<?php p($f_topic_parent_id);?>">
	</TD>
</TR>

<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
<!--	<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/admin/topics/index.php?f_topic_parent_id=<?php p($f_topic_parent_id);?>'">-->
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
