<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/IssuePublish.php');

$translator = \Zend_Registry::get('container')->getService('translator');
// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
	camp_html_display_error($translator->trans('You do not have the right to change issues.', array(), 'issues'));
	exit;
}
$Pub = Input::Get('Pub', 'int');
$Issue = Input::Get('Issue', 'int');
$Language = Input::Get('Language', 'int');
$event_id = Input::Get('event_id', 'string', null, true);

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid Input: $1', array('$1' => Input::GetErrorString()), 'issues'));
	exit;
}
$publicationObj = new Publication($Pub);
$issueObj = new Issue($Pub, $Language, $Issue);

$action = '';
$publish_articles = '';
$publish_date = date("Y-m-d");
$publish_hour = (date("H") + 1);
$publish_min = "00";

if (!is_null($event_id)) {
	$issuePublishObj = new IssuePublish($event_id);
	if ($issuePublishObj->exists()) {
		$action = $issuePublishObj->getPublishAction();
		$publish_articles = $issuePublishObj->getPublishArticlesAction();
	}
	$datetime = explode(" ", trim($issuePublishObj->getActionTime()));
	$publish_date = $datetime[0];
	$publish_time = explode(":", trim($datetime[1]));
	$publish_hour = $publish_time[0];
	$publish_min = $publish_time[1];
}

camp_html_content_top($translator->trans('Issue Publishing Schedule', array(), 'issues'), array('Pub' => $publicationObj, 'Issue' => $issueObj), true, true);

?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($Pub); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($Pub); ?>"><B><?php echo $translator->trans("Issue List"); ?></B></A></TD>
	<TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($Pub); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/issues/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($issueObj->getIssueNumber()); ?>&Language=<?php p($issueObj->getLanguageId()); ?>"><B><?php  echo $translator->trans("Issue").": ".htmlspecialchars($issueObj->getName()); ?></B></A></TD>
</TR>
</TABLE>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/issues/autopublish_do_add.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="box_table">
<TR>
	<TD COLSPAN="2">
		<?php if (is_null($event_id)) { ?>
		<B><?php echo $translator->trans("Schedule a new action"); ?></B>
		<?php } else { ?>
		<B><?php echo $translator->trans("Edit"); ?></B>
		<?php } ?>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php echo $Pub; ?>">
<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php echo $Issue; ?>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php echo $Language; ?>">
<?php if (!is_null($event_id)) { ?>
<input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
<?php } ?>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Date"); ?>:</TD>
	<TD>
		<?php $now = getdate(); ?>
		<input type="text" class="input_text date minDate_0" name="publish_date" id="publish_date" maxlength="10" size="11" value="<?php p($publish_date); ?>" alt="date|yyyy/mm/dd|-|4|<?php echo $now["year"]."/".$now["mon"]."/".$now["mday"]; ?>" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => "'".$translator->trans('Date')."'")); ?> <?php echo $translator->trans("The date must be in the future."); ?>" />
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("Time"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="publish_hour" SIZE="2" MAXLENGTH="2" VALUE="<?php p($publish_hour); ?>" alt="number|0|0|23" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => "'".$translator->trans('Time')."'")); ?>"> :
	<INPUT TYPE="TEXT" class="input_text" NAME="publish_min" SIZE="2" MAXLENGTH="2" VALUE="<?php p($publish_min); ?>" alt="number|0|0|59" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => "'".$translator->trans('Time')."'")); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("Action"); ?>:</TD>
	<TD>
	<SELECT NAME="action" class="input_select" alt="select" emsg="<?php echo $translator->trans('You must select an action.'); ?>">
		<OPTION VALUE=" ">---</OPTION>
		<OPTION VALUE="P" <?php if ($action == "P") echo "SELECTED"; ?>><?php echo $translator->trans("Publish"); ?></OPTION>
		<OPTION VALUE="U" <?php if ($action == "U") echo "SELECTED"; ?>><?php echo $translator->trans("Unpublish"); ?></OPTION>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("Publish all articles", array(), 'issues'); ?>:</TD>
	<TD>
	<SELECT NAME="publish_articles" class="input_select">
		<OPTION VALUE="Y" <?php if ($publish_articles == "Y") echo "SELECTED"; ?>><?php echo $translator->trans("Yes"); ?></OPTION>
		<OPTION VALUE="N" <?php if ($publish_articles == "N") echo "SELECTED"; ?>><?php echo $translator->trans("No"); ?></OPTION>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php echo $translator->trans('Save'); ?>">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>

<?php camp_html_copyright_notice(); ?>
