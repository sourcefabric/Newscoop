<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/editor_load_tinymce.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/DbReplication.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticlePublish.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAttachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleImage.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTopic.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAudioclip.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ShortURL.php');
camp_load_translation_strings("article_comments");
if (SystemPref::Get('UseCampcasterAudioclips') == 'Y') {
	camp_load_translation_strings("article_audioclips");
}
camp_load_translation_strings("api");

// These are optional, depending on whether you are in a section
// or whether editing an article that doesnt have a location.
$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);

$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_unlock = Input::Get('f_unlock', 'string', false, true);

// Saved session values
// $f_edit_mode can be "view" or "edit"
$f_edit_mode = camp_session_get('f_edit_mode', 'edit');
// Whether to show comments at the bottom of the article
// (you may not want to show them to speed up your loading time)
$f_show_comments = camp_session_get('f_show_comments', 1);
// Selected language of the article
$f_language_selected = (int)camp_session_get('f_language_selected', 0);

if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
    exit;
}

// Fetch article
$articleObj = new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
    camp_html_display_error(getGS('No such article.'));
    exit;
}
$articleAuthorObj = new Author($articleObj->getAuthorId());

$articleData = $articleObj->getArticleData();
// Get article type fields.
$dbColumns = $articleData->getUserDefinedColumns(false, true);
$articleType = new ArticleType($articleObj->getType());

$articleImages = ArticleImage::GetImagesByArticleNumber($f_article_number);
$lockUserObj = new User($articleObj->getLockedByUser());
$articleCreator = new User($articleObj->getCreatorId());
$articleEvents = ArticlePublish::GetArticleEvents($f_article_number, $f_language_selected, true);
$articleTopics = ArticleTopic::GetArticleTopics($f_article_number);
$articleFiles = ArticleAttachment::GetAttachmentsByArticleNumber($f_article_number, $f_language_selected);
$articleAudioclips = ArticleAudioclip::GetAudioclipsByArticleNumber($f_article_number, $f_language_selected);
$articleLanguages = $articleObj->getLanguages();

// Create displayable "last modified" time.
$lastModified = strtotime($articleObj->getLastModified());
$today = getdate();
$savedOn = getdate($lastModified);
$savedToday = true;
if ($today['year'] != $savedOn['year'] || $today['mon'] != $savedOn['mon'] || $today['mday'] != $savedOn['mday']) {
    $savedToday = false;
}

$showComments = false;
$showCommentControls = false;
if ($f_publication_id > 0) {
    $publicationObj = new Publication($f_publication_id);
    $issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
    $sectionObj = new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
    $languageObj = new Language($articleObj->getLanguageId());

    $showCommentControls = ($publicationObj->commentsEnabled() && $articleType->commentsEnabled());
    $showComments = $showCommentControls && $articleObj->commentsEnabled();
}

if ($showComments) {
    require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleComment.php');
    if (SystemPref::Get("UseDBReplication") == 'Y') {
        $dbReplicationObj = new DbReplication();
        $connectedToOnlineServer = $dbReplicationObj->connect();
        if ($connectedToOnlineServer == true) {
            // Fetch the comments attached to this article
            // (from replication database)
            $comments = ArticleComment::GetArticleComments($f_article_number, $f_language_id);
        }
    } else {
        // Fetch the comments attached to this article
        // (from local database)
        $comments = ArticleComment::GetArticleComments($f_article_number, $f_language_id);
    }
}

// Automatically switch to "view" mode if user doesnt have permissions.
if (!$articleObj->userCanModify($g_user)) {
    $f_edit_mode = "view";
}

//
// Automatic unlocking
//
$locked = true;
// If the article hasnt been touched in 24 hours
$timeDiff = camp_time_diff_str($articleObj->getLockTime());
if ($timeDiff['days'] > 0) {
    $articleObj->setIsLocked(false);
    $locked = false;
}
// If the user who locked the article doesnt exist anymore, unlock the article.
elseif (($articleObj->getLockedByUser() != 0) && !$lockUserObj->exists()) {
    $articleObj->setIsLocked(false);
    $locked = false;
}

//
// Automatic locking
//

// If the article has not been unlocked and is not locked by a user.
if ($f_unlock === false) {
    if (!$articleObj->isLocked()) {
        // Lock the article
        $articleObj->setIsLocked(true, $g_user->getUserId());
    }
} else {
    $f_edit_mode = "view";
}

// Automatically unlock the article is the user goes into VIEW mode
$lockedByCurrentUser = ($articleObj->getLockedByUser() == $g_user->getUserId());
if (($f_edit_mode == "view") && $lockedByCurrentUser) {
    $articleObj->setIsLocked(false);
}

// If the article is locked by the current user, OK to edit.
if ($lockedByCurrentUser) {
    $locked = false;
}

//
// Begin Display of page
//
include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

if ($f_edit_mode == "edit") {
    $title = getGS("Edit article");
} else {
    $title = getGS("View article");
}

if ($f_publication_id > 0) {
    $topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj,
		      'Section' => $sectionObj, 'Article'=>$articleObj);
    camp_html_content_top($title, $topArray);
} else {
    $crumbs = array();
    $crumbs[] = array(getGS("Actions"), "");
    $crumbs[] = array($title, "");
    echo camp_html_breadcrumbs($crumbs);
}

$hasArticleBodyField = false;
foreach ($dbColumns as $dbColumn) {
    if ($dbColumn->getType() == ArticleTypeField::TYPE_BODY) {
        $hasArticleBodyField = true;
    }
}

if (($f_edit_mode == "edit") && $hasArticleBodyField) {
    $languageSelectedObj = new Language($f_language_selected);
    $editorLanguage = camp_session_get('TOL_Language', $languageSelectedObj->getCode());
    editor_load_tinymce($dbColumns, $g_user, $f_article_number, $editorLanguage);
}

if ($g_user->hasPermission('EditorSpellcheckerEnabled')) {
    $spellcheck = 'spellcheck="true"';
} else {
    $spellcheck = 'spellcheck="false"';
}

include ("edit_html.php");

if ($locked) {
	camp_html_copyright_notice();
	return;
}


$jsArrayFieldsStr = '';
for($i = 0; $i < sizeof($fCustomFields); $i++) {
    $jsArrayFieldsStr .= "'" . addslashes($fCustomFields[$i]) . "'";
    if ($i + 1 < sizeof($fCustomFields)) {
        $jsArrayFieldsStr .= ',';
    }
}
$jsArraySwitchesStr = '';
for($i = 0; $i < sizeof($fCustomSwitches); $i++) {
    $jsArraySwitchesStr .= "'" . addslashes($fCustomSwitches[$i]) . "'";
    if ($i + 1 < sizeof($fCustomSwitches)) {
        $jsArraySwitchesStr .= ',';
    }
}
$jsArrayTextareasStr = '';
for($i = 0; $i < sizeof($fCustomTextareas); $i++) {
    $jsArrayTextareasStr .= "'" . addslashes($fCustomTextareas[$i]) . "'";
    if ($i + 1 < sizeof($fCustomTextareas)) {
        $jsArrayTextareasStr .= ',';
    }
}

include ("edit_javascript.php");


if ($showComments && $f_show_comments) {
    include("comments/show_comments.php");
}

camp_html_copyright_notice();
?>
