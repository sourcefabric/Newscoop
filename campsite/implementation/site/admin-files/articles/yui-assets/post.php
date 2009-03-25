<?php
header('Content-Type: application/json');

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/ArticleComment.php");

require_once('JSON.php');

$json = new Services_JSON();

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_content_lib.php");


$f_save = Input::Get('f_save', 'string', '', true);

$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);

$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_article_author = Input::Get('f_article_author', 'string', '');
$f_on_front_page = Input::Get('f_on_front_page', 'string', '', true);
$f_on_section_page = Input::Get('f_on_section_page', 'string', '', true);
$f_is_public = Input::Get('f_is_public', 'string', '', true);
$f_keywords = Input::Get('f_keywords');
$f_article_title = Input::Get('f_article_title');
$f_message = Input::Get('f_message', 'string', '', true);
$f_creation_date = Input::Get('f_creation_date');
$f_publish_date = Input::Get('f_publish_date');
$f_comment_status = Input::Get('f_comment_status', 'string', '', true);

$data = new stdclass();
$data->Results = new stdclass();
$data->Results->f_publication_id = $f_publication_id;
$data->Results->f_issue_number = $f_issue_number;
$data->Results->f_section_number = $f_section_number;
$data->Results->f_language_id = $f_language_id;
$data->Results->f_language_selected = $f_language_selected;
$data->Results->f_article_number = $f_article_number;
$data->Results->f_article_author = $f_article_author;
$data->Results->f_on_front_page = $f_on_front_page;
$data->Results->f_on_section_page = $f_on_section_page;
$data->Results->f_is_public = $f_is_public;
$data->Results->f_keywords = $f_keywords;
$data->Results->f_article_title = $f_article_title;
$data->Results->f_message = $f_message;
$data->Results->f_creation_date = $f_creation_date;
$data->Results->f_publish_date = $f_publish_date;
$data->Results->f_comment_status = $f_comment_status;
$data->Results->f_save = $f_save;

if ($f_save == 'close') {
	$f_save_button = 'save_and_close';
	$BackLink = "/$ADMIN/articles/index.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_language_id=$f_language_id&f_section_number=$f_section_number";
} else {
	$f_save_button = 'save';
	$BackLink = "/$ADMIN/";
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;
}

// Fetch article
$articleObj = new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('No such article.'), $BackLink);
	exit;
}

$articleTypeObj = $articleObj->getArticleData();
$dbColumns = $articleTypeObj->getUserDefinedColumns();

$articleFields = array();
foreach ($dbColumns as $dbColumn) {
    if (stristr($dbColumn->getType(), "blob")) {
        $dbColumnParam = $dbColumn->getName() . '_' . $f_article_number;
    } else {
        $dbColumnParam = $dbColumn->getName();
    }
    if (isset($_REQUEST[$dbColumnParam])) {
        $articleFields[$dbColumn->getName()] = trim(Input::Get($dbColumnParam));
    }
}

if (!empty($f_message)) {
	camp_html_add_msg($f_message, "ok");
}

if (!$articleObj->userCanModify($g_user)) {
	camp_html_add_msg(getGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only be changed by authorized users."));
	camp_html_goto_page($BackLink);
	exit;
}
// Only users with a lock on the article can change it.
if ($articleObj->isLocked() && ($g_user->getUserId() != $articleObj->getLockedByUser())) {
	$diffSeconds = time() - strtotime($articleObj->getLockTime());
	$hours = floor($diffSeconds/3600);
	$diffSeconds -= $hours * 3600;
	$minutes = floor($diffSeconds/60);
	$lockUser = new User($articleObj->getLockedByUser());
	camp_html_add_msg(getGS('Could not save the article. It has been locked by $1 $2 hours and $3 minutes ago.', $lockUser->getRealName(), $hours, $minutes));
	camp_html_goto_page($BackLink);
	exit;
}

// Update the first comment if the article title has changed
if ($f_article_title != $articleObj->getTitle()) {
	$firstPostId = ArticleComment::GetCommentThreadId($articleObj->getArticleNumber(), $articleObj->getLanguageId());
	if ($firstPostId) {
		$firstPost = new Phorum_message($firstPostId);
		$firstPost->setSubject($f_article_title);
	}
}

// Update the article author
if ($f_save == 'f_article_author' || $f_save == 'all') {
    $authorObj = new Author($f_article_author);
    if (!$authorObj->exists()) {
        $authorData = Author::ReadName($f_article_author);
	$authorObj->create($authorData);
    }
    $articleObj->setAuthorId($authorObj->getId());
}

// Update the article.
if ($f_save == 'all') {
    $articleObj->setOnFrontPage(!empty($f_on_front_page));
    $articleObj->setOnSectionPage(!empty($f_on_section_page));
    $articleObj->setIsPublic(!empty($f_is_public));
}
if ($f_save == 'f_keywords' || $f_save == 'all') {
    $articleObj->setKeywords($f_keywords);
}
if ($f_save == 'f_article_title' || $f_save == 'all') {
    $articleObj->setTitle($f_article_title);
}
$articleObj->setIsIndexed(false);

if ($f_save == 'all' && !empty($f_comment_status)) {
    if ($f_comment_status == "enabled" || $f_comment_status == "locked") {
        $commentsEnabled = true;
    } else {
        $commentsEnabled = false;
    }
    // If status has changed, then you need to show/hide all the comments
    // as appropriate.
    if ($articleObj->commentsEnabled() != $commentsEnabled) {
	    $articleObj->setCommentsEnabled($commentsEnabled);
		$comments = ArticleComment::GetArticleComments($f_article_number, $f_language_selected);
		if ($comments) {
			foreach ($comments as $comment) {
				$comment->setStatus($commentsEnabled?PHORUM_STATUS_APPROVED:PHORUM_STATUS_HIDDEN);
			}
		}
    }
    $articleObj->setCommentsLocked($f_comment_status == "locked");
}

// Make sure that the time stamp is updated.
$articleObj->setProperty('time_updated', 'NOW()', true, true);

// Verify creation date is in the correct format.
// If not, dont change it.
if (preg_match("/\d{4}-\d{2}-\d{2}/", $f_creation_date)) {
	$articleObj->setCreationDate($f_creation_date);
}

// Verify publish date is in the correct format.
// If not, dont change it.
if (preg_match("/\d{4}-\d{2}-\d{2}/", $f_publish_date)) {
	$articleObj->setPublishDate($f_publish_date);
}

foreach ($articleFields as $dbColumnName => $text) {
    if ($f_save == $dbColumnName || $f_save == 'all') {
        // Replace <span class="subhead"> ... </span> with <!** Title> ... <!** EndTitle>
        $text = preg_replace_callback("/(<\s*span[^>]*class\s*=\s*[\"']campsite_subhead[\"'][^>]*>|<\s*span|<\s*\/\s*span\s*>)/i", "TransformSubheads", $text);

		// Replace <a href="campsite_internal_link?IdPublication=1&..." ...> ... </a>
		// with <!** Link Internal IdPublication=1&...> ... <!** EndLink>
		$text = preg_replace_callback("/(<\s*a\s*(((href\s*=\s*[\"']campsite_internal_link[?][\w&=;]*[\"'])|(target\s*=\s*['\"][_\w]*['\"]))[\s]*)*[\s\w\"']*>)|(<\s*\/a\s*>)/i", "TransformInternalLinks", $text);

		// Replace <img id=".." src=".." alt=".." title=".." align="..">
		// with <!** Image [image_template_id] align=".." alt=".." sub="..">
	    $idAttr = "(id\s*=\s*\"[^\"]*\")";
		$srcAttr = "(src\s*=\s*\"[^\"]*\")";
		$altAttr = "(alt\s*=\s*\"[^\"]*\")";
	    $subAttr = "(title\s*=\s*\"[^\"]*\")";
		$alignAttr = "(align\s*=\s*\"[^\"]*\")";
        $widthAttr = "(width\s*=\s*\"[^\"]*\")";
        $heightAttr = "(height\s*=\s*\"[^\"]*\")";
        $otherAttr = "(\w+\s*=\s*\"[^\"]*\")*";
	    $pattern = "/<\s*img\s*(($idAttr|$srcAttr|$altAttr|$subAttr|$alignAttr|$widthAttr|$heightAttr|$otherAttr)\s*)*\/>/i";
		$text = preg_replace_callback($pattern, "TransformImageTags", $text);
		$articleTypeObj->setProperty($dbColumnName, $text);
    }
}

$logtext = getGS('Article content edited for "$1" (Publication: $2, Issue: $3, Section: $4, Language: $5)', $articleObj->getTitle(), $articleObj->getPublicationId(), $articleObj->getIssueNumber(), $articleObj->getSectionNumber(), $articleObj->getLanguageId());
Log::Message($logtext, $g_user->getUserId(), 37);

echo($json->encode($data));

?>
