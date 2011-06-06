<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticlePublish.php');

if (!$g_user->hasPermission("Publish")) {
	camp_html_display_error(getGS("You do not have the right to schedule issues or articles for automatic publishing."));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;
}

if ($f_publication_id == 0 || $f_issue_number == 0 || $f_section_number == 0) {
	camp_html_display_error(getGS('You must set the publication, issue, and section for this article before you can schedule it for publishing.  Go to the "$1" menu and select "$2" to do this.', getGS("Actions")."...", getGS("Move")), null, true);
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('Article does not exist.'), null, true);
	exit;
}

$BackLink = camp_html_article_url($articleObj, $f_language_id, "edit.php");

$publicationObj = new Publication($f_publication_id);
if (!$publicationObj->exists()) {
	camp_html_display_error(getGS('Publication does not exist.'), $BackLink, true);
	exit;
}

$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('Issue does not exist.'), $BackLink);
	exit;
}

$sectionObj = new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
if (!$sectionObj->exists()) {
	camp_html_display_error(getGS('Section does not exist.'), $BackLink, true);
	exit;
}


$articleEvents = ArticlePublish::GetArticleEvents($f_article_number, $f_language_selected);

$publishTime = date("Y-m-d H:i");
if ($articleObj->getWorkflowStatus() != 'N') {
	$publishAction = '';
	$frontPageAction = '';
	$sectionPageAction = '';
	$datetime = explode(" ", trim($publishTime));
	$publishDate = $datetime[0];
	$publishTime = explode(":", trim($datetime[1]));
	$publishHour = $publishTime[0];
	$publishMinute = $publishTime[1];
?>

<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Expires" content="now" />
  <title><?php putGS("Attach Topic To Article"); ?></title>

  <?php include dirname(__FILE__) . '/../html_head.php'; ?>
</head>
<body>

<FORM NAME="autopublish" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/articles/autopublish_do_add.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php echo $f_publication_id; ?>">
<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php echo $f_issue_number; ?>">
<INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php echo $f_section_number; ?>">
<INPUT TYPE="HIDDEN" NAME="f_article_number" VALUE="<?php echo $f_article_number; ?>">
<INPUT TYPE="hidden" NAME="f_article_code[]" VALUE="<?php echo $f_article_number.'_'.$f_language_selected; ?>">
<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php echo $f_language_id; ?>">
<INPUT TYPE="HIDDEN" NAME="f_language_selected" VALUE="<?php echo $f_language_selected; ?>">
<INPUT type="hidden" name="f_backlink" value="<?php echo $BackLink; ?>">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php putGS("Schedule a new action"); ?></B>
            <HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Date"); ?>:</TD>
	<TD>
		<?php $now = getdate(); ?>
		<input type="text" name="f_publish_date" value="" class="input_text date minDate_0" size="10" alt="date|yyyy/mm/dd|-|4|<?php echo $now["year"]."/".$now["mon"]."/".$now["mday"]; ?>" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('Date')."'"); ?> <?php putGS("The date must be in the future."); ?>" />
        <script type="text/javascript">
        <!--
        $(document).ready(function() {
            $('input[name=f_publish_date]').focus();
        });
        -->
        </script>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Time"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_publish_hour" SIZE="2" MAXLENGTH="2" VALUE="<?php p($publishHour); ?>" class="input_text" alt="number|0|0|23" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('Time')."'"); ?>"> :
	<INPUT TYPE="TEXT" NAME="f_publish_minute" SIZE="2" MAXLENGTH="2" VALUE="<?php p($publishMinute); ?>" class="input_text" alt="number|0|0|59" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('Time')."'"); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="CENTER" COLSPAN="2"><b><?php  putGS("Actions"); ?></b></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Publish"); ?>:</TD>
	<TD>
	<SELECT NAME="f_publish_action" class="input_select">
		<OPTION VALUE=" ">---</OPTION>
		<OPTION VALUE="P" <?php if ($publishAction == "P") echo "SELECTED"; ?>><?php putGS("Publish"); ?></OPTION>
		<OPTION VALUE="U" <?php if ($publishAction == "U") echo "SELECTED"; ?>><?php putGS("Unpublish"); ?></OPTION>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Front page"); ?>:</TD>
	<TD>
	<SELECT NAME="f_front_page_action" class="input_select">
		<OPTION VALUE=" ">---</OPTION>
		<OPTION VALUE="S" <?php if ($frontPageAction == "S") echo "SELECTED"; ?>><?php putGS("Show on front page"); ?></OPTION>
		<OPTION VALUE="R" <?php if ($frontPageAction == "R") echo "SELECTED"; ?>><?php putGS("Remove from front page"); ?></OPTION>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Section page"); ?>:</TD>
	<TD>
	<SELECT NAME="f_section_page_action" class="input_select">
		<OPTION VALUE=" ">---</OPTION>
		<OPTION VALUE="S" <?php if ($sectionPageAction == "S") echo "SELECTED"; ?>><?php putGS("Show on section page"); ?></OPTION>
		<OPTION VALUE="R" <?php if ($sectionPageAction == "R") echo "SELECTED"; ?>><?php putGS("Remove from section page"); ?></OPTION>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
	<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save'); ?>" class="button">
	&nbsp;&nbsp;
        <input type="submit" value="<?php putGS("Close"); ?>" class="button" onclick="parent.$.fancybox.close(); return false;" />
	</TD>
</TR>
</TABLE>
</FORM>
<?php } else { ?>
	<BLOCKQUOTE>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Scheduling a new publish action"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><?php putGS("The article is new; it is not possible to schedule it for automatic publishing.");?></BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2" align="center">
			<INPUT TYPE="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/articles/edit.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_article_number=<?php p($f_article_number); ?>&f_language_id=<?php p($f_language_id); ?>&f_language_selected=<?php p($f_language_selected); ?>'" class="button">
		</TD>
	</TR>
	</TABLE>
	</BLOCKQUOTE>
<?php } ?>
</body>
</html>
