<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticlePublish.php');

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission("Publish")) {
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
if ($f_mode == "multi") {
	$args = $_REQUEST;
	$argsStr = camp_implode_keys_and_values($args, "=", "&");
	$backLink = "/$ADMIN/articles/multi_autopublish.php?".$argsStr;
} else {
	$args = $_REQUEST;
	unset($args["f_article_code"]);
	$argsStr = camp_implode_keys_and_values($args, "=", "&");
	$backLink = "/$ADMIN/articles/autopublish.php?".$argsStr;
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;
}

// Get all the articles.
$articles = array();
$errorArticles = array();
foreach ($f_article_code as $code) {
	list($articleId, $languageId) = explode("_", $code);
	$tmpArticle = new Article($languageId, $articleId);
	if ($tmpArticle->getWorkflowStatus() != 'N') {
		$articles[] = $tmpArticle;
	}
	else {
		$errorArticles[] = $tmpArticle;
	}
}

$publicationObj = new Publication($f_publication_id);
if (!$publicationObj->exists()) {
	camp_html_display_error(getGS('Publication does not exist.'));
	exit;
}

$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('Issue does not exist.'));
	exit;
}

$sectionObj = new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
if (!$sectionObj->exists()) {
	camp_html_display_error(getGS('Section does not exist.'));
	exit;
}

if ($f_publish_date == "") {
	camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('Date').'</B>' ));
}

if ( ($f_publish_hour == "") || ($f_publish_minute == "") ) {
	camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('Time').'</B>' ));
}

if ( ($f_publish_action != "P") && ($f_publish_action != "U")
	 && ($f_front_page_action != "S") && ($f_front_page_action != "R")
	 && ($f_section_page_action != "S") && ($f_section_page_action != "R") ) {
	camp_html_add_msg(getGS('You must select an action.'));
}

if ( (count($articles) == 0) && (count($errorArticles) > 0) ) {
	camp_html_add_msg(getGS("The article is new; it is not possible to schedule it for automatic publishing."));
}




if (camp_html_has_msgs()) {
	camp_html_goto_page($backLink);
}

$publishTime = $f_publish_date . " " . $f_publish_hour . ":" . $f_publish_minute . ":00";

foreach ($articles as $tmpArticle) {


	$articleEvents = ArticlePublish::GetArticleEvents($tmpArticle->getArticleNumber(), $f_language_selected, TRUE);
	foreach($articleEvents as $event) {
        if ( $event->getActionTime() == $publishTime ) {
            if ( $f_publish_action != $event->getPublishAction() ) {
            	?>
			    <script type="text/javascript">
			    try {
			        parent.$.fancybox.reload = true;
			        parent.$.fancybox.message = '<?php putGS('You can not schedule opposing events at the same time'); ?>';
			        parent.$.fancybox.close();
			    } catch (e) {
			    }
			    </script>
			    <?php
            	exit();
            }
        }
	}


	$articlePublishObj = new ArticlePublish();
	$articlePublishObj->create();
	$articlePublishObj->setArticleNumber($tmpArticle->getArticleNumber());
	$articlePublishObj->setLanguageId($tmpArticle->getLanguageId());
	$articlePublishObj->setActionTime($publishTime);
	if ($f_publish_action == "P" || $f_publish_action == "U") {
		$articlePublishObj->setPublishAction($f_publish_action);
	}
	if ($f_front_page_action == "S" || $f_front_page_action == "R") {
		$articlePublishObj->setFrontPageAction($f_front_page_action);
	}
	if ($f_section_page_action == "S" || $f_section_page_action == "R") {
		$articlePublishObj->setSectionPageAction($f_section_page_action);
	}
	Log::ArticleMessage($tmpArticle, getGS('Scheduled action added'), $g_user->getUserId(), 37);
}


if ($f_mode == "multi") {
	$args = $_REQUEST;
	unset($args["f_article_code"]);
	$argsStr = camp_implode_keys_and_values($args, "=", "&");
	camp_html_add_msg(getGS("Scheduled action added."), "ok");
	camp_html_goto_page("/$ADMIN/articles/index.php?".$argsStr);
} else {
	?>
	<script type="text/javascript">
    try {
        parent.$.fancybox.reload = true;
        parent.$.fancybox.message = '<?php putGS('Actions updated.'); ?>';
        parent.$.fancybox.close();
    } catch (e) {
    }
	</script>
	<?php
}
?>
