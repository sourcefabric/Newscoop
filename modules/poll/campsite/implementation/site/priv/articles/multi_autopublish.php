<?PHP
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticlePublish.php');

if (!$g_user->hasPermission("Publish")) {
	camp_html_display_error(getGS("You do not have the right to schedule issues or articles for automatic publishing."));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_code = Input::Get('f_article_code', 'array', 0);

// Get all the articles.
$articles = array();
$errorArticles = array();
foreach ($f_article_code as $code) {
	list($articleId, $languageId) = split("_", $code);
	$tmpArticle =& new Article($languageId, $articleId);
	if ($tmpArticle->getWorkflowStatus() != 'N') {
		$articles[] = $tmpArticle;
	}
	else {
		$errorArticles[] = $tmpArticle;
	}
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;
}

$publicationObj =& new Publication($f_publication_id);
if (!$publicationObj->exists()) {
	camp_html_display_error(getGS('Publication does not exist.'));
	exit;
}

$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('Issue does not exist.'));
	exit;
}

$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
if (!$sectionObj->exists()) {
	camp_html_display_error(getGS('Section does not exist.'));
	exit;
}

$crumbs = array(getGS("Articles") => "/$ADMIN/articles/index.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id&f_language_selected=$f_language_selected");
$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj,
				  'Section' => $sectionObj);
camp_html_content_top(getGS("Article automatic publishing schedule"), $topArray, true, false, $crumbs);

?>
<style type="text/css">@import url(<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/calendar-system.css);</style>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/lang/calendar-<?php echo camp_session_get('TOL_Language', 'en'); ?>.js"></script>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/calendar-setup.js"></script>


<?php
if (count($errorArticles) > 0) {
	?>
	<p>
	<div class="page_title">
	<?php putGS("The following articles are new; it is not possible to schedule them for automatic publishing"); ?>:
	</div>
	<p>
	<table cellpadding="3" cellspacing="0" style="padding-left: 10px;">
	<tr class="table_list_header">
		<td><?php putGS("Name"); ?></td>
	</tr>
	<?php
	$color = 0;
	foreach ($errorArticles as $tmpArticle) { ?>
	<tr class="<?php if ($color) { ?>list_row_even<?php } else { ?>list_row_odd<?php } $color = !$color; ?>">
		<td><?php p($tmpArticle->getTitle()); ?></td>
	</tr>
	<?php } ?>
	</table>
	<?PHP
}

if (count($articles) > 0) {
?>
<P>
<FORM NAME="autopublish" METHOD="POST" ACTION="autopublish_do_add.php" >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Schedule a new action"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php echo $f_publication_id; ?>">
<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php echo $f_issue_number; ?>">
<INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php echo $f_section_number; ?>">
<?php foreach ($articles as $article) { ?>
<input type="hidden" name="f_article_code[]" value="<?php p($article->getArticleNumber()."_".$article->getLanguageId()); ?>">
<?php } ?>
<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php echo $f_language_id; ?>">
<INPUT TYPE="HIDDEN" NAME="f_language_selected" VALUE="<?php echo $f_language_selected; ?>">
<INPUT TYPE="HIDDEN" NAME="f_mode" VALUE="multi">
<TR>
	<TD valign="top" align="right" style="padding-top: 12px;">
		<?php putGS("Articles"); ?>:
	</TD>
	<TD>
		<table cellpadding="3" cellspacing="2">
		<?php
		foreach ($articles as $tmpArticle) { ?>
		<tr class="list_row_even">
			<td><?php p($tmpArticle->getTitle()); ?></td>
		</tr>
		<?php } ?>
		</table>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" valign="top" style="padding-top: 12px;"><?php  putGS("Date"); ?>:</TD>
	<TD>
		<div id="calendar-container"></div>
		<script type="text/javascript">
		function dateChanged(calendar) {
			// Beware that this function is called even if the end-user only
			// changed the month/year.  In order to determine if a date was
			// clicked you can use the dateClicked property of the calendar:
			if (calendar.dateClicked) {
			  // OK, a date was clicked, redirect to /yyyy/mm/dd/index.php
			  var y = calendar.date.getFullYear();
			  var m = calendar.date.getMonth()+1;     // integer, 0..11
			  var d = calendar.date.getDate();      // integer, 1..31
			  document.forms.autopublish.f_publish_date.value = y+"-"+m+"-"+d;
			}
		};

		Calendar.setup(
			{
			  flat         : "calendar-container", // ID of the parent element
			  flatCallback : dateChanged           // our callback function
			}
		);
		</script>
		<p>
		<input type="text" name="f_publish_date" value="" readonly class="input_text_disabled">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Time"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_publish_hour" SIZE="2" MAXLENGTH="2" VALUE="0" class="input_text"> :
	<INPUT TYPE="TEXT" NAME="f_publish_minute" SIZE="2" MAXLENGTH="2" VALUE="0" class="input_text">
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
		<OPTION VALUE="P"><?php putGS("Publish"); ?></OPTION>
		<OPTION VALUE="U"><?php putGS("Unpublish"); ?></OPTION>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Front page"); ?>:</TD>
	<TD>
	<SELECT NAME="f_front_page_action" class="input_select">
		<OPTION VALUE=" ">---</OPTION>
		<OPTION VALUE="S"><?php putGS("Show on front page"); ?></OPTION>
		<OPTION VALUE="R"><?php putGS("Remove from front page"); ?></OPTION>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Section page"); ?>:</TD>
	<TD>
	<SELECT NAME="f_section_page_action" class="input_select">
		<OPTION VALUE=" ">---</OPTION>
		<OPTION VALUE="S"><?php putGS("Show on section page"); ?></OPTION>
		<OPTION VALUE="R"><?php putGS("Remove from section page"); ?></OPTION>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save'); ?>" class="button">
	</TD>
</TR>
</TABLE>
</FORM>
</P>
<?php
}

camp_html_copyright_notice();
?>