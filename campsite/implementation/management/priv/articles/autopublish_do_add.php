<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticlePublish.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission("Publish")) {
	camp_html_display_error(getGS("You do not have the right to schedule issues or articles for automatic publishing."));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_publish_date = trim(Input::Get('f_publish_date', 'string', '', true));
$f_publish_hour = trim(Input::Get('f_publish_hour', 'int', '', true));
$f_publish_minute = trim(Input::Get('f_publish_minute', 'int', '', true));
$f_publish_action = Input::Get('f_publish_action', 'string', '', true);
$f_front_page_action = Input::Get('f_front_page_action', 'string', '', true);
$f_section_page_action = Input::Get('f_section_page_action', 'string', '', true);
$f_article_code = Input::Get('f_article_code', 'array', 0);
// "mode" can be "multi" or "single"
$f_mode = Input::Get('f_mode', 'string', 'single', true);

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


$correct = true;
$created = false;
$errorMsgs = array();
if ($f_publish_date == "") {
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('Date').'</B>' );
	$correct = false;
}

if ( ($f_publish_hour == "") || ($f_publish_minute == "") ) {
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('Time').'</B>' );
	$correct = false;
}

if ( ($f_publish_action != "P") && ($f_publish_action != "U")
	 && ($f_front_page_action != "S") && ($f_front_page_action != "R")
	 && ($f_section_page_action != "S") && ($f_section_page_action != "R") ) {
	$errorMsgs[] = getGS('You must select an action.');
	$correct = false;
}

if ( (count($articles) == 0) && (count($errorArticles) > 0) ) {
	$errorMsgs[] = getGS("The article is new; it is not possible to schedule it for automatic publishing.");
	$correct = false;
}

if ($correct) {
	$publishTime = $f_publish_date . " " . $f_publish_hour . ":" . $f_publish_minute . ":00";
	foreach ($articles as $tmpArticle) {
		$articlePublishObj =& new ArticlePublish();
		$articlePublishObj->create();
		$articlePublishObj->setArticleNumber($tmpArticle->getArticleNumber());
		$articlePublishObj->setLanguageId($tmpArticle->getLanguageId());
		$articlePublishObj->setActionTime($publishTime);
		$created = true;
		if ($f_publish_action == "P" || $f_publish_action == "U") {
			$articlePublishObj->setPublishAction($f_publish_action);
		}
		if ($f_front_page_action == "S" || $f_front_page_action == "R") {
			$articlePublishObj->setFrontPageAction($f_front_page_action);
		}
		if ($f_section_page_action == "S" || $f_section_page_action == "R") {
			$articlePublishObj->setSectionPageAction($f_section_page_action);
		}
	}
	if ($f_mode == "multi") {
		$args = $_REQUEST;
		unset($args["f_article_code"]);
		$argsStr = camp_implode_keys_and_values($args, "=", "&");
		$url = "Location: /$ADMIN/articles/index.php?".$argsStr;
		header($url);
	} else {
		?>
		<script>
		window.opener.document.forms.article_edit.onsubmit();
		window.opener.document.forms.article_edit.submit();
		window.close();
		</script>
		<?php
	}
	exit;
}
?>
<html>
<head>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<title><?php putGS("Schedule a new action"); ?></title>
</head>
<body>
<br>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Schedule a new action"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?php
		foreach ($errorMsgs as $errorMsg) { ?>
			<li><?php p($errorMsg); ?></li>
			<?php
		}
		?>
		</BLOCKQUOTE>
	</TD>
	</TR>
	<TR>
		<TD COLSPAN="2" align="center">
			<INPUT TYPE="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="history.back();" class="button">
		</TD>
	</TR>
</TABLE>
<P>
