<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/editor_load_tinymce.php");
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/editor_load_countable.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/DbReplication.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticlePublish.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAttachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleImage.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTopic.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ShortURL.php');

require_once WWW_DIR . '/classes/GeoMap.php';

camp_load_translation_strings("article_comments");
camp_load_translation_strings("api");

// These are optional, depending on whether you are in a section
// or whether editing an article that doesnt have a location.
$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);

$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_unlock = Input::Get('f_unlock', 'string', false, true);

// $f_edit_mode can be "view" or "edit"
$f_edit_mode = Input::Get('f_edit_mode', 'string', 'edit', true);

// Whether to show comments at the bottom of the article
// (you may not want to show them to speed up your loading time)
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

$articleData = $articleObj->getArticleData();
// Get article type fields.
$dbColumns = $articleData->getUserDefinedColumns(FALSE, TRUE);

$articleType = new ArticleType($articleObj->getType());

$articleImages = ArticleImage::GetImagesByArticleNumber($f_article_number);
$lockUserObj = new User($articleObj->getLockedByUser());
$articleCreator = new User($articleObj->getCreatorId());
$articleEvents = ArticlePublish::GetArticleEvents($f_article_number, $f_language_selected, TRUE);
$articleTopics = ArticleTopic::GetArticleTopics($f_article_number);
$articleFiles = ArticleAttachment::GetAttachmentsByArticleNumber($f_article_number, $f_language_selected);
$articleLanguages = $articleObj->getLanguages();

// Create displayable "last modified" time.
$lastModified = strtotime($articleObj->getLastModified());
$today = getdate();
$savedOn = getdate($lastModified);
$savedToday = true;
if ($today['year'] != $savedOn['year'] || $today['mon'] != $savedOn['mon'] || $today['mday'] != $savedOn['mday']) {
    $savedToday = FALSE;
}

$showComments = FALSE;
$showCommentControls = FALSE;
if ($f_publication_id > 0) {
    $publicationObj = new Publication($f_publication_id);
    $issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
    $sectionObj = new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
    $languageObj = new Language($articleObj->getLanguageId());

    $showCommentControls = ($publicationObj->commentsEnabled() && $articleType->commentsEnabled());
    $showComments = $showCommentControls && $articleObj->commentsEnabled();
}

if ($showComments) {
//    $comments = array();

    /**
    * @todoget comments
    */
}

// Automatically switch to "view" mode if user doesnt have permissions
if (!$articleObj->userCanModify($g_user)) {
    $f_edit_mode = 'view';
}

//
// Automatic unlocking
//
$locked = TRUE;
// If the article has not been touched in 24 hours
$timeDiff = camp_time_diff_str($articleObj->getLockTime());
if ($timeDiff['days'] > 0) {
    $articleObj->setIsLocked(FALSE);
    $locked = FALSE;
} elseif (($articleObj->getLockedByUser() != 0) && !$lockUserObj->exists()) {
    // If the user who locked the article doesnt exist anymore, unlock the article
    $articleObj->setIsLocked(FALSE);
    $locked = FALSE;
}

//
// Automatic locking
//

// If the article has not been unlocked and is not locked by a user.
if ($f_unlock === FALSE) {
    if (!$articleObj->isLocked()) {
        // Lock the article
        $articleObj->setIsLocked(TRUE, $g_user->getUserId());
    }
} else {
    $f_edit_mode = 'view';
}

// Automatically unlock the article if the user goes into VIEW mode
$lockedByCurrentUser = ($articleObj->getLockedByUser() == $g_user->getUserId());
if (($f_edit_mode == 'view') && $lockedByCurrentUser) {
    $articleObj->setIsLocked(FALSE);
}

// If the article is locked by the current user, OK to edit.
if ($lockedByCurrentUser) {
    $locked = FALSE;
}

//
// Begin Display of page
//
include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

$inEditMode = ($f_edit_mode == 'edit');
$inViewMode = ($f_edit_mode == 'view');
$publishRights = $g_user->hasPermission('Publish');

if ($g_user->hasPermission('EditorSpellcheckerEnabled')) {
    $spellcheck = 'spellcheck="true"';
} else {
    $spellcheck = 'spellcheck="false"';
}

// Generate the breadcrumb
$title = '';
if ($f_publication_id > 0) {
    $topArray = array(
        'Pub' => $publicationObj,
        'Issue' => $issueObj,
        'Section' => $sectionObj,
        'Article' => $articleObj
    );
    camp_html_content_top($title, $topArray);
} else {
    $crumbs = array();
    $crumbs[] = array(getGS('Pending Article'), '');
    $crumbs[] = array($title, '');
    echo camp_html_breadcrumbs($crumbs);
}

function parseTextBody($text, $articleNumber)
{
    // Subheads
    $text = preg_replace("/<!\*\*\s*Title\s*>/i", "<span class=\"campsite_subhead\">", $text);
    $text = preg_replace("/<!\*\*\s*EndTitle\s*>/i", "</span>", $text);

    // Internal Links with targets
    $text = preg_replace("/<!\*\*\s*Link\s*Internal\s*([\w=&]*)\s*target[\s\"]*([\w_]*)[\s\"]*>/i", '<a href="/campsite/campsite_internal_link?$1" target="$2">', $text);

    // Internal Links without targets
    $text = preg_replace("/<!\*\*\s*Link\s*Internal\s*([\w=&]*)\s*>/i", '<a href="/campsite/campsite_internal_link?$1">', $text);

    // External Links (old style 2.1) with targets
    $text = preg_replace("/<!\*\*\s*Link\s*External[\s\"]*([^\s\"]*)[\s\"]*target[\s\"]*([\w_]*)[\s\"]*>/i", '<a href="$1" target="$2">', $text);

    // External Links (old style 2.1) without targets
    $text = preg_replace("/<!\*\*\s*Link\s*External[\s\"]*([^\s\"]*)[\s\"]*>/i", '<a href="$1">', $text);

    // End link
    $text = preg_replace("/<!\*\*\s*EndLink\s*>/i", "</a>", $text);
    // Images
    preg_match_all("/<!\*\*\s*Image\s*([\d]*)\s*/i",$text, $imageMatches);

    preg_match_all("/\s*sub=\"(.*?)\"/", $text, $titles);

    preg_match_all("/<!\*\*\s*Image\s*([\d]*)\s*(.*?)\s*ratio=\"(.*?)\"/", $text, $ratios);

    if (isset($imageMatches[1][0])) {
        if (isset($titles) && sizeof($titles) > 0) {
            for($x = 0; $x < sizeof($titles[0]); $x++) {
                $text = preg_replace("/\s*".preg_replace('~\/~', '\/',
                preg_quote($titles[0][$x]))."/", ' title="'.$titles[1][$x].'"', $text);
            }
        }

        $formattingErrors = FALSE;
        foreach ($imageMatches[1] as $templateId) {
            // Get the image URL
            $articleImage = new ArticleImage($articleNumber, NULL, $templateId);
            if (!$articleImage->exists()) {
                ArticleImage::RemoveImageTagsFromArticleText($articleNumber, $templateId);
                $formattingErrors = TRUE;
                continue;
            }
            $image = new Image($articleImage->getImageId());
            $imageUrl = $image->getImageUrl();
            unset($fakeTemplateId);
            if (isset($ratios) && sizeof($ratios) > 0) {
                $n = 0;
                foreach ($ratios[3] as $ratio) {
                    if ($ratios[1][$n++] == $templateId) {
                        $fakeTemplateId = $templateId.'_'.$ratio;
                    }
                }
            }
            if (!isset($fakeTemplateId)) {
                $fakeTemplateId = $templateId;
            }
            $text = preg_replace("/<!\*\*\s*Image\s*".$templateId."\s*/i", '<img src="'.$imageUrl.'" id="'.$fakeTemplateId.'" ', $text);
        }
        if ($formattingErrors) {
            print ('<script type="text/javascript">window.location.reload();</script>');
        }
    }
    return $text;
}

include_once('edit_html.php');

// Display tinymce loading code if required
$hasArticleBodyField = FALSE;
foreach ($dbColumns as $dbColumn) {
    if ($dbColumn->getType() == ArticleTypeField::TYPE_BODY) {
        $hasArticleBodyField = TRUE;
    }
}
if ($inEditMode && $hasArticleBodyField) {
    $languageSelectedObj = new Language($f_language_selected);
    $editorLanguage = !empty($_COOKIE['TOL_Language']) ? $_COOKIE['TOL_Language'] : $languageSelectedObj->getCode();
    editor_load_tinymce($dbColumns, $g_user, $f_article_number, $editorLanguage);
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

?>
