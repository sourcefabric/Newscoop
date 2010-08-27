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
?>
<!-- YUI dependencies -->
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/yahoo/yahoo-min.js"></script>
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/event/event-min.js"></script>
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/dom/dom-min.js"></script>
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/connection/connection-min.js"></script>
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/animation/animation-min.js"></script>
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/container/container.js"></script>

<!-- Autocomplete dependencies -->
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/datasource/datasource-min.js"></script>

<!-- Button dependencies -->
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/element/element-beta-min.js"></script>
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/button/button-min.js"></script>

<!-- Autocomplete Source file -->
<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/autocomplete/autocomplete-min.js"></script>

<!-- CSS file (default YUI Sam Skin) -->
<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/autocomplete/assets/skins/sam/autocomplete.css" />

<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/button/assets/skins/sam/button.css" />

<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/yui/build/container/assets/container.css" />

<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/admin/articles/yui-assets/styles.css" />


<?php
// If the article is locked.
if ($articleObj->userCanModify($g_user) && $locked && ($f_edit_mode == "edit")) {
?>
<p>
<table border="0" cellspacing="0" cellpadding="6" class="table_input">
<tr>
  <td colspan="2">
    <b><?php  putGS("Article is locked"); ?> </b>
    <hr noshade size="1" color="black">
  </td>
</tr>
<tr>
  <td colspan="2" align="center">
    <blockquote>
    <?php
        $timeDiff = camp_time_diff_str($articleObj->getLockTime());
        if ($timeDiff['hours'] > 0) {
            putGS('The article has been locked by $1 ($2) $3 hour(s) and $4 minute(s) ago.',
                  '<B>'.htmlspecialchars($lockUserObj->getRealName()),
                  htmlspecialchars($lockUserObj->getUserName()).'</B>',
                  $timeDiff['hours'], $timeDiff['minutes']);
        } else {
            putGS('The article has been locked by $1 ($2) $3 minute(s) ago.',
                  '<B>'.htmlspecialchars($lockUserObj->getRealName()),
                  htmlspecialchars($lockUserObj->getUserName()).'</B>',
                  $timeDiff['minutes']);
        }
    ?>
    <br/>
    </blockquote>
  </td>
</tr>
<tr>
  <td colspan="2">
    <div align="CENTER">
      <input type="button" name="Yes" value="<?php  putGS('Unlock'); ?>" class="button" onclick="location.href='<?php echo camp_html_article_url($articleObj, $f_language_id, "do_unlock.php", '', null, true); ?>'" />
      <input type="button" name="Yes" value="<?php  putGS('View'); ?>" class="button" onclick="location.href='<?php echo camp_html_article_url($articleObj, $f_language_id, "edit.php", "", "&f_edit_mode=view"); ?>'" />
      <input type="button" name="No" value="<?php  putGS('Cancel'); ?>" class="button" onclick="location.href='/<?php echo $ADMIN; ?>/articles/?f_publication_id=<?php  p($f_publication_id); ?>&f_issue_number=<?php  p($f_issue_number); ?>&f_language_id=<?php p($f_language_id); ?>&f_section_number=<?php  p($f_section_number); ?>'" />
    </div>
  </td>
</tr>
</table>
<p>
<?php
   return;
}

if ($f_edit_mode == "edit") { ?>
<style type="text/css">@import url(<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/calendar-system.css);</style>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/lang/calendar-<?php echo camp_session_get('TOL_Language', 'en'); ?>.js"></script>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/calendar-setup.js"></script>
<?php } // if edit mode ?>

<?php if ($f_publication_id > 0) { ?>
<table border="0" cellspacing="0" cellpadding="1" class="action_buttons" style="padding-top: 5px;">
<tr>
  <td><a href="<?php echo "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"; ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" border="0"></a></td>
  <td><a href="<?php echo "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"; ?>"><b><?php  putGS("Article List"); ?></b></a></td>

  <?php if ($g_user->hasPermission('AddArticle')) { ?>
  <td style="padding-left: 20px;"><a href="add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>" ><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" border="0"></a></td>
  <td><a href="add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>" ><b><?php  putGS("Add new article"); ?></b></a></td>
<?php } ?>
</tr>
</table>
<?php } ?>

<?php camp_html_display_msgs("0.25em", "0.25em"); ?>

<div id="yui-connection-container"></div>
<div id="yui-connection-message"></div>
<div id="yui-connection-error"></div>

<table border="0" cellspacing="1" cellpadding="0" class="table_input" width="900px" style="margin-top: 5px;">
<tr>
  <td width="700px" style="border-bottom: 1px solid #8baed1;" colspan="2">
    <!-- for the left side of the article edit screen -->
    <table cellpadding="0" cellspacing="0">
    <tr>
      <td width="100%" valign="middle">
        <!-- BEGIN the article control bar -->
        <form name="article_actions" action="do_article_action.php" method="POST">
		<?php echo SecurityToken::FormParameter(); ?>
        <input type="hidden" name="f_publication_id" id="f_publication_id" value="<?php  p($f_publication_id); ?>" />
        <input type="hidden" name="f_issue_number" id="f_issue_number" value="<?php  p($f_issue_number); ?>" />
        <input type="hidden" name="f_section_number" id="f_section_number" value="<?php  p($f_section_number); ?>" />
        <input type="hidden" name="f_language_id" id="f_language_id" value="<?php  p($f_language_id); ?>" />
        <input type="hidden" name="f_language_selected" id="f_language_selected" value="<?php  p($f_language_selected); ?>" />
        <input type="hidden" name="f_article_number" id="f_article_number" value="<?php  p($f_article_number); ?>" />
        <table border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td style="padding-left: 1em;">
            <script>
            function action_selected(dropdownElement) {
                // Get the index of the "delete" option.
                deleteOptionIndex = -1;
                for (var index = 0; index < dropdownElement.options.length; index++) {
                    if (dropdownElement.options[index].value == "delete") {
                        deleteOptionIndex = index;
                    }
                }

                // if the user has selected the "delete" option
                if (dropdownElement.selectedIndex == deleteOptionIndex) {
                    ok = confirm("<?php putGS("Are you sure you want to delete this article?"); ?>");
                    if (!ok) {
                        dropdownElement.options[0].selected = true;
                        return;
                    }
                }

                // do the action if it isnt the first or second option
                if ((dropdownElement.selectedIndex != 0) &&  (dropdownElement.selectedIndex != 1)) {
                    dropdownElement.form.submit();
                }
            }
            </script>
            <select name="f_action" class="input_select" onchange="action_selected(this);">
              <option value=""><?php putGS("Actions"); ?>...</option>
              <option value="">-----------</option>
              <?php if ($articleObj->userCanModify($g_user) && $articleObj->isLocked()) { ?>
              <option value="unlock"><?php putGS("Unlock"); ?></option>
              <?php } ?>

              <?php if ($g_user->hasPermission('DeleteArticle')) { ?>
              <option value="delete"><?php putGS("Delete"); ?></option>
              <?php } ?>

              <?php if ($g_user->hasPermission('AddArticle')) { ?>
              <option value="copy"><?php putGS("Duplicate"); ?></option>
              <?php } ?>

              <?php if ($g_user->hasPermission('TranslateArticle')) { ?>
              <option value="translate"><?php putGS("Translate"); ?></option>
              <?php } ?>

              <?php if ($g_user->hasPermission('MoveArticle')) { ?>
              <option value="move"><?php putGS("Move"); ?></option>
              <?php } ?>
            </select>
          </td>

          <!-- BEGIN Workflow -->
          <td style="padding-left: 1em;">
          <?php
          // Show a different menu depending on the rights of the user.
          if ($g_user->hasPermission("Publish")) { ?>
            <select name="f_action_workflow" class="input_select" onchange="this.form.submit();">
			<?php
			if (isset($issueObj) && $issueObj->isPublished()) {
				camp_html_select_option("Y", $articleObj->getWorkflowStatus(), getGS("Status") . ': ' . getGS("Published"));
			} else {
				camp_html_select_option("M", $articleObj->getWorkflowStatus(), getGS("Status") . ': ' . getGS("Publish with issue"));
			}
			camp_html_select_option("S", $articleObj->getWorkflowStatus(), getGS("Status") . ': ' . getGS("Submitted"));
			camp_html_select_option("N", $articleObj->getWorkflowStatus(), getGS("Status") . ': ' . getGS("New"));
			?>
            </select>
          <?php } elseif ($articleObj->userCanModify($g_user) && ($articleObj->getWorkflowStatus() != 'Y')) { ?>
            <select name="f_action_workflow" class="input_select" onchange="this.form.submit();">
              <?php
                  camp_html_select_option("S", $articleObj->getWorkflowStatus(), getGS("Status") . ': ' . getGS("Submitted"));
                  camp_html_select_option("N", $articleObj->getWorkflowStatus(), getGS("Status") . ': ' . getGS("New"));
              ?>
            </select>
          <?php } else {
              switch ($articleObj->getWorkflowStatus()) {
              case 'Y':
                  echo getGS("Status") . ': ' . getGS("Published");
                  break;
              case 'M':
                  echo getGS("Status") . ': ' . getGS("Publish with issue");
                  break;
              case 'S':
                  echo getGS("Status") . ': ' . getGS("Submitted");
                  break;
              case 'N':
                  echo getGS("Status") . ': ' . getGS("New");
                  break;
              }
          }
          if (count($articleEvents) > 0) {
          ?>
            <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/automatic_publishing.png" alt="<?php  putGS("Scheduled Publishing"); ?>" title="<?php  putGS("Scheduled Publishing"); ?>" border="0" width="22" height="22" align="middle" style="padding-bottom: 1px;">
          <?php
          }
          ?>
          </td>
          <!-- END Workflow -->

					<td style="padding-left: 1em;">
		        		<table border="0" cellspacing="0" cellpadding="3">
		        		<tr>
		        			<td><?php  putGS('Language'); ?>:</td>
		        			<td>
								<?php
								if (count($articleLanguages) > 1) {
				        		$languageUrl = "edit.php?f_publication_id=$f_publication_id"
				        			."&f_issue_number=$f_issue_number"
				        			."&f_section_number=$f_section_number"
				        			."&f_article_number=$f_article_number"
				        			."&f_language_id=$f_language_id"
				        			."&f_language_selected=";
		        				?>
		        				<select name="f_language_selected" class="input_select" onchange="dest = '<?php p($languageUrl); ?>'+this.options[this.selectedIndex].value; location.href=dest;">
		    					<?php
		    					foreach ($articleLanguages as $articleLanguage) {
		    					    camp_html_select_option($articleLanguage->getLanguageId(), $f_language_selected, htmlspecialchars($articleLanguage->getNativeName()));
		    					}
		        				?></select>
		        				<?php } else {
		        					$articleLanguage = camp_array_peek($articleLanguages);
		        					echo '<b>'.htmlspecialchars($articleLanguage->getNativeName()).'</b>';
		        				}
		        				?>

		        			</td>
		        		</tr>
		        		</table>
					</td>
				</tr>
				</table>
				</form>
				<!-- END the article control bar -->
			</td>

			<?php
			if ($articleObj->userCanModify($g_user)) {
			$switchModeUrl = camp_html_article_url($articleObj, $f_language_id, "edit.php")
				."&f_edit_mode=".( ($f_edit_mode =="edit") ? "view" : "edit");
			?>
			<td align="right" style="padding-top: 1px;" valign="top">
			     <table cellpadding="0" cellspacing="0" border="0">
			     <tr><td>
			     <input type="button" name="edit" value="<?php putGS("Edit"); ?>" <?php if ($f_edit_mode == "edit") {?> disabled class="button_disabled" <?php } else { ?> onclick="location.href='<?php p($switchModeUrl); ?>';" class="button" <?php } ?> />
			     </td>

			     <td style="padding-left: 5px; padding-right: 10px;">
			     <input type="button" name="edit" value="<?php putGS("View"); ?>" <?php if ($f_edit_mode == "view") {?> disabled class="button_disabled" <?php } else { ?> onclick="location.href='<?php p($switchModeUrl); ?>';" class="button" <?php } ?> />
			     </td>

			     <td nowrap>
			       <div id="yui-saved-box">
				 <div id="yui-connection-saved"></div>
				 <div id="yui-saved">
                                 <script>
				    var dateTime = '<?php if ($savedToday) { p(date("H:i:s", $lastModified)); } else { p(date("Y-m-d H:i", $lastModified)); } ?>';
			            if (document.getElementById('yui-connection-saved').value == undefined) {
				        document.write('<?php putGS("Saved:"); ?> ' + dateTime);
				    }
				 </script>
                                 </div>
                               </div>
			     </td>
			     </tr></table>
			</td>
			<?php } ?>
		</tr>
		</table>
	</td>
</tr>

<tr>
	<td valign="top">
	<!-- BEGIN article content -->
	<form name="article_edit" action="do_edit.php" method="POST">
	<?php echo SecurityToken::FormParameter(); ?>
	<fieldset id="pushbuttonsfrommarkup" class=" yui-skin-sam">
	<input type="hidden" name="f_publication_id" value="<?php  p($f_publication_id); ?>" />
	<input type="hidden" name="f_issue_number" value="<?php  p($f_issue_number); ?>" />
	<input type="hidden" name="f_section_number" value="<?php  p($f_section_number); ?>" />
	<input type="hidden" name="f_language_id" value="<?php  p($f_language_id); ?>" />
	<input type="hidden" name="f_language_selected" value="<?php  p($f_language_selected); ?>" />
	<input type="hidden" name="f_article_number" value="<?php  p($f_article_number); ?>" />
	<input type="hidden" name="f_message" id="f_message" value="" />
	<table width="100%">
	<tr>
		<td style="padding-top: 3px;">
			<?php if ($f_edit_mode == "edit") { ?>
			<table width="100%" style="border-bottom: 1px solid #8baed1; padding: 0px;">
			<tr>
				<td align="center">
                    <?php if ($f_publication_id > 0) { ?>
                    <!-- Preview Link -->
                    <input type="submit" name="preview" value="<?php putGS('Preview'); ?>" class="button" onclick="window.open('/<?php echo $ADMIN; ?>/articles/preview.php?f_publication_id=<?php p($f_publication_id); ?>&amp;f_issue_number=<?php p($f_issue_number); ?>&amp;f_section_number=<?php p($f_section_number); ?>&amp;f_article_number=<?php p($f_article_number); ?>&amp;f_language_id=<?php p($f_language_id); ?>&amp;f_language_selected=<?php p($f_language_selected); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=no, width=680, height=560'); return false">
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <?php } ?>
					<input type="button" name="save" id="save" value="<?php putGS('Save All'); ?>" class="button" onClick="makeRequest('all');" />
					&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="submit" name="save_and_close" id="save_and_close" value="<?php putGS('Save and Close'); ?>" class="button" />
				</td>
			</tr>
			</table>
			<?php } ?>
			<table width="100%" style="border-bottom: 1px solid #8baed1; padding-bottom: 3px;">
			<tr>
	                  <td align="left" valign="top" style="padding-right: 5px;">
	                  <?php if ($f_edit_mode == "edit") { ?>
                            <input type="button" id="save_f_article_title" name="button4" value="<?php putGS('Saved'); ?>">
			  <?php } ?>
                          </td>
                          <td align="right" valign="top"><b><?php  putGS("Name"); ?>:</b></td>
                          <td align="left" valign="top" colspan="2">
                          <?php if ($f_edit_mode == "edit") { ?>
                            <input type="text" name="f_article_title" id="f_article_title" size="55" class="input_text" value="<?php  print htmlspecialchars($articleObj->getTitle()); ?>" onkeyup="buttonEnable('save_f_article_title');" <?php print $spellcheck ?> />
                          <?php } else {
                              print wordwrap(htmlspecialchars($articleObj->getTitle()), 60, "<br>");
                                }
                          ?>
                          </td>
			</tr>
			<tr>
				<td align="left" valign="top" style="padding-right: 5px;">
				<?php if ($f_edit_mode == "edit") { ?>
					<input type="button" id="save_f_article_author" name="button5" value="<?php putGS('Saved'); ?>">
				<?php } ?>
				</td>
				<td align="right" valign="top"><b><?php putGS("Author"); ?>:</b></td>
                <td align="left" valign="top" class="yui-skin-sam">
                    <?php if ($f_edit_mode == "edit") { ?>
                    <div id="authorAutoComplete">
                        <input type="text" name="f_article_author" id="f_article_author" size="45" class="input_text" value="<?php print htmlspecialchars($articleAuthorObj->getName()); ?>" onkeyup="buttonEnable('save_f_article_author');" />
                        <div id="authorContainer"></div>
                    </div>
                    <?php } else {
                            print wordwrap(htmlspecialchars($articleAuthorObj->getName()), 60, "<br>");
	                      }
                    ?>
                </td>
				<td align="right" valign="top" style="padding-right: 0.5em;"><b><?php  putGS("Created by"); ?>:</b> <?php p(htmlspecialchars($articleCreator->getRealName())); ?></td>
		    </tr>
		    </table>

		    <table cellpadding="0" cellspacing="0" width="100%">
		    <tr>
				<td align="left" valign="top">
				    <!-- Left-hand column underneath article title -->
				    <table>
				    <tr>
				        <td align="right" valign="top" style="padding-left: 1em;"><b><?php putGS("Reads"); ?>:</b></td>
				        <td align="left" valign="top">
                        <?php
                          if ($articleObj->isPublished()) {
                              $requestObject = new RequestObject($articleObj->getProperty('object_id'));
                              if ($requestObject->exists()) {
                                  echo $requestObject->getRequestCount();
                              } else {
                                  echo "0";
                              }
                          } else {
                              putGS("N/A");
                          }
                        ?>
				        </td>
				    </tr>

				    <!-- Type -->
				    <tr>
				        <td align="right" valign="top" style="padding-left: 1em;"><b><?php  putGS("Type"); ?>:</b></td>
				        <td align="left" valign="top">
					<?php print htmlspecialchars($articleType->getDisplayName()); ?>
				        </td>
                    </tr>

                    <!-- Number -->
        			<tr>
        			    <td align="right" valign="top" nowrap><b><?php putGS("Number"); ?>:</b></td>
        			    <td align="left" valign="top"  style="padding-top: 2px; padding-left: 4px;">
        			    	<?php
        			    	p($articleObj->getArticleNumber());
        			    	if (isset($publicationObj) && $publicationObj->getUrlTypeId() == 2 && $articleObj->isPublished()) {
        			    		$url = ShortURL::GetURL($publicationObj->getPublicationId(), $articleObj->getLanguageId(), null, null, $articleObj->getArticleNumber());
        			    		if (PEAR::isError($url)) {
        			    			echo $url->getMessage();
        			    		} else {
        			    			echo '&nbsp;(<a href="' . $url . '">' . getGS("Link to public page") . '</a>)';
        			    		}
        			    	}
        			    	?></td>
                    </tr>

                    <!-- Creation Date -->
        			<tr>
        				<td align="right" valign="top" style="padding-left: 1em;"><b><nobr><?php  putGS("Creation date"); ?>:</nobr></b></td>
        				<td align="left" valign="top" nowrap>
        					<?php if ($f_edit_mode == "edit") { ?>
        					<input type="hidden" name="f_creation_date" value="<?php p($articleObj->getCreationDate()); ?>" id="f_creation_date" />
        					<table cellpadding="0" cellspacing="2"><tr>
        						<td><span id="show_c_date"><?php p($articleObj->getCreationDate()); ?></span></td>
        						<td valign="top" align="left"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/calendar.gif" id="f_trigger_c"
        					    	 style="cursor: pointer; border: 1px solid red;"
        					     	 title="Date selector"
        					     	 onmouseover="this.style.background='red';"
        					     	 onmouseout="this.style.background=''" /></td>
        					</tr></table>
        					<script type="text/javascript">
        					    Calendar.setup({
        					        inputField:"f_creation_date",
        					        ifFormat:"%Y-%m-%d %H:%M:00",
        					        displayArea:"show_c_date",
        					        daFormat:"%Y-%m-%d %H:%M:00",
        					        showsTime:true,
        					        showOthers:true,
        					        weekNumbers:false,
        					        range:new Array(1990, 2020),
        					        button:"f_trigger_c"
        					    });
        					</script>
        					<?php } else { ?>
        					<?php print htmlspecialchars($articleObj->getCreationDate()); ?>
        					<?php } ?>
        				</td>
                    </tr>
                    <!-- End creation date -->

                    <tr>
                    	<td align="right" valign="top" style="padding-left: 1em;"><b><?php  putGS("Publish date"); ?>:</b></td>
				        <td align="left" valign="top">
        					<?php if ($f_edit_mode == "edit" && $articleObj->isPublished()) { ?>
        					<input type="hidden" name="f_publish_date" value="<?php p($articleObj->getPublishDate()); ?>" id="f_publish_date" />
        					<table cellpadding="0" cellspacing="2"><tr>
        						<td><span id="show_p_date"><?php p($articleObj->getPublishDate()); ?></span></td>
        						<td valign="top" align="left"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/calendar.gif" id="f_trigger_p"
        					    	 style="cursor: pointer; border: 1px solid red;"
        					     	 title="Date selector"
        					     	 onmouseover="this.style.background='red';"
        					     	 onmouseout="this.style.background=''" /></td>
        					</tr></table>
        					<script type="text/javascript">
        					    Calendar.setup({
        					        inputField:"f_publish_date",
        					        ifFormat:"%Y-%m-%d %H:%M:00",
        					        displayArea:"show_p_date",
        					        daFormat:"%Y-%m-%d %H:%M:00",
        					        showsTime:true,
        					        showOthers:true,
        					        weekNumbers:false,
        					        range:new Array(1990, 2020),
        					        button:"f_trigger_p"
        					    });
        					</script>
        					<?php } elseif ($articleObj->isPublished()) { ?>
        					<?php print htmlspecialchars($articleObj->getPublishDate()); ?>
        					<?php } else { ?>
        					<input type="hidden" name="f_publish_date" value="<?php p($articleObj->getPublishDate()); ?>" id="f_publish_date" />
        					<?php putGS('N/A'); } ?>
				        </td>
                    </tr>
                    </table>
                </td>

                <!-- Right-hand column underneath article title -->
                <td valign="top" align="right" style="padding-right: 2em; padding-top: 0.25em;">
                    <table border="0" cellpadding="0" cellspacing="1">

                    <!-- Show article on front page -->
                    <tr>
				        <td align="right" valign="top"><input type="CHECKBOX" name="f_on_front_page" id="f_on_front_page" class="input_checkbox" <?php  if ($articleObj->onFrontPage()) { ?> CHECKED<?php  } ?> <?php if ($f_edit_mode == "view") { ?>disabled<?php }?> /></td>
				        <td align="left" valign="top" style="padding-top: 0.1em;">
        				<?php  putGS('Show article on front page'); ?>
        				</td>
        			</tr>

        			<!-- Show article on section page -->
        			<tr>
				        <td align="right" valign="top"><input type="CHECKBOX" name="f_on_section_page" id="f_on_section_page" class="input_checkbox" <?php  if ($articleObj->onSectionPage()) { ?> CHECKED<?php  } ?> <?php if ($f_edit_mode == "view") { ?>disabled<?php }?> /></td>
				        <td align="left" valign="top"  style="padding-top: 0.1em;">
				            <?php  putGS('Show article on section page'); ?>
				        </td>
			        </tr>

			        <!-- Article viewable by public -->
			        <tr>
				        <td align="right" valign="top"><input type="CHECKBOX" name="f_is_public" id="f_is_public" class="input_checkbox" <?php  if ($articleObj->isPublic()) { ?> CHECKED<?php  } ?> <?php if ($f_edit_mode == "view") { ?>disabled<?php }?> /></td>
				        <td align="left" valign="top" style="padding-top: 0.1em;">
							<?php putGS('Visible to non-subscribers'); ?>
				        </td>
				    </tr>

				    <!-- Comments enabled -->
				    <?php
				    if ($showCommentControls) {
				    ?>
				    <tr>
				        <td align="left" colspan="2" style="padding-top: 0.25em;">
				            <?php putGS("Comments"); ?>:
				            <select name="f_comment_status" id="f_comment_status" class="input_select" <?php if ($f_edit_mode == "view") { ?>disabled<?php } ?>>
				            <?php
				            if ($articleObj->commentsEnabled()) {
				                if ($articleObj->commentsLocked()) {
				                    $commentStatus = 'locked';
				                } else {
				                    $commentStatus = 'enabled';
				                }
				            } else {
				                $commentStatus = 'disabled';
				            }
				            camp_html_select_option('disabled', $commentStatus, getGS("Disabled"));
				            camp_html_select_option('locked', $commentStatus, getGS("Locked"));
				            camp_html_select_option('enabled', $commentStatus, getGS("Enabled"));
				            ?>
				            </select>
				        </td>
                    </tr>
                    <?php } // end if comments enabled ?>
				    </table>
				</td>
			</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td style="border-top: 1px solid #8baed1; padding-top: 3px;">
			<table>
			<tr>
				<td align="left" style="padding-right: 5px;">
				<?php if ($f_edit_mode == "edit") { ?>
					<input type="button" id="save_f_keywords" name="button6" value="<?php putGS('Saved'); ?>">
				<?php } ?>
				</td>
				<td align="right" ><?php  putGS("Keywords"); ?>:</td>
				<td>
					<?php if ($f_edit_mode == "edit") { ?>
					<input type="TEXT" name="f_keywords" id="f_keywords" value="<?php print htmlspecialchars($articleObj->getKeywords()); ?>" class="input_text" size="50" maxlength="255" onkeyup="buttonEnable('save_f_keywords');" <?php print $spellcheck ?> />
					<?php } else {
						print htmlspecialchars($articleObj->getKeywords());
					}
					?>
				</td>
			</tr>

			<?php
			$fCustomFields = array();
                        $fCustomSwitches = array();
                        $fCustomTextareas = array();
                        $saveButtons = array();
                        $saveButtonNames = array('save_f_article_title','save_f_article_author','save_f_keywords');
			// Display the article type fields.
			foreach ($dbColumns as $dbColumn) {
				if ($dbColumn->getType() == ArticleTypeField::TYPE_TEXT) {
					// Single line text fields
			?>
			<tr>
				<td align="left" style="padding-right: 5px;">
				    <?php if ($f_edit_mode == "edit") {
                                    $saveButtonNames[] = 'save_' . $dbColumn->getName();
				    $saveButtons[] = 'var oSave' . $dbColumn->getName() .'Button = new YAHOO.widget.Button("save_' . $dbColumn->getName() . '", {
	        onclick: { fn: onButtonClick },
	        disabled: true
	});';
                                    ?>
					<input type="button" id="save_<?php p($dbColumn->getName()); ?>" value="<?php putGS('Saved'); ?>">
					<?php } ?>
				</td>
				<td align="right">
					<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
				</td>
				<td>
				<?php
				if ($f_edit_mode == "edit") {
				    $fCustomFields[] = $dbColumn->getName();
				?>
		        <input name="<?php echo $dbColumn->getName(); ?>"
				       id="<?php echo $dbColumn->getName(); ?>"
					   type="TEXT"
					   value="<?php print htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?>"
					   class="input_text"
					   size="50"
					   maxlength="255"
			           onkeyup="buttonEnable('save_<?php p($dbColumn->getName()); ?>');"
			           <?php print $spellcheck ?> />
		        <?php } else {
		        	print htmlspecialchars($articleData->getProperty($dbColumn->getName()));
		        }
		        ?>
				</td>
			</tr>
			<?php
			} elseif ($dbColumn->getType() == ArticleTypeField::TYPE_DATE) {
				// Date fields
				if ($articleData->getProperty($dbColumn->getName()) == "0000-00-00") {
					$articleData->setProperty($dbColumn->getName(), "CURDATE()", true, true);
				}
			?>
			<tr>
				<td align="left" style="padding-right: 5px;">
					<?php if ($f_edit_mode == "edit") { ?>
                                        <input type="button" id="save_<?php p($dbColumn->getName()); ?>" value="<?php putGS('Saved'); ?>">
					<?php } ?>
				</td>
				<td align="right">
					<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
				</td>
				<td>
				<?php
				    if ($f_edit_mode == "edit") {
				        $fCustomFields[] = $dbColumn->getName();
					$saveButtonNames[] = 'save_' . $dbColumn->getName();
					$saveButtons[] = 'var oSave' . $dbColumn->getName() .'Button = new YAHOO.widget.Button("save_' . $dbColumn->getName() . '", {
	        onclick: { fn: onButtonClick },
	        disabled: true
	});';
				?>
				<input name="<?php echo $dbColumn->getName(); ?>"
				           id="<?php echo $dbColumn->getName(); ?>"
					   type="TEXT"
					   value="<?php echo htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?>"
					   class="input_text"
					   size="11"
					   maxlength="10"
                                           onkeyup="buttonEnable('save_<?php p($dbColumn->getName()); ?>');" />
				<?php } else { ?>
					<span style="padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px; border: 1px solid #888; margin-right: 5px; background-color: #EEEEEE;"><?php echo htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?></span>
					<?php
				}
				?>
				<?php putGS('YYYY-MM-DD'); ?>
				</td>
			</tr>
			<?php
			} elseif ($dbColumn->getType() == ArticleTypeField::TYPE_BODY) {
				// Multiline text fields
				// Transform Campsite-specific tags into editor-friendly tags.
				$text = $articleData->getProperty($dbColumn->getName());

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
							$titles[0][$x])."/", ' title="'.$titles[1][$x].'"', $text);
						}
					}
					$formattingErrors = false;
					foreach ($imageMatches[1] as $templateId) {
						// Get the image URL
						$articleImage = new ArticleImage($f_article_number, null, $templateId);
						if (!$articleImage->exists()) {
							ArticleImage::RemoveImageTagsFromArticleText($f_article_number, $templateId);
							$formattingErrors = true;
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
						?>
<script type="text/javascript">
window.location.reload();
</script>
						<?php
					}
				}
			?>
			<tr>
			<td align="right" valign="top" style="padding-top: 8px; padding-right: 5px;">
				<?php if ($f_edit_mode == "edit") {
                                $saveButtonNames[] = 'save_' . $dbColumn->getName();
                                $saveButtons[] = 'var oSave' . $dbColumn->getName() .'Button = new YAHOO.widget.Button("save_' . $dbColumn->getName() . '", {
	        onclick: { fn: onButtonClick },
	        disabled: true
	});';
                                ?>
			        <input type="button" id="save_<?php p($dbColumn->getName()); ?>" value="<?php putGS('Saved'); ?>">
				<?php } ?>
			</td>
			<td align="right" valign="top" style="padding-top: 8px;">
				<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
			</td>
			<td align="left" valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<?php
			                    if ($f_edit_mode == "edit") {
					        $textAreaId = $dbColumn->getName() . '_' . $f_article_number;
						$fCustomTextareas[] = $textAreaId;
				        ?>
					<td><textarea name="<?php print($textAreaId); ?>"
								  id="<?php print($textAreaId); ?>"
								  rows="20" cols="70" onkeyup="buttonEnable('save_<?php p($dbColumn->getName()); ?>');"><?php print $text; ?></textarea>
					</td>
					<?php } else { ?>
					<td align="left" style="padding: 5px; <?php if (!empty($text)) {?>border: 1px solid #888; margin-right: 5px;<?php } ?>" <?php if (!empty($text)) {?>bgcolor="#EEEEEE"<?php } ?>><?php p($text); ?></td>
					<?php } ?>
				</tr>
				</table>
			</td>
			</tr>
			<?php
			} elseif ($dbColumn->getType() == ArticleTypeField::TYPE_TOPIC) {
				$articleTypeField = new ArticleTypeField($articleObj->getType(),
														 substr($dbColumn->getName(), 1));
				$rootTopicId = $articleTypeField->getTopicTypeRootElement();
				$rootTopic = new Topic($rootTopicId);
				$subtopics = Topic::GetTree($rootTopicId);
				$articleTopicId = $articleData->getProperty($dbColumn->getName());
			?>
			<tr>
			<td align="left" style="padding-right: 5px;">
				<?php if ($f_edit_mode == "edit") {
				    $fCustomFields[] = $dbColumn->getName();
                                    $saveButtonNames[] = 'save_' . $dbColumn->getName();
				    $saveButtons[] = 'var oSave' . $dbColumn->getName() .'Button = new YAHOO.widget.Button("save_' . $dbColumn->getName() . '", {
	        onclick: { fn: onButtonClick },
	        disabled: true
	});';
                                ?>
				<input type="button" id="save_<?php p($dbColumn->getName()); ?>" value="<?php putGS('Saved'); ?>">
				<?php } ?>
			</td>
			<td align="right">
				<?php echo $articleTypeField->getDisplayName(); ?>:
			</td>
			<td>
			    <?php if (count($subtopics) == 0) { ?>
			    No subtopics available.
    			<?php } else { ?>
    				<select class="input_select" name="<?php echo $dbColumn->getName(); ?>" id="<?php echo $dbColumn->getName(); ?>" <?php if ($f_edit_mode != "edit") { ?>disabled<?php } ?> onchange="buttonEnable('save_<?php p($dbColumn->getName()); ?>');">
    				<option value="0"></option>
    				<?php
    				$TOL_Language = camp_session_get('TOL_Language', 'en');
    				$currentLanguage = new Language($TOL_Language);
    				$currentLanguageId = $currentLanguage->getLanguageId();
    				foreach ($subtopics as $topicPath) {
    					$printTopic = array();
    					foreach ($topicPath as $topicId => $topic) {
    						$translations = $topic->getTranslations();
    						if (array_key_exists($currentLanguageId, $translations)) {
    							$currentTopic = $translations[$currentLanguageId];
    						} elseif ( ($currentLanguageId != 1) && array_key_exists(1, $translations)) {
    							$currentTopic = $translations[1];
    						} else {
    							$currentTopic = end($translations);
    						}
    						$printTopic[] = $currentTopic;
    					}
    					camp_html_select_option($topicId, $articleTopicId, implode(" / ", $printTopic));
    				}
    				?>
    				</select>
            <?php } ?>
			</td>
			</tr>
			<?php
			} elseif ($dbColumn->getType() == ArticleTypeField::TYPE_SWITCH) {
				$checked = $articleData->getFieldValue($dbColumn->getPrintName()) ? 'checked' : '';
			?>
            <tr>
            <td align="left" style="padding-right: 5px;">
                <?php if ($f_edit_mode == "edit") {
                    $fCustomSwitches[] = $dbColumn->getName();
                                    $saveButtonNames[] = 'save_' . $dbColumn->getName();
                    $saveButtons[] = 'var oSave' . $dbColumn->getName() .'Button = new YAHOO.widget.Button("save_' . $dbColumn->getName() . '", {
            onclick: { fn: onButtonClick },
            disabled: true
    });';
                                ?>
                <input type="button" id="save_<?php p($dbColumn->getName()); ?>" value="<?php putGS('Saved'); ?>">
                <?php } ?>
            </td>
            <td align="right">
                <?php echo $dbColumn->getDisplayName(); ?>:
            </td>
            <td>
            <input type="checkbox" name="<?php echo $dbColumn->getName(); ?>" id="<?php echo $dbColumn->getName(); ?>" <?php if ($f_edit_mode != "edit") { ?>disabled<?php } ?> onchange="buttonEnable('save_<?php p($dbColumn->getName()); ?>');" class="input_checkbox" <?php echo $checked; ?> />
            </td>
            </tr>
			<?php
			} elseif ($dbColumn->getType() == ArticleTypeField::TYPE_NUMERIC) {
            ?>
            <tr>
            <td align="left" style="padding-right: 5px;">
                <?php if ($f_edit_mode == "edit") {
                    $fCustomFields[] = $dbColumn->getName();
                                    $saveButtonNames[] = 'save_' . $dbColumn->getName();
                    $saveButtons[] = 'var oSave' . $dbColumn->getName() .'Button = new YAHOO.widget.Button("save_' . $dbColumn->getName() . '", {
            onclick: { fn: onButtonClick },
            disabled: true
    });';
                                ?>
                <input type="button" id="save_<?php p($dbColumn->getName()); ?>" value="<?php putGS('Saved'); ?>">
                <?php } ?>
            </td>
            <td align="right">
                <?php echo $dbColumn->getDisplayName(); ?>:
            </td>
            <td>
            <input type="text" class="input_text" size="20" maxlength="20" <?php print $spellcheck ?>
                   name="<?php echo $dbColumn->getName(); ?>"
                   value="<?php echo htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?>"
                   id="<?php echo $dbColumn->getName(); ?>"
                   <?php if ($f_edit_mode != "edit") { ?>disabled<?php } ?>
                   onkeyup="buttonEnable('save_<?php p($dbColumn->getName()); ?>');" />
            </td>
            </tr>
            <?php
			}
		} // foreach ($dbColumns as $dbColumn)
		?>
			</table>
		</td>
	</tr>

	<?php if ($f_edit_mode == "edit") { ?>
	<tr>
		<td colspan="2" align="center">
            <?php if ($f_publication_id > 0) { ?>
            <!-- Preview Link -->
            <input type="submit" name="preview" value="<?php putGS('Preview'); ?>" class="button" onclick="window.open('/<?php echo $ADMIN; ?>/articles/preview.php?f_publication_id=<?php p($f_publication_id); ?>&amp;f_issue_number=<?php p($f_issue_number); ?>&amp;f_section_number=<?php p($f_section_number); ?>&amp;f_article_number=<?php p($f_article_number); ?>&amp;f_language_id=<?php p($f_language_id); ?>&amp;f_language_selected=<?php p($f_language_selected); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=no, width=680, height=560'); return false">
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php } ?>
			<input type="button" name="save" value="<?php putGS('Save All'); ?>" class="button" onClick="makeRequest('all');" />
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="submit" name="save_and_close" value="<?php putGS('Save and Close'); ?>" class="button" />
		</td>
	</tr>
	<?php } ?>
	</table>
	</fieldset>
    </form>
	<!-- END Article Content -->
</td>
	<!-- END left side of article screen -->

	<!-- BEGIN right side of article screen -->
	<td valign="top" style="border-left: 1px solid #8baed1;" width="200px">
		<table width="100%">
		<?php if ($articleObj->getWorkflowStatus() != 'N') { ?>
		<tr><td>
            <!-- BEGIN Scheduled Publishing table -->
            <?php require('edit_schedule_box.php'); ?>
		    <!-- END Scheduled Publishing table -->
		</td></tr>
		<?php } ?>


		<?php if ($showComments) { ?>
		<tr><td>
		    <!-- BEGIN Comments table -->
            <?php require('edit_comments_box.php'); ?>
		    <!-- END Comments table -->
		</td></tr>
        <?php } ?>


		<tr><td>
			<!-- BEGIN Images table -->
            <?php require('edit_images_box.php'); ?>
			<!-- END Images table -->
		</td></tr>


		<tr><td>
			<!-- BEGIN Files table -->
			<?php require('edit_files_box.php'); ?>
			<!-- END Files table -->
		</td></tr>


		<tr><td>
			<!-- BEGIN Topics table -->
			<?php require('edit_topics_box.php'); ?>
			<!-- END Topics table -->
		</td></tr>


        <?php if (SystemPref::Get("UseCampcasterAudioclips") == 'Y') { ?>
		<tr><td>
            <!-- BEGIN Audioclips table -->
            <?php require('edit_audioclips_box.php'); ?>
            <!-- END Audioclips table -->
        </td></tr>
        <?php } ?>

        <?php CampPlugin::PluginAdminHooks(__FILE__); ?>

		</table>
	</td>
</tr>
</table>

<?php
//
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
?>

<!-- YUI code //-->
<script>
var resp = document.getElementById('yui-connection-container');
var mesg = document.getElementById('yui-connection-message');
var emsg = document.getElementById('yui-connection-error');
var saved = document.getElementById('yui-connection-saved');

YAHOO.namespace("example.container");

YAHOO.example.init = function () {

    // "click" event handler for each Button instance
    function onButtonClick(p_oEvent) {
        var fieldPrefix = "save_";
	var buttonId = this.get("id");
	var field = buttonId.substr(fieldPrefix.length);

	makeRequest(field);
    }

    // "contentready" event handler for the "pushbuttonsfrommarkup" <fieldset>
    YAHOO.util.Event.onContentReady("pushbuttonsfrommarkup", function () {
	// Create Buttons using existing <input> elements as a data source
	var oSaveArticleTitleButton = new YAHOO.widget.Button("save_f_article_title", {
	        onclick: { fn: onButtonClick },
	        disabled: true
	});
	var oSaveArticleAuthorButton = new YAHOO.widget.Button("save_f_article_author", {
	        onclick: { fn: onButtonClick },
	        disabled: true
	});
	var oSaveKeywordsButton = new YAHOO.widget.Button("save_f_keywords", {
	        onclick: { fn: onButtonClick },
	        disabled: true
	});
<?php
    foreach ($saveButtons as $saveButton) {
	  print($saveButton);
    }
?>
    });
} ();

function buttonEnable(buttonId) {
    var oPushButton = YAHOO.widget.Button.getButton(buttonId);
    oPushButton.set("disabled", false);
    oPushButton.set("label", "<?php putGS('Save'); ?>");
}

function buttonDisable(buttonId) {
    var oPushButton = YAHOO.widget.Button.getButton(buttonId);
    oPushButton.set("disabled", true);
    oPushButton.set("label", "<?php putGS('Saved'); ?>");
}

var handleSuccess = function(o){
    if(o.responseText !== undefined){
        //resp.innerHTML = "<li>Transaction id: " + o.tId + "</li>";
        //resp.innerHTML += "<li>HTTP status: " + o.status + "</li>";
        //resp.innerHTML += "<li>Status code message: " + o.statusText + "</li>";
        //resp.innerHTML += "<li>HTTP headers received: <ul>" + o.getAllResponseHeaders + "</ul></li>";
        //resp.innerHTML += "<li>PHP response: " + o.responseText + "</li>";

        mesg.style.display = 'inline';
        document.getElementById('yui-saved').style.display = 'none';
        var savedTime = makeSavedTime();
        saved.innerHTML = '<?php putGS("Saved:"); ?> ' + savedTime;
        mesg.innerHTML = '<?php putGS("Article Saved"); ?>';
        emsg.style.display = 'none' ;
        YAHOO.example.container.wait.hide();
    }
};

var handleFailure = function(o){
    if(o.status == 0 || o.status == -1) {
        mesg.style.display = 'none';
        emsg.style.display = 'inline';
        emsg.innerHTML = '<?php putGS("Unable to reach Campsite. Please check your internet connection."); ?>';
        YAHOO.example.container.wait.hide();
    }
};

var callback =
{
    success: handleSuccess,
    failure: handleFailure
};


var sUrl = "<?php echo $Campsite['WEBSITE_URL']; ?>/admin/articles/yui-assets/post.php";


function makeRequest(a){
    // Initialize the temporary Panel to display while waiting
    // for article saving
    YAHOO.example.container.wait =
        new YAHOO.widget.Panel("wait",
                                        { width:"240px",
					  fixedcenter:true,
					  close:false,
					  draggable:false,
					  zindex: 4,
					  modal:true,
					  visible:false
					}
			       );

    YAHOO.example.container.wait.setHeader("<?php putGS('Saving, please wait...'); ?>");
    YAHOO.example.container.wait.setBody("<img src=\"http://us.i1.yimg.com/us.yimg.com/i/us/per/gr/gp/rel_interstitial_loading.gif\"/>");
    YAHOO.example.container.wait.render(document.body);

    var postAction = '&f_save=' + a;

    var ycaFArticleTitle = document.getElementById('f_article_title').value;
    var ycaFArticleAuthor = document.getElementById('f_article_author').value;
    var ycaFOnFrontPage = document.getElementById('f_on_front_page').checked;
    var ycaFOnSectionPage = document.getElementById('f_on_section_page').checked;
    var ycaFCreationDate = document.getElementById('f_creation_date').value;
    var ycaFPublishDate = document.getElementById('f_publish_date').value;
    var ycaFIsPublic = document.getElementById('f_is_public').checked;
    <?php if ($showCommentControls) { ?>
    var ycaFCommentStatus = document.getElementById('f_comment_status').value;
    <?php } ?>
    var ycaFKeywords = document.getElementById('f_keywords').value;
    var ycaFPublicationId = document.getElementById('f_publication_id').value;
    var ycaFIssueNumber = document.getElementById('f_issue_number').value;
    var ycaFSectionNumber = document.getElementById('f_section_number').value;
    var ycaFLanguageId = document.getElementById('f_language_id').value;
    var ycaFLanguageSelected = document.getElementById('f_language_selected').value;
    var ycaFArticleNumber = document.getElementById('f_article_number').value;
    var ycaFMessage = document.getElementById('f_message').value;

    var textFields = [<?php print($jsArrayFieldsStr); ?>];
    var textSwitches = [<?php print($jsArraySwitchesStr); ?>];
    var textAreas = [<?php print($jsArrayTextareasStr); ?>];
    var postCustomFieldsData = '';
    var postCustomSwitchesData = '';
    var postCustomTextareasData = '';

    for (i = 0; i < textFields.length; i++) {
        postCustomFieldsData += '&' + textFields[i] + '=' + encodeURIComponent(document.getElementById(textFields[i]).value);
    }

    for (i = 0; i < textSwitches.length; i++) {
        if (document.getElementById(textSwitches[i]).checked == true)
	    postCustomSwitchesData += '&' + textSwitches[i] + '=on';
	else
	    postCustomSwitchesData += '&' + textSwitches[i] + '=';
    }

    for (i = 0; i < textAreas.length; i++) {
        var ed = tinyMCE.get(textAreas[i]);
        postCustomTextareasData += '&' + textAreas[i] + '=' + encodeURIComponent(ed.getContent());
    }

    if (ycaFOnFrontPage == true)
        ycaFOnFrontPage = 'on';
    else
        ycaFOnFrontPage = '';
    if (ycaFOnSectionPage == true)
        ycaFOnSectionPage = 'on';
    else
        ycaFOnSectionPage = '';
    if (ycaFIsPublic == true)
        ycaFIsPublic = 'on';
    else
        ycaFIsPublic = '';

    var postData = "f_article_title=" + encodeURIComponent(ycaFArticleTitle)
      + "&f_article_author=" + ycaFArticleAuthor
      + "&f_on_front_page=" + ycaFOnFrontPage
      + "&f_on_section_page=" + ycaFOnSectionPage
      + "&f_creation_date=" + ycaFCreationDate
      + "&f_publish_date=" + ycaFPublishDate
      + "&f_is_public=" + ycaFIsPublic
    <?php if ($showCommentControls) { ?>
      + "&f_comment_status=" + ycaFCommentStatus
    <?php } ?>
      + "&f_keywords=" + ycaFKeywords
      + "&f_publication_id=" + ycaFPublicationId
      + "&f_issue_number=" + ycaFIssueNumber
      + "&f_section_number=" + ycaFSectionNumber
      + "&f_language_id=" + ycaFLanguageId
      + "&f_language_selected=" + ycaFLanguageSelected
      + "&f_article_number=" + ycaFArticleNumber
      + "&f_message=" + encodeURIComponent(ycaFMessage)
      + postCustomFieldsData + postCustomSwitchesData
      + postCustomTextareasData + postAction
      + "&<?php echo SecurityToken::URLParameter(); ?>";

    // Show the saving panel
    YAHOO.example.container.wait.show();

    var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, postData);
    setTimeout(function() { YAHOO.util.Connect.abort(request, callback) }, 30000);

    if (a == "all") {
        <?php
            foreach ($saveButtonNames as $saveButtonName) {
        ?>
                buttonDisable("<?php print($saveButtonName); ?>");
        <?php
            }
        ?>
    } else {
        buttonDisable("save_" + a);
    }
}

function makeSavedTime() {
    var dt = new Date();
    var hours = dt.getHours();
    var minutes = dt.getMinutes();
    var seconds = dt.getSeconds();

    if (minutes < 10){ minutes = "0" + minutes }
    if (seconds < 10){ seconds = "0" + seconds }

    return hours + ':' + minutes + ':' + seconds;
}

authorsData = {
		arrayAuthors: [
<?php
$allAuthors = Author::GetAllExistingNames();
$quoteStringFn = create_function('&$value, $key',
                 '$value = "\"" . camp_javascriptspecialchars($value) . "\"";');
array_walk($allAuthors, $quoteStringFn);
echo implode(",\n", $allAuthors);
?>
        ]
};

createAuthorAutocomplete = function() {
    // Use a LocalDataSource
    var oDS = new YAHOO.util.LocalDataSource(authorsData.arrayAuthors);

    // Instantiate the AutoComplete
    var oAC = new YAHOO.widget.AutoComplete("f_article_author", "authorContainer", oDS);
    oAC.prehighlightClassName = "yui-ac-prehighlight";
    oAC.useShadow = true;

    return {
        oDS: oDS,
        oAC: oAC
    };
}();

</script>
<!-- END YUI code //-->

<?php
if ($showComments && $f_show_comments) {
    include("comments/show_comments.php");
}

camp_html_copyright_notice();
?>
