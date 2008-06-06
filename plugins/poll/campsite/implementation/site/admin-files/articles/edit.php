<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/editor_load_xinha.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbReplication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticlePublish.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleAttachment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleImage.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleTopic.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleAudioclip.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ShortURL.php');
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

$articleData = $articleObj->getArticleData();
// Get article type fields.
$dbColumns = $articleData->getUserDefinedColumns(0);
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
    require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleComment.php');
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
if ( $timeDiff['days'] > 0 ) {
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
if ( ($f_edit_mode == "view") && $lockedByCurrentUser) {
    $articleObj->setIsLocked(false);
}

// If the article is locked by the current user, OK to edit.
if ($lockedByCurrentUser) {
    $locked = false;
}

//
// Begin Display of page
//
include_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/javascript_common.php");

if ($f_edit_mode == "edit") {
	$title = getGS("Edit article");
}
else {
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
	if (stristr($dbColumn->getType(), "blob")) {
		$hasArticleBodyField = true;
	}
}
if (($f_edit_mode == "edit") && $hasArticleBodyField) {
	$languageSelectedObj = new Language($f_language_selected);
	$editorLanguage = camp_session_get('TOL_Language', $languageSelectedObj->getCode());
	editor_load_xinha($dbColumns, $g_user, $editorLanguage);
}

// If the article is locked.
if ($articleObj->userCanModify($g_user) && $locked && ($f_edit_mode == "edit")) {
	?><P>
	<table border="0" cellspacing="0" cellpadding="6" class="table_input">
	<tr>
		<td colspan="2">
			<B><?php  putGS("Article is locked"); ?> </B>
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
				}
				else {
					putGS('The article has been locked by $1 ($2) $3 minute(s) ago.',
						  '<B>'.htmlspecialchars($lockUserObj->getRealName()),
						  htmlspecialchars($lockUserObj->getUserName()).'</B>',
						  $timeDiff['minutes']);
				}
				?>
				<br>
			</blockquote>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<div align="CENTER">
		<input type="button" name="Yes" value="<?php  putGS('Unlock'); ?>" class="button" onclick="location.href='<?php echo camp_html_article_url($articleObj, $f_language_id, "do_unlock.php"); ?>'" />
		<input type="button" name="Yes" value="<?php  putGS('View'); ?>" class="button" onclick="location.href='<?php echo camp_html_article_url($articleObj, $f_language_id, "edit.php", "", "&f_edit_mode=view"); ?>'" />
		<input type="button" name="No" value="<?php  putGS('Cancel'); ?>" class="button" onclick="location.href='/<?php echo $ADMIN; ?>/articles/?f_publication_id=<?php  p($f_publication_id); ?>&f_issue_number=<?php  p($f_issue_number); ?>&f_language_id=<?php p($f_language_id); ?>&f_section_number=<?php  p($f_section_number); ?>'" />
		</div>
		</td>
	</tr>
	</table>
	<P>
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
	<td><a href="<?php echo "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"; ?>"><B><?php  putGS("Article List"); ?></B></a></td>

	<?php if ($g_user->hasPermission('AddArticle')) { ?>
	<td style="padding-left: 20px;"><a href="add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>" ><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" border="0"></a></td>
	<td><a href="add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>" ><B><?php  putGS("Add new article"); ?></B></a></td>
<?php  } ?>
</tr>
</table>
<?php } ?>

<?php camp_html_display_msgs("0.25em", "0.25em"); ?>

<table border="0" cellspacing="1" cellpadding="0" class="table_input" width="900px" style="margin-top: 5px;">
<tr>
	<td width="700px" style="border-bottom: 1px solid #8baed1;" colspan="2">
		<!-- for the left side of the article edit screen -->
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td width="100%" valign="middle">

    			<!-- BEGIN the article control bar -->
    			<form name="article_actions" action="do_article_action.php" method="POST">
    			<input type="hidden" name="f_publication_id" value="<?php  p($f_publication_id); ?>" />
    			<input type="hidden" name="f_issue_number" value="<?php  p($f_issue_number); ?>" />
    			<input type="hidden" name="f_section_number" value="<?php  p($f_section_number); ?>" />
    			<input type="hidden" name="f_language_id" value="<?php  p($f_language_id); ?>" />
    			<input type="hidden" name="f_language_selected" value="<?php  p($f_language_selected); ?>" />
    			<input type="hidden" name="f_article_number" value="<?php  p($f_article_number); ?>" />
    			<table border="0" cellspacing="1" cellpadding="0">
    			<tr>
					<td style="padding-left: 1em;">
						<script>
						function action_selected(dropdownElement)
						{
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
							if ( (dropdownElement.selectedIndex != 0) &&  (dropdownElement.selectedIndex != 1) ) {
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

						<?php  if ($g_user->hasPermission('DeleteArticle')) { ?>
						<option value="delete"><?php putGS("Delete"); ?></option>
						<?php } ?>

						<?php  if ($g_user->hasPermission('AddArticle')) { ?>
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
						camp_html_select_option("Y", $articleObj->getWorkflowStatus(), getGS("Status: Published"));
						camp_html_select_option("S", $articleObj->getWorkflowStatus(), getGS("Status: Submitted"));
						camp_html_select_option("N", $articleObj->getWorkflowStatus(), getGS("Status: New"));
						?>
						</select>
						<?php } elseif ($articleObj->userCanModify($g_user) && ($articleObj->getWorkflowStatus() != 'Y')) { ?>
						<select name="f_action_workflow" class="input_select" onchange="this.form.submit();">
						<?php
						camp_html_select_option("S", $articleObj->getWorkflowStatus(), getGS("Status: Submitted"));
						camp_html_select_option("N", $articleObj->getWorkflowStatus(), getGS("Status: New"));
						?>
						</select>
						<?php } else {
							switch ($articleObj->getWorkflowStatus()) {
								case 'Y':
									putGS("Status: Published");
									break;
								case 'S':
									putGS("Status: Submitted");
									break;
								case 'N':
									putGS("Status: New");
									break;
							}
						}
						if ( count($articleEvents) > 0 ) {
							?>
							<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/automatic_publishing.png" alt="<?php  putGS("Scheduled Publishing"); ?>" title="<?php  putGS("Scheduled Publishing"); ?>" border="0" width="22" height="22" align="absmiddle" style="padding-bottom: 1px;">
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

			     <td style="background-color: #00CB38; color: #FFF; border: 1px solid white; padding-left: 5px; padding-right: 10px;" align="center" nowrap>
			     <b><?php putGS("Saved:"); ?> <?php if ($savedToday) { p(date("H:i", $lastModified)); } else { p(date("Y-m-d H:i", $lastModified)); } ?></b>
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
	<input type="hidden" name="f_publication_id" value="<?php  p($f_publication_id); ?>" />
	<input type="hidden" name="f_issue_number" value="<?php  p($f_issue_number); ?>" />
	<input type="hidden" name="f_section_number" value="<?php  p($f_section_number); ?>" />
	<input type="hidden" name="f_language_id" value="<?php  p($f_language_id); ?>" />
	<input type="hidden" name="f_language_selected" value="<?php  p($f_language_selected); ?>" />
	<input type="hidden" name="f_article_number" value="<?php  p($f_article_number); ?>" />
	<input type="hidden" name="f_message" value="" />
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
					<input type="submit" name="save" value="<?php putGS('Save'); ?>" class="button" />
					&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="submit" name="save_and_close" value="<?php putGS('Save and Close'); ?>" class="button" />
				</td>
			</tr>
			</table>
			<?php } ?>
			<table width="100%" style="border-bottom: 1px solid #8baed1; padding-bottom: 3px;">
			<tr>
				<td align="left" valign="top"><b><?php putGS("Name"); ?>:</b>
					<?php if ($f_edit_mode == "edit") { ?>
					<input type="text" name="f_article_title" size="60" class="input_text" value="<?php  print htmlspecialchars($articleObj->getTitle()); ?>" />
					<?php } else {
						print wordwrap(htmlspecialchars($articleObj->getTitle()), 60, "<br>");
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
        						<td><span id="show_date"><?php p($articleObj->getCreationDate()); ?></span></td>
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
        					        displayArea:"show_date",
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
        						<td><span id="show_date"><?php p($articleObj->getPublishDate()); ?></span></td>
        						<td valign="top" align="left"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/calendar.gif" id="f_trigger_c"
        					    	 style="cursor: pointer; border: 1px solid red;"
        					     	 title="Date selector"
        					     	 onmouseover="this.style.background='red';"
        					     	 onmouseout="this.style.background=''" /></td>
        					</tr></table>
        					<script type="text/javascript">
        					    Calendar.setup({
        					        inputField:"f_publish_date",
        					        ifFormat:"%Y-%m-%d %H:%M:00",
        					        displayArea:"show_date",
        					        daFormat:"%Y-%m-%d %H:%M:00",
        					        showsTime:true,
        					        showOthers:true,
        					        weekNumbers:false,
        					        range:new Array(1990, 2020),
        					        button:"f_trigger_c"
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
				        <td align="right" valign="top"><input type="CHECKBOX" name="f_on_front_page" class="input_checkbox" <?php  if ($articleObj->onFrontPage()) { ?> CHECKED<?php  } ?> <?php if ($f_edit_mode == "view") { ?>disabled<?php }?> /></td>
				        <td align="left" valign="top" style="padding-top: 0.1em;">
        				<?php  putGS('Show article on front page'); ?>
        				</td>
        			</tr>

        			<!-- Show article on section page -->
        			<tr>
				        <td align="right" valign="top"><input type="CHECKBOX" name="f_on_section_page" class="input_checkbox" <?php  if ($articleObj->onSectionPage()) { ?> CHECKED<?php  } ?> <?php if ($f_edit_mode == "view") { ?>disabled<?php }?> /></td>
				        <td align="left" valign="top"  style="padding-top: 0.1em;">
				            <?php  putGS('Show article on section page'); ?>
				        </td>
			        </tr>

			        <!-- Article viewable by public -->
			        <tr>
				        <td align="right" valign="top"><input type="CHECKBOX" name="f_is_public" class="input_checkbox" <?php  if ($articleObj->isPublic()) { ?> CHECKED<?php  } ?> <?php if ($f_edit_mode == "view") { ?>disabled<?php }?> /></td>
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
				            <?php putGS("Comments:"); ?>
				            <select name="f_comment_status" class="input_select" <?php if ($f_edit_mode == "view") { ?>disabled<?php } ?>>
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
					<input type="image" src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" name="save" value="save" />
				<?php } ?>
				</td>
				<td align="right" ><?php  putGS("Keywords"); ?>:</td>
				<td>
					<?php if ($f_edit_mode == "edit") { ?>
					<input type="TEXT" name="f_keywords" value="<?php print htmlspecialchars($articleObj->getKeywords()); ?>" class="input_text" size="50" maxlength="255" />
					<?php } else {
						print htmlspecialchars($articleObj->getKeywords());
					}
					?>
				</td>
			</tr>

			<?php
			// Display the article type fields.
			foreach ($dbColumns as $dbColumn) {

				if (stristr($dbColumn->getType(), "char")
				    /* DO NOT DELETE */ || stristr($dbColumn->getType(), "binary") /* DO NOT DELETE */ ) {
					// The "binary" comparizon is needed for Fedora distro; MySQL on Fedora changes ALL
					// "char" types to "binary".

					// Single line text fields
			?>
			<tr>
				<td align="left" style="padding-right: 5px;">
					<?php if ($f_edit_mode == "edit") { ?>
					<input type="image" src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" name="save" value="save" />
					<?php } ?>
				</td>
				<td align="right">
					<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
				</td>
				<td>
				<?php
				if ($f_edit_mode == "edit") { ?>
		        <input name="<?php echo $dbColumn->getName(); ?>"
					   type="TEXT"
					   value="<?php print htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?>"
					   class="input_text"
					   size="50"
					   maxlength="255" />
		        <?php } else {
		        	print htmlspecialchars($articleData->getProperty($dbColumn->getName()));
		        }
		        ?>
				</td>
			</tr>
			<?php
			} elseif (stristr($dbColumn->getType(), "date")) {
				// Date fields
				if ($articleData->getProperty($dbColumn->getName()) == "0000-00-00") {
					$articleData->setProperty($dbColumn->getName(), "CURDATE()", true, true);
				}
			?>
			<tr>
				<td align="left" style="padding-right: 5px;">
					<?php if ($f_edit_mode == "edit") { ?>
					<input type="image" src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" name="save" value="save" />
					<?php } ?>
				</td>
				<td align="right">
					<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
				</td>
				<td>
				<?php if ($f_edit_mode == "edit") { ?>
				<input name="<?php echo $dbColumn->getName(); ?>"
					   type="TEXT"
					   value="<?php echo htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?>"
					   class="input_text"
					   size="11"
					   maxlength="10" />
				<?php } else { ?>
					<span style="padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px; border: 1px solid #888; margin-right: 5px; background-color: #EEEEEE;"><?php echo htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?></span>
					<?php
				}
				?>
				<?php putGS('YYYY-MM-DD'); ?>
				</td>
			</tr>
			<?php
			} elseif (stristr($dbColumn->getType(), "blob")) {
				// Multiline text fields
				// Transform Campsite-specific tags into editor-friendly tags.
				$text = $articleData->getProperty($dbColumn->getName());

				// Subheads
				$text = preg_replace("/<!\*\*\s*Title\s*>/i", "<span class=\"campsite_subhead\">", $text);
				$text = preg_replace("/<!\*\*\s*EndTitle\s*>/i", "</span>", $text);

				// Internal Links with targets
                $text = preg_replace("/<!\*\*\s*Link\s*Internal\s*([\w=&]*)\s*target[\s\"]*([\w_]*)[\s\"]*>/i", '<a href="campsite_internal_link?$1" target="$2">', $text);

				// Internal Links without targets
				$text = preg_replace("/<!\*\*\s*Link\s*Internal\s*([\w=&]*)\s*>/i", '<a href="campsite_internal_link?$1">', $text);

                // External Links (old style 2.1) with targets
                $text = preg_replace("/<!\*\*\s*Link\s*External[\s\"]*([^\s\"]*)[\s\"]*target[\s\"]*([\w_]*)[\s\"]*>/i", '<a href="$1" target="$2">', $text);

                // External Links (old style 2.1) without targets
                $text = preg_replace("/<!\*\*\s*Link\s*External[\s\"]*([^\s\"]*)[\s\"]*>/i", '<a href="$1">', $text);

				// End link
				$text = preg_replace("/<!\*\*\s*EndLink\s*>/i", "</a>", $text);
				// Images
				preg_match_all("/<!\*\*\s*Image\s*([\d]*)\s*/i",$text, $imageMatches);

				if (isset($imageMatches[1][0])) {
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
						$text = preg_replace("/<!\*\*\s*Image\s*".$templateId."\s*/i", '<img src="'.$imageUrl.'" id="'.$templateId.'" ', $text);
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
				<?php if ($f_edit_mode == "edit") { ?>
				<input type="image" src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" name="save" value="save" />
				<?php } ?>
			</td>
			<td align="right" valign="top" style="padding-top: 8px;">
				<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
			</td>
			<td align="left" valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<?php if ($f_edit_mode == "edit") { ?>
					<td><textarea name="<?php print $dbColumn->getName() ?>"
								  id="<?php print $dbColumn->getName() ?>"
								  rows="20" cols="80"><?php print $text; ?></textarea>
					</td>
					<?php } else { ?>
					<td align="left" style="padding: 5px; <?php if (!empty($text)) {?>border: 1px solid #888; margin-right: 5px;<?php } ?>" <?php if (!empty($text)) {?>bgcolor="#EEEEEE"<?php } ?>><?php p($text); ?></td>
					<?php } ?>
				</tr>
				</table>
			</td>
			</tr>
			<?php
			} elseif (stristr($dbColumn->getType(), "topic")) {
				$articleTypeField = new ArticleTypeField($articleObj->getType(),
														 substr($dbColumn->getName(), 1));
				$rootTopicId = $articleTypeField->getTopicTypeRootElement();
				$rootTopic = new Topic($rootTopicId);
				$subtopics = Topic::GetTree($rootTopicId);
				$articleTopicId = $articleData->getProperty($dbColumn->getName());
			?>
			<tr>
			<td align="right" valign="top" style="padding-top: 8px; padding-right: 5px;">
				<?php if ($f_edit_mode == "edit") { ?>
				<input type="image" src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" name="save" value="save" />
				<?php } ?>
			</td>
			<td align="right">
				<?php echo $articleTypeField->getDisplayName(); ?>:
			</td>
			<td>
			    <?php if (count($subtopics) == 0) { ?>
			    No subtopics available.
    			<?php } else { ?>
    				<select class="input_select" name="<?php echo $dbColumn->getName(); ?>" <?php if ($f_edit_mode != "edit") { ?>disabled<?php } ?>>
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
			<input type="submit" name="save" value="<?php putGS('Save'); ?>" class="button" />
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="submit" name="save_and_close" value="<?php putGS('Save and Close'); ?>" class="button" />
		</td>
	</tr>
	<?php } ?>
	</table>
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
        
        <?php
        // plugins: poll assignment popup
        if ($path = camp_get_plugin_path('poll', __FILE__)) {
            include ($path);   
        }
        ?>
		</table>
	</td>
</tr>
</table>
<?php
if ($showComments && $f_show_comments) {
    include("comments/show_comments.php");
}

camp_html_copyright_notice();
?>
