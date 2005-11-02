<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/IssuePublish.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageIssue')) {
	camp_html_display_error(getGS('You do not have the right to change issues.'));
	exit;
}
$Pub = Input::Get('Pub', 'int');
$Issue = Input::Get('Issue', 'int');
$Language = Input::Get('Language', 'int');
$publish_time = Input::Get('publish_time', 'string', '', true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));
	exit;
}
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);

if ($publish_time == "") {
	$publish_time = date("Y-m-d H:i");
}
$action = '';
$publish_articles = '';
if ($publish_time != '') {
	$issuePublishObj =& new IssuePublish($Pub, $Issue, $Language, $publish_time);
	if ($issuePublishObj->exists()) {
		$action = $issuePublishObj->getPublishAction();
		$publish_articles = $issuePublishObj->getPublishArticlesAction();
	}
	$datetime = explode(" ", trim($publish_time));
	$publish_date = $datetime[0];
	$publish_time = explode(":", trim($datetime[1]));
	$publish_hour = $publish_time[0];
	$publish_min = $publish_time[1];
}
$allEvents = IssuePublish::GetIssueEvents($Pub, $Issue, $Language);

camp_html_content_top(getGS('Issue Publishing Schedule'), array('Pub' => $publicationObj, 'Issue' => $issueObj));

?>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="autopublish_do_add.php" >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Schedule a new action"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php echo $Pub; ?>">
<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php echo $Issue; ?>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php echo $Language; ?>">
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Date"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="publish_date" SIZE="11" MAXLENGTH="10" VALUE="<?php p($publish_date); ?>">
	<?php putGS('YYYY-MM-DD'); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Time"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="publish_hour" SIZE="2" MAXLENGTH="2" VALUE="<?php p($publish_hour); ?>"> :
	<INPUT TYPE="TEXT" class="input_text" NAME="publish_min" SIZE="2" MAXLENGTH="2" VALUE="<?php p($publish_min); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Action"); ?>:</TD>
	<TD>
	<SELECT NAME="action" class="input_select">
		<OPTION VALUE=" ">---</OPTION>
		<OPTION VALUE="P" <?php if ($action == "P") echo "SELECTED"; ?>><?php putGS("Publish"); ?></OPTION>
		<OPTION VALUE="U" <?php if ($action == "U") echo "SELECTED"; ?>><?php putGS("Unpublish"); ?></OPTION>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Publish articles"); ?>:</TD>
	<TD>
	<SELECT NAME="publish_articles" class="input_select">
		<OPTION VALUE="Y" <?php if ($publish_articles == "Y") echo "SELECTED"; ?>><?php putGS("Yes"); ?></OPTION>
		<OPTION VALUE="N" <?php if ($publish_articles == "N") echo "SELECTED"; ?>><?php putGS("No"); ?></OPTION>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
	<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/issues/?Pub=<?php p($Pub); ?>&Language=<?php p($Language); ?>'">-->
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
</P>

<P>
<?php
if (count($allEvents) > 0) {
	$color= 0;
	?>
	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Date/Time"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Action"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Publish articles"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Delete"); ?></B></TD>
	</TR>
	
	<?php
	foreach ($allEvents as $event) {
		$url_publish_time = urlencode($event->getActionTime());
		?>	
		<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		
		<TD >
			<A HREF="/<?php echo $ADMIN; ?>/issues/autopublish.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>&publish_time=<?php echo $url_publish_time; ?>"><?php p(htmlspecialchars($event->getActionTime())); ?></A>
		</TD>
		
		<TD >
			<?php
				$action = $event->getPublishAction();
				if ($action == "P") {
					putGS("Publish");
				}
				else {
					putGS("Unpublish");
				}
			?>&nbsp;
		</TD>
		
		<TD >
			<?php
				$publish_articles = $event->getPublishArticlesAction();
				if ($publish_articles == "Y") {
					putGS("Yes");
				}
				else {
					putGS("No");
				}
			?>&nbsp;
		</TD>
		
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/issues/autopublish_del.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>&publish_time=<?php echo $url_publish_time; ?>" onclick="return confirm('<?php putGS("Are you sure you want to delete this scheduled action?"); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php putGS('Delete entry'); ?>"></A>
		</TD>
		
	<?php } // foreach ?>
	</TR>
<?php
} // if 
camp_html_copyright_notice();
?>