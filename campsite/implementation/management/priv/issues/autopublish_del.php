<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/IssuePublish.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('Publish')) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS("You do not have the right to schedule issues or articles for automatic publishing." )));
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$publish_time = trim(Input::Get('publish_time', 'string', ''));

$action =& new IssuePublish($Pub, $Issue, $Language, $publish_time);
$action->delete();
//$AFFECTED_ROWS=0;
//$sql = "DELETE FROM IssuePublish WHERE IdPublication = $Pub AND NrIssue = $Issue AND IdLanguage = $Language AND PublishTime = '$publish_time'";
//query ($sql);
//$del = $AFFECTED_ROWS > 0;
$del = 1;

if ($del)
	header("Location: /$ADMIN/issues/autopublish.php?Pub=$Pub&Issue=$Issue&Language=$Language");

?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD><?php  putGS("Delete scheduled publish action"); ?></TD>
		<TD ALIGN=RIGHT>
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
			<TD><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php p($Pub); ?>" class="breadcrumb"><?php  putGS("Issues");  ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/" class="breadcrumb"><?php  putGS("Publications");  ?></A></TD>
		</TR>
		</TABLE>
		</TD>
	</TR>
</TABLE>

<?php
	query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	if ($NUM_ROWS) {
		query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
		if ($NUM_ROWS) {
		    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		    fetchRow($q_iss);
		    fetchRow($q_pub);
		    fetchRow($q_lang);
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php pgetHVar($q_pub,'Name'); ?></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Issue"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php pgetHVar($q_iss,'Number'); ?>. <?php pgetHVar($q_iss,'Name'); ?> (<?php pgetHVar($q_lang,'Name'); ?>)</TD>
</TR>
</TABLE>

<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Delete scheduled publish action"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php

	if ($del) { ?>		<LI><?php putGS('The action scheduled on $1 has been deleted.','<B>'.$publish_time.'</B>' ); ?></LI>
	<?php } else { ?>		<LI><?php putGS('The action scheduled on $1 could not be deleted.','<B>'.$publish_time.'</B>' ); ?></LI>
	<?php } ?>	</BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
<?php
    if ($del) { ?>		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/issues/autopublish.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>'">
<?php } else { ?>		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/issues/autopublish.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>'">
<?php } ?>		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
<P>

<?php } else { ?><BLOCKQUOTE>
	<LI><?php putGS("Publication does not exist."); ?></LI>
</BLOCKQUOTE>
<?php } ?>
<?php } else { ?><BLOCKQUOTE>
	<LI><?php putGS("No such issue."); ?></LI>
</BLOCKQUOTE>
<?php } ?>
<?php camp_html_copyright_notice(); ?>
</BODY>

</HTML>
