<?php
define("STATUS_APPROVED","approved");
define("STATUS_HIDDEN","hidden");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_article_author = Input::Get('f_article_author', 'array', array(), true);
$f_article_author_type = Input::Get('f_article_author_type', 'array', array(), true);
$f_on_front_page = Input::Get('f_on_front_page', 'string', '', true);
$f_on_section_page = Input::Get('f_on_section_page', 'string', '', true);
$f_is_public = Input::Get('f_is_public', 'string', '', true);
$f_keywords = Input::Get('f_keywords');
$f_article_title = Input::Get('f_article_title');
$f_message = Input::Get('f_message', 'string', '', true);
$f_creation_date = Input::Get('f_creation_date');
$f_publish_date = Input::Get('f_publish_date');
$f_comment_status = Input::Get('f_comment_status', 'string', '', true);
if (isset($_REQUEST['save_and_close'])) {
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
$dbColumns = $articleTypeObj->getUserDefinedColumns(false, true);

$articleFields = array();
foreach ($dbColumns as $dbColumn) {
    if ($dbColumn->getType() == ArticleTypeField::TYPE_BODY) {
        $dbColumnParam = $dbColumn->getName() . '_' . $f_article_number;
    } else {
        $dbColumnParam = $dbColumn->getName();
    }
    if (isset($_REQUEST[$dbColumnParam])) {
        if($dbColumn->getType() == ArticleTypeField::TYPE_TEXT
            && $dbColumn->getMaxSize()!=0
            && $dbColumn->getMaxSize()!='') {
                $articleFields[$dbColumn->getName()] = substr(trim(Input::Get($dbColumnParam)), 0, $dbColumn->getMaxSize());
            }
        else
    	    $articleFields[$dbColumn->getName()] = trim(Input::Get($dbColumnParam));
    } else {
    	$articleFields[$dbColumn->getName()] = null;
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

// Update the article author
if (!empty($f_article_author)) {
    ArticleAuthor::OnArticleLanguageDelete($articleObj->getArticleNumber(), $articleObj->getLanguageId());
    $i = 0;
    foreach ($f_article_author as $author) {
        $authorObj = new Author($author);
        if (!$authorObj->exists()  && strlen(trim($author)) > 0) {
            $authorData = Author::ReadName($author);
            $authorObj->create($authorData);
        }
        // Sets the author type selected
        $author_type = $f_article_author_type[$i];
        $authorObj->setType($author_type);
        // Links the author to the article
        $articleAuthorObj = new ArticleAuthor($articleObj->getArticleNumber(),
                                              $articleObj->getLanguageId(),
                                              $authorObj->getId(), $author_type);
        if (!$articleAuthorObj->exists()) {
            $articleAuthorObj->create();
        }
        $i++;
    }
}

// Update the article.
$articleObj->setOnFrontPage(!empty($f_on_front_page));
$articleObj->setOnSectionPage(!empty($f_on_section_page));
$articleObj->setIsPublic(!empty($f_is_public));
$articleObj->setKeywords($f_keywords);
$articleObj->setTitle($f_article_title);
$articleObj->setIsIndexed(false);
if (!empty($f_comment_status)) {
    if ($f_comment_status == "enabled" || $f_comment_status == "locked") {
        $commentsEnabled = true;
    } else {
        $commentsEnabled = false;
    }
    // If status has changed, then you need to show/hide all the comments
    // as appropriate.
    if ($articleObj->commentsEnabled() != $commentsEnabled) {
	    $articleObj->setCommentsEnabled($commentsEnabled);
        global $controller;
        $repository = $controller->getHelper('entity')->getRepository('Newscoop\Entity\Comment');
	    $repository->setArticleStatus($f_article_number, $f_language_selected, $commentsEnabled?STATUS_APPROVED:STATUS_HIDDEN);
	    $repository->flush();
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
	$articleTypeObj->setProperty($dbColumnName, $text);
}

$articleObj->setIsLocked(false);

Log::ArticleMessage($articleObj, getGS('Content edited'), $g_user->getUserId(), 37);

if ($f_save_button == "save") {
	camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'edit.php'), false);
} elseif ($f_save_button == "save_and_close") {
	if ($f_publication_id > 0) {
		camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'index.php'), false);
	} else {
		camp_html_goto_page("/$ADMIN/", false);
	}
}

ArticleIndex::RunIndexer(3, 10, true);

?>
