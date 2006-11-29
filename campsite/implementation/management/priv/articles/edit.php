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
$f_language_selected = camp_session_get('f_language_selected', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

// Fetch article
$articleObj =& new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('No such article.'));
	exit;
}

$articleData = $articleObj->getArticleData();
// Get article type fields.
$dbColumns = $articleData->getUserDefinedColumns(0);
$articleType =& new ArticleType($articleObj->getType());

$articleImages = ArticleImage::GetImagesByArticleNumber($f_article_number);
$lockUserObj =& new User($articleObj->getLockedByUser());
$articleCreator =& new User($articleObj->getCreatorId());
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
	$publicationObj =& new Publication($f_publication_id);
	$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
	$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
	$languageObj =& new Language($articleObj->getLanguageId());

    $showCommentControls = ($publicationObj->commentsEnabled() && $articleType->commentsEnabled());
    $showComments = $showCommentControls && $articleObj->commentsEnabled();
}

if ($showComments) {
    require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleComment.php');
    if (SystemPref::Get("UseDBReplication") == 'Y') {
        $dbReplicationObj =& new DbReplication();
        $onlineCnn = $dbReplicationObj->Connect();
    }
    // Fetch the comments attached to this article
    $comments = ArticleComment::GetArticleComments($f_article_number, $f_language_id);
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
	editor_load_xinha($dbColumns, $g_user);
}

// If the article is locked.
if ($articleObj->userCanModify($g_user) && $locked && ($f_edit_mode == "edit")) {
	?><P>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("Article is locked"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2" align="center">
			<BLOCKQUOTE>
				<?PHP
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
			</BLOCKQUOTE>
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" NAME="Yes" VALUE="<?php  putGS('Unlock'); ?>" class="button" ONCLICK="location.href='<?php echo camp_html_article_url($articleObj, $f_language_id, "do_unlock.php"); ?>'">
		<INPUT TYPE="button" NAME="Yes" VALUE="<?php  putGS('View'); ?>" class="button" ONCLICK="location.href='<?php echo camp_html_article_url($articleObj, $f_language_id, "edit.php", "", "&f_edit_mode=view"); ?>'">
		<INPUT TYPE="button" NAME="No" VALUE="<?php  putGS('Cancel'); ?>" class="button" ONCLICK="location.href='/<?php echo $ADMIN; ?>/articles/?f_publication_id=<?php  p($f_publication_id); ?>&f_issue_number=<?php  p($f_issue_number); ?>&f_language_id=<?php p($f_language_id); ?>&f_section_number=<?php  p($f_section_number); ?>'">
		</DIV>
		</TD>
	</TR>
	</TABLE>
	<P>
	<?php
	return;
}

if ($f_edit_mode == "edit") { ?>
<style type="text/css">@import url(<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/calendar-system.css);</style>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/lang/calendar-<?php echo $_REQUEST["TOL_Language"]; ?>.js"></script>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/calendar-setup.js"></script>
<?php } // if edit mode ?>

<?php if ($f_publication_id > 0) { ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="<?php echo "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"; ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="<?php echo "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"; ?>"><B><?php  putGS("Article List"); ?></B></A></TD>

	<?php if ($g_user->hasPermission('AddArticle')) { ?>
	<TD style="padding-left: 20px;"><A HREF="add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
	<TD><A HREF="add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>" ><B><?php  putGS("Add new article"); ?></B></A></TD>
<?php  } ?>
</tr>
</TABLE>
<?php } ?>

<?php camp_html_display_msgs("0.25em", "0.25em"); ?>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" class="table_input" width="900px" style="margin-top: 5px;">
<TR>
	<TD width="700px" style="border-bottom: 1px solid #8baed1;" colspan="2">
		<!-- for the left side of the article edit screen -->
		<TABLE cellpadding="0" cellspacing="0">
		<tr>
			<td width="100%" valign="middle">

    			<!-- BEGIN the article control bar -->
    			<FORM name="article_actions" action="do_article_action.php" method="POST">
    			<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php  p($f_publication_id); ?>">
    			<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php  p($f_issue_number); ?>">
    			<INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php  p($f_section_number); ?>">
    			<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
    			<INPUT TYPE="HIDDEN" NAME="f_language_selected" VALUE="<?php  p($f_language_selected); ?>">
    			<INPUT TYPE="HIDDEN" NAME="f_article_number" VALUE="<?php  p($f_article_number); ?>">
    			<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
    			<TR>
					<TD style="padding-left: 1em;">
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
						<SELECT name="f_action" class="input_select" onchange="action_selected(this);">
						<OPTION value=""><?php putGS("Actions"); ?>...</OPTION>
						<OPTION value="">-----------</OPTION>

						<?php if ($articleObj->userCanModify($g_user) && $articleObj->isLocked()) { ?>
						<OPTION value="unlock"><?php putGS("Unlock"); ?></OPTION>
						<?php } ?>

						<?php  if ($g_user->hasPermission('DeleteArticle')) { ?>
						<OPTION value="delete"><?php putGS("Delete"); ?></OPTION>
						<?php } ?>

						<?php  if ($g_user->hasPermission('AddArticle')) { ?>
						<OPTION value="copy"><?php putGS("Duplicate"); ?></OPTION>
						<?php } ?>

						<?php if ($g_user->hasPermission('TranslateArticle')) { ?>
						<OPTION value="translate"><?php putGS("Translate"); ?></OPTION>
						<?php } ?>

						<?php if ($g_user->hasPermission('MoveArticle')) { ?>
						<OPTION value="move"><?php putGS("Move"); ?></OPTION>
						<?php } ?>
						</SELECT>
					</TD>

					<?php if ($f_publication_id > 0) { ?>
					<TD>
						<!-- Preview Link -->
						<A HREF="" ONCLICK="window.open('/<?php echo $ADMIN; ?>/articles/preview.php?f_publication_id=<?php  p($f_publication_id); ?>&f_issue_number=<?php  p($f_issue_number); ?>&f_section_number=<?php  p($f_section_number); ?>&f_article_number=<?php  p($f_article_number); ?>&f_language_id=<?php  p($f_language_id); ?>&f_language_selected=<?php  p($f_language_selected); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=no, width=680, height=560'); return false"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/preview.png" BORDER="0" alt="<?php putGS("Preview"); ?>" title="<?php putGS("Preview"); ?>"></A>
					</TD>
					<?php } ?>

					<!-- BEGIN Workflow -->
					<TD style="padding-left: 1em;">
						<?php
						// Show a different menu depending on the rights of the user.
						if ($g_user->hasPermission("Publish")) { ?>
						<SELECT name="f_action_workflow" class="input_select" onchange="this.form.submit();">
						<?php
						camp_html_select_option("Y", $articleObj->getWorkflowStatus(), getGS("Status: Published"));
						camp_html_select_option("S", $articleObj->getWorkflowStatus(), getGS("Status: Submitted"));
						camp_html_select_option("N", $articleObj->getWorkflowStatus(), getGS("Status: New"));
						?>
						</SELECT>
						<?php } elseif ($articleObj->userCanModify($g_user) && ($articleObj->getWorkflowStatus() != 'Y')) { ?>
						<SELECT name="f_action_workflow" class="input_select" onchange="this.form.submit();">
						<?php
						camp_html_select_option("S", $articleObj->getWorkflowStatus(), getGS("Status: Submitted"));
						camp_html_select_option("N", $articleObj->getWorkflowStatus(), getGS("Status: New"));
						?>
						</SELECT>
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

					</TD>
					<!-- END Workflow -->

					<TD style="padding-left: 1em;">
		        		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
		        		<TR>
		        			<TD><?php  putGS('Language'); ?>:</TD>
		        			<TD>
								<?php
								if (count($articleLanguages) > 1) {
				        		$languageUrl = "edit.php?f_publication_id=$f_publication_id"
				        			."&f_issue_number=$f_issue_number"
				        			."&f_section_number=$f_section_number"
				        			."&f_article_number=$f_article_number"
				        			."&f_language_id=$f_language_id"
				        			."&f_language_selected=";
		        				?>
		        				<SELECT NAME="f_language_selected" class="input_select" onchange="dest = '<?php p($languageUrl); ?>'+this.options[this.selectedIndex].value; location.href=dest;">
		    					<?php
		    					foreach ($articleLanguages as $articleLanguage) {
		    					    camp_html_select_option($articleLanguage->getLanguageId(), $f_language_selected, htmlspecialchars($articleLanguage->getNativeName()));
		    					}
		        				?></SELECT>
		        				<?php } else {
		        					$articleLanguage = camp_array_peek($articleLanguages);
		        					echo '<b>'.htmlspecialchars($articleLanguage->getNativeName()).'</b>';
		        				}
		        				?>

		        			</TD>
		        		</TR>
		        		</TABLE>
					</TD>
				</TR>
				</TABLE>
				</form>
				<!-- END the article control bar -->
			</TD>

			<?PHP
			if ($articleObj->userCanModify($g_user)) {
			$switchModeUrl = camp_html_article_url($articleObj, $f_language_id, "edit.php")
				."&f_edit_mode=".( ($f_edit_mode =="edit") ? "view" : "edit");
			?>
			<TD align="right" style="padding-top: 1px;" valign="top">
			     <table cellpadding="0" cellspacing="0" border="0">
			     <tr><td>
			     <input type="button" name="edit" value="<?php putGS("Edit"); ?>" <?php if ($f_edit_mode == "edit") {?> disabled class="button_disabled" <?php } else { ?> onclick="location.href='<?php p($switchModeUrl); ?>';" class="button" <?php } ?>>
			     </td>

			     <td style="padding-left: 5px; padding-right: 10px;">
			     <input type="button" name="edit" value="<?php putGS("View"); ?>" <?php if ($f_edit_mode == "view") {?> disabled class="button_disabled" <?php } else { ?> onclick="location.href='<?php p($switchModeUrl); ?>';" class="button" <?php } ?>>
			     </td>

			     <td style="background-color: #00CB38; color: #FFF; border: 1px solid white; padding-left: 5px; padding-right: 10px;" align="center" nowrap>
			     <b><?php putGS("Saved:"); ?> <?php if ($savedToday) { p(date("H:i", $lastModified)); } else { p(date("Y-m-d H:i", $lastModified)); } ?></b>
			     </td>
			     </tr></table>
			</TD>
			<?php } ?>
		</TR>
		</table>
	</td>
</tr>

<tr>
	<td valign="top">
	<!-- BEGIN article content -->
	<FORM name="article_edit" action="do_edit.php" method="POST">
	<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php  p($f_publication_id); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php  p($f_issue_number); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php  p($f_section_number); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_language_selected" VALUE="<?php  p($f_language_selected); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_article_number" VALUE="<?php  p($f_article_number); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_message" VALUE="">
	<table width="100%">
	<TR>
		<TD style="padding-top: 3px;">
			<TABLE width="100%" style="border-bottom: 1px solid #8baed1; padding-bottom: 3px;">
			<TR>
				<TD ALIGN="left" valign="top"><b><?php putGS("Name"); ?>:</b>
					<?php if ($f_edit_mode == "edit") { ?>
					<input type="text" name="f_article_title" size="60" class="input_text" value="<?php  print htmlspecialchars($articleObj->getTitle()); ?>">
					<?php } else {
						print wordwrap(htmlspecialchars($articleObj->getTitle()), 60, "<br>");
					}
					?>
				</TD>
				<TD ALIGN="RIGHT" valign="top" style="padding-right: 0.5em;"><b><?php  putGS("Created by"); ?>:</b> <?php p(htmlspecialchars($articleCreator->getRealName())); ?></TD>
		    </tr>
		    </table>

		    <table cellpadding="0" cellspacing="0" width="100%">
		    <tr>
				<td align="left" valign="top">
				    <!-- Left-hand column underneath article title -->
				    <table>

				    <!-- Type -->
				    <tr>
				        <TD ALIGN="RIGHT" valign="top" style="padding-left: 1em;"><b><?php  putGS("Type"); ?>:</b></TD>
				        <TD align="left" valign="top">
					<?php print htmlspecialchars($articleType->getDisplayName()); ?>
				        </TD>
                    </tr>

                    <!-- Number -->
        			<TR>
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
        			<TR>
        				<TD ALIGN="RIGHT" valign="top" style="padding-left: 1em;"><b><nobr><?php  putGS("Creation date"); ?>:</nobr></b></TD>
        				<TD align="left" valign="top" nowrap>
        					<?php if ($f_edit_mode == "edit") { ?>
        					<input type="hidden" name="f_creation_date" value="<?php p($articleObj->getCreationDate()); ?>" id="f_creation_date">
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
        				</TD>
                    </tr>
                    <!-- End creation date -->

                    <tr>
                    	<TD ALIGN="RIGHT" valign="top" style="padding-left: 1em;"><b><?php  putGS("Publish date"); ?>:</b></TD>
				        <TD align="left" valign="top">
        					<?php if ($f_edit_mode == "edit" && $articleObj->isPublished()) { ?>
        					<input type="hidden" name="f_publish_date" value="<?php p($articleObj->getPublishDate()); ?>" id="f_publish_date">
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
        					<input type="hidden" name="f_publish_date" value="<?php p($articleObj->getPublishDate()); ?>" id="f_publish_date">
        					<?php putGS('N/A'); } ?>
				        </TD>
                    </tr>
                    </table>
                </td>

                <!-- Right-hand column underneath article title -->
                <td valign="top" align="right" style="padding-right: 2em; padding-top: 0.25em;">
                    <table border="0" cellpadding="0" cellspacing="1">

                    <!-- Show article on front page -->
                    <tr>
				        <TD ALIGN="RIGHT" valign="top"><INPUT TYPE="CHECKBOX" NAME="f_on_front_page" class="input_checkbox" <?php  if ($articleObj->onFrontPage()) { ?> CHECKED<?php  } ?> <?php if ($f_edit_mode == "view") { ?>disabled<?php }?>></TD>
				        <TD align="left" valign="top" style="padding-top: 0.1em;">
        				<?php  putGS('Show article on front page'); ?>
        				</TD>
        			</TR>

        			<!-- Show article on section page -->
        			<tr>
				        <TD ALIGN="RIGHT" valign="top"><INPUT TYPE="CHECKBOX" NAME="f_on_section_page" class="input_checkbox" <?php  if ($articleObj->onSectionPage()) { ?> CHECKED<?php  } ?> <?php if ($f_edit_mode == "view") { ?>disabled<?php }?>></TD>
				        <TD align="left" valign="top"  style="padding-top: 0.1em;">
				            <?php  putGS('Show article on section page'); ?>
				        </TD>
			        </TR>

			        <!-- Article viewable by public -->
			        <tr>
				        <TD ALIGN="RIGHT" valign="top"><INPUT TYPE="CHECKBOX" NAME="f_is_public" class="input_checkbox" <?php  if ($articleObj->isPublic()) { ?> CHECKED<?php  } ?> <?php if ($f_edit_mode == "view") { ?>disabled<?php }?>></TD>
				        <TD align="left" valign="top" style="padding-top: 0.1em;">
							<?php putGS('Visible to non-subscribers'); ?>
				        </TD>
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
			</TR>
			</TABLE>
		</TD>
	</TR>

	<TR>
		<TD style="border-top: 1px solid #8baed1; padding-top: 3px;">
			<TABLE>
			<TR>
				<td align="left" style="padding-right: 5px;">
				<?php if ($f_edit_mode == "edit") { ?>
					<input type="image" src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" name="save" value="save">
				<?php } ?>
				</td>
				<TD ALIGN="RIGHT" ><?php  putGS("Keywords"); ?>:</TD>
				<TD>
					<?php if ($f_edit_mode == "edit") { ?>
					<INPUT TYPE="TEXT" NAME="f_keywords" VALUE="<?php print htmlspecialchars($articleObj->getKeywords()); ?>" class="input_text" SIZE="50" MAXLENGTH="255">
					<?php } else {
						print htmlspecialchars($articleObj->getKeywords());
					}
					?>
				</TD>
			</TR>

			<?php
			// Display the article type fields.
			foreach ($dbColumns as $dbColumn) {

				if (stristr($dbColumn->getType(), "char")
				    /* DO NOT DELETE */ || stristr($dbColumn->getType(), "binary") /* DO NOT DELETE */ ) {
					// The "binary" comparizon is needed for Fedora distro; MySQL on Fedora changes ALL
					// "char" types to "binary".

					// Single line text fields
			?>
			<TR>
				<td align="left" style="padding-right: 5px;">
					<?php if ($f_edit_mode == "edit") { ?>
					<input type="image" src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" name="save" value="save">
					<?php } ?>
				</td>
				<td align="right">
					<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
				</td>
				<TD>
				<?php
				if ($f_edit_mode == "edit") { ?>
		        <INPUT NAME="<?php echo $dbColumn->getName(); ?>"
					   TYPE="TEXT"
					   VALUE="<?php print htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?>"
					   class="input_text"
					   SIZE="50"
					   MAXLENGTH="255">
		        <?php } else {
		        	print htmlspecialchars($articleData->getProperty($dbColumn->getName()));
		        }
		        ?>
				</TD>
			</TR>
			<?php
			} elseif (stristr($dbColumn->getType(), "date")) {
				// Date fields
				if ($articleData->getProperty($dbColumn->getName()) == "0000-00-00") {
					$articleData->setProperty($dbColumn->getName(), "CURDATE()", true, true);
				}
			?>
			<TR>
				<td align="left" style="padding-right: 5px;">
					<?php if ($f_edit_mode == "edit") { ?>
					<input type="image" src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" name="save" value="save">
					<?php } ?>
				</td>
				<td align="right">
					<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
				</td>
				<TD>
				<?php if ($f_edit_mode == "edit") { ?>
				<INPUT NAME="<?php echo $dbColumn->getName(); ?>"
					   TYPE="TEXT"
					   VALUE="<?php echo htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?>"
					   class="input_text"
					   SIZE="11"
					   MAXLENGTH="10">
				<?php } else { ?>
					<span style="padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px; border: 1px solid #888; margin-right: 5px; background-color: #EEEEEE;"><?php echo htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?></span>
					<?php
				}
				?>
				<?php putGS('YYYY-MM-DD'); ?>
				</TD>
			</TR>
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
					foreach ($imageMatches[1] as $templateId) {
						// Get the image URL
						$articleImage =& new ArticleImage($f_article_number, null, $templateId);
						$image =& new Image($articleImage->getImageId());
						$imageUrl = $image->getImageUrl();
						$text = preg_replace("/<!\*\*\s*Image\s*".$templateId."\s*/i", '<img src="'.$imageUrl.'" id="'.$templateId.'" ', $text);
					}
				}
			?>
			<TR>
			<TD ALIGN="RIGHT" VALIGN="TOP" style="padding-top: 8px; padding-right: 5px;">
				<?php if ($f_edit_mode == "edit") { ?>
				<input type="image" src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" name="save" value="save">
				<?php } ?>
			</td>
			<td align="right" valign="top" style="padding-top: 8px;">
				<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
			</td>
			<TD align="left" valign="top">
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
			</TD>
			</TR>
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
			<TD ALIGN="RIGHT" VALIGN="TOP" style="padding-top: 8px; padding-right: 5px;">
				<?php if ($f_edit_mode == "edit") { ?>
				<input type="image" src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" name="save" value="save">
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
    				$TOL_Language = Input::Get('TOL_Language');
    				$currentLanguage =& new Language($TOL_Language);
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
			</TABLE>
		</TD>
	</TR>

	<?php if ($f_edit_mode == "edit") { ?>
	<TR>
		<TD COLSPAN="2" align="center">
			<INPUT TYPE="submit" NAME="save" VALUE="<?php putGS('Save'); ?>" class="button">
			&nbsp;&nbsp;&nbsp;&nbsp;
			<INPUT TYPE="submit" NAME="save_and_close" VALUE="<?php putGS('Save and Close'); ?>" class="button">
		</TD>
	</TR>
	<?php } ?>

	</TABLE>
	<!-- END Article Content -->
</TD>
	<!-- END left side of article screen -->

	<!-- BEGIN right side of article screen -->
	<TD valign="top" style="border-left: 1px solid #8baed1;" width="200px">
		<TABLE width="100%">
		<!-- Begin Scheduled Publishing section -->
		<?php if ($articleObj->getWorkflowStatus() != 'N') { ?>
		<TR><TD>
			<TABLE width="100%" style="border: 1px solid #EEEEEE;">
			<TR>
				<TD>
					<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<TR>
						<TD align="left">
						<b><?php putGS("Publish Schedule"); ?></b>
						</td>
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('Publish')) {  ?>
						<td align="right">
							<table cellpadding="2" cellspacing="0"><tr><td><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" border="0"></td>
							<td><a href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "autopublish.php"); ?>', 'autopublish_window', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=450, height=500, top=200, left=200');"><?php putGS("Add Event"); ?></a></td></tr></table>
						</td>
						<?php } ?>
					</tr>
					</table>
				</td>
			</tr>
			<?php foreach ($articleEvents as $event) { ?>
			<tr>
				<td style="padding-left: 8px;">
					<table cellpadding="0" cellspacing="2">
					<tr>
						<td valign="middle" style="padding-top: 3px;">
							<?php p(htmlspecialchars($event->getActionTime())); ?>
						</td>

						<td style="padding-left: 3px;" valign="middle">
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('Publish')) { ?>
						<a href="<?php p(camp_html_article_url($articleObj, $f_language_id, "autopublish_del.php", '', '&f_event_id='.$event->getArticlePublishId())); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the event scheduled on $1?", camp_javascriptspecialchars($event->getActionTime())); ?>');"><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/unlink.png" border="0"></a>
						<?php } ?>
						</td>
					</tr>
					<?php
					$publishAction = $event->getPublishAction();
					if (!empty($publishAction)) {
						echo "<tr><td colspan=2 style='padding-left: 7px;'>";
						if ($publishAction == "P") {
							putGS("Publish");
						}
						if ($publishAction == "U") {
							putGS("Unpublish");
						}
						echo "</td></tr>";
					}
					$frontPageAction = $event->getFrontPageAction();
					if (!empty($frontPageAction)) {
						echo "<tr><td colspan=2 style='padding-left: 7px;'>";
						if ($frontPageAction == "S") {
							putGS("Show on front page");
						}
						if ($frontPageAction == "R") {
							putGS("Remove from front page");
						}
						echo "</td></tr>";
					}
					$sectionPageAction = $event->getSectionPageAction();
					if (!empty($sectionPageAction)) {
						echo "<tr><td colspan=2 style='padding-left: 7px;'>";
						if ($sectionPageAction == "S") {
							putGS("Show on section page");
						}
						if ($sectionPageAction == "R") {
							putGS("Remove from section page");
						}
						echo "</td></tr>";
					}
					?>
					</table>
				</td>
			</tr>
			<?php } ?>
			</table>
		</TD></TR>
		<?php } ?>
		<!-- End Scheduled Publishing section -->

		<?php if ($showComments) { ?>
		<!-- Begin Comment Info -->
		<tr><td>
			<TABLE width="100%" style="border: 1px solid #EEEEEE;">
			<TR>
				<TD>
					<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<TR>
						<TD align="left">
						<b><?php putGS("Comments"); ?></b>
						</td>
					</tr>
					</table>
				</td>
			</tr>
				<td align="left" width="100%" style="padding-left: 8px;">
				<?php
				if (is_array($comments)) {
					putGS("Total:"); ?> <?php p(count($comments));
				?>
				    <br />
				    <?php if ($f_show_comments) { ?>
				    <a href="<?php echo camp_html_article_url($articleObj, $f_language_selected, "edit.php", "", "&f_show_comments=0"); ?>"><?php putGS("Hide Comments"); ?></a>
				    <?php } else { ?>
				    <a href="<?php echo camp_html_article_url($articleObj, $f_language_selected, "edit.php", "", "&f_show_comments=1"); ?>"><?php putGS("Show Comments"); ?></a>
				    <?php }
				} else {
					putGS("Comments Disabled");
				}
				?>
				
                </td>
            </tr>
            </table>
		</td></tr>
		<!-- End Comment Info -->
        <?php } ?>

		<TR><TD>
			<!-- BEGIN Images table -->
			<TABLE width="100%" style="border: 1px solid #EEEEEE;">
			<TR>
				<TD>
					<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<TR>
						<TD align="left">
						<b><?php putGS("Images"); ?></b>
						</td>
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('AttachImageToArticle')) {  ?>
						<td align="right">
							<img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" border="0">
							<a href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "images/popup.php"); ?>', 'attach_image', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=750, height=600, top=200, left=100');"><?php putGS("Attach"); ?></a>
						</td>
						<?php } ?>
					</tr>
					</table>
				</td>
			</tr>
			<?PHP
			foreach ($articleImages as $tmpArticleImage) {
				$image = $tmpArticleImage->getImage();
				$imageEditUrl = "/$ADMIN/articles/images/edit.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_image_id=".$image->getImageId()."&f_language_id=$f_language_id&f_language_selected=$f_language_selected&f_image_template_id=".$tmpArticleImage->getTemplateId();
				$detachUrl = "/$ADMIN/articles/images/do_unlink.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_image_id=".$image->getImageId()."&f_language_selected=$f_language_selected&f_language_id=$f_language_id&f_image_template_id=".$tmpArticleImage->getTemplateId();
				$imageSize = getimagesize($image->getImageStorageLocation());
			?>
			<tr>
				<td align="center" width="100%">
					<table>
					<tr>
						<td align="center" valign="middle">
							<?php echo $tmpArticleImage->getTemplateId(); ?>.
						</td>
						<td align="center" valign="middle">
							<?php if ($f_edit_mode == "edit") { ?><a href="<?php p($imageEditUrl); ?>"><?php } ?><img src="<?php p($image->getThumbnailUrl()); ?>" border="0"><?php if ($f_edit_mode == "edit") { ?></a><?php } ?>
						</td>
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('AttachImageToArticle')) { ?>
						<td>
							<a href="<?php p($detachUrl); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the image \\'$1\\' from the article?", camp_javascriptspecialchars($image->getDescription())); ?>');"><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/unlink.png" border="0"></a>
						</td>
						<?php } ?>
					</tr>
					<tr>
						<td></td>
						<td align="center"><?php p($imageSize[0]."x".$imageSize[1]); ?></td>
						<td></td>
					</tr>
					</table>
				</td>
			</tr>
			<?php } ?>
			</TABLE>
			<!-- END Images table -->
		</TD></TR>


		<TR><TD>
			<!-- BEGIN Files table -->
			<TABLE width="100%" style="border: 1px solid #EEEEEE;">
			<TR>
				<TD>
					<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<TR>
						<TD align="left">
						<b><?php putGS("Files"); ?></b>
						</td>
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('AddFile')) {  ?>
						<td align="right">
							<img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" border="0">
							<a href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "files/popup.php"); ?>', 'attach_file', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=500, height=400, top=200, left=100');"><?php putGS("Attach"); ?></a>
						</td>
						<?php } ?>
					</tr>
					</table>
				</td>
			</tr>
			<?PHP
			foreach ($articleFiles as $file) {
				$fileEditUrl = "/$ADMIN/articles/files/edit.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_attachment_id=".$file->getAttachmentId()."&f_language_id=$f_language_id&f_language_selected=$f_language_selected";
				$deleteUrl = "/$ADMIN/articles/files/do_del.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_attachment_id=".$file->getAttachmentId()."&f_language_selected=$f_language_selected&f_language_id=$f_language_id";
				$downloadUrl = "/attachment/".basename($file->getStorageLocation())."?g_download=1";
				if (strstr($file->getMimeType(), "image/") && (strstr($_SERVER['HTTP_ACCEPT'], $file->getMimeType()) ||
										(strstr($_SERVER['HTTP_ACCEPT'], "*/*")))) {
				$previewUrl = "/attachment/".basename($file->getStorageLocation())."?g_show_in_browser=1";
				}
			?>
			<tr>
				<td align="center" width="100%">
					<table>
					<tr>
						<td align="center" valign="top">
							<?php if ($f_edit_mode == "edit") { ?><a href="<?php p($fileEditUrl); ?>"><?php } p(wordwrap($file->getFileName(), "25", "<br>", true)); ?><?php if ($f_edit_mode == "edit") { ?></a><?php } ?><br><?php p(htmlspecialchars($file->getDescription($f_language_selected))); ?>
						</td>
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('DeleteFile')) { ?>
						<td>
							<a title="<?php putGS("Delete"); ?>" href="<?php p($deleteUrl); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the file \\'$1\\' from the article?", camp_javascriptspecialchars($file->getFileName())); ?>');"><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/unlink.png" border="0" /></a><br />
							<?php if (!empty($previewUrl)) { ?>
							<a title="<?php putGS("Preview"); ?>" href="javascript: void(0);" onclick="window.open('<?php echo $previewUrl; ?>', 'attach_file', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=500, height=400, top=200, left=100');"><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/preview-16x16.png" border="0" /></a>
							<?php } ?>
						</td>
						<?php } ?>
					</tr>
					<tr>
						<td align="center"><?php p(camp_format_bytes($file->getSizeInBytes())); ?> <a title="<?php putGS("Download"); ?>" href="<?php p($downloadUrl); ?>"><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/download.png" border="0" /></a></td>
						<td></td>
					</tr>
					</table>
				</td>
			</tr>
			<?php } ?>
			</TABLE>
			<!-- END Files table -->
		</TD></TR>



		<TR><TD>
			<!-- BEGIN TOPICS table -->
			<TABLE width="100%" style="border: 1px solid #EEEEEE;">
			<TR>
				<TD>
					<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<TR>
						<TD align="left">
						<b><?php putGS("Topics"); ?></b>
						</td>
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('AttachTopicToArticle')) {  ?>
						<td align="right">
							<img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" border="0">
							<a href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "topics/popup.php"); ?>', 'attach_topic', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=300, height=400, top=200, left=200');"><?php putGS("Attach"); ?></a>
						</td>
						<?php } ?>
					</tr>
					</table>
				</td>
			</tr>
			<?PHP
			foreach ($articleTopics as $tmpArticleTopic) {
				$detachUrl = "/$ADMIN/articles/topics/do_del.php?f_article_number=$f_article_number&f_topic_id=".$tmpArticleTopic->getTopicId()."&f_language_selected=$f_language_selected&f_language_id=$f_language_id";
			?>
			<tr>
				<td align="center" width="100%" style="border-top: 1px solid #EEEEEE;">
					<table>
					<tr>
						<td align="center" valign="middle">
							<?php
							$path = $tmpArticleTopic->getPath();
							$pathStr = "";
							foreach ($path as $element) {
								$name = $element->getName($f_language_selected);
								if (empty($name)) {
									// For backwards compatibility -
									// get the english translation if the translation
									// doesnt exist for the article's language.
									$name = $element->getName(1);
									if (empty($name)) {
										$name = "-----";
									}
								}
								$pathStr .= " / ". htmlspecialchars($name);
							}

							// Get the topic name for the 'detach topic' dialog box, below.
							$tmpTopicName = $tmpArticleTopic->getName($f_language_selected);
							// For backwards compatibility.
							if (empty($tmpTopicName)) {
								$tmpTopicName = $tmpArticleTopic->getName(1);
							}
							?>
							<?php p(wordwrap($pathStr, 25, "<br>&nbsp;&nbsp;")); ?>
						</td>
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('AttachTopicToArticle')) { ?>
						<td>
							<a href="<?php p($detachUrl); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the topic \\'$1\\' from the article?", camp_javascriptspecialchars($tmpTopicName)); ?>');"><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/unlink.png" border="0"></a>
						</td>
						<?php } ?>
					</tr>
					</table>
				</td>
			</tr>
			<?php } ?>
			</TABLE>
			<!-- END TOPICS table -->
		</TD></TR>


		<TR><TD>
			<!-- BEGIN AUDIO CLIPS table -->
			<TABLE width="100%" style="border: 1px solid #EEEEEE;">
			<TR>
				<TD>
					<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<TR>
						<TD align="left">
						<b><?php putGS("Audio clips"); ?></b>
						</td>
                        <?php if (($f_edit_mode == "edit") && $g_user->hasPermission('AttachAudioclipToArticle')) {  ?>
						<td align="right">
							<img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" border="0">
                            <a href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php"); ?>', 'attach_audioclip', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=750, height=600, top=200, left=100');"><?php putGS("Attach"); ?></a>
						</td>
						<?php } ?>
					</tr>
					</table>
				</td>
			</tr>
            <style type="text/css">@import url(<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/domTT/domTT.css);</style>
            <script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/domTT/domLib.js"></script>
            <script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/domTT/domTT.js"></script>
            <script type="text/javascript">
                var domTT_styleClass = 'domTTOverlib';
            </script>
            <?php
            foreach($articleAudioclips as $articleAudioclip) {
                $allTags = '';
                $aClipMetaTags = $articleAudioclip->getAvailableMetaTags();
                foreach ($aClipMetaTags as $metaTag) {
                    list($nameSpace, $localPart) = explode(':', strtolower($metaTag));
                    if ($localPart == 'title') {
                        $allTags .= '<strong>'.$metatagLabel[$metaTag] . ': ' . $articleAudioclip->getMetatagValue($localPart) . '</strong><br />';
                    } else {
                        $allTags .= $metatagLabel[$metaTag] . ': ' . $articleAudioclip->getMetatagValue($localPart) . '<br />';
                    }
                }
                if (($f_edit_mode == "edit")
                    && $g_user->hasPermission('AttachAudioclipToArticle')) {
                    $aClipEditUrl = "/$ADMIN/articles/audioclips/edit.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_action=edit&f_audioclip_gunid=".$articleAudioclip->getGunId()."&f_language_id=$f_language_id&f_language_selected=$f_language_selected";
                    $aClipDeleteUrl = "/$ADMIN/articles/audioclips/do_del.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_audioclip_gunid=".$articleAudioclip->getGunId()."&f_language_selected=$f_language_selected&f_language_id=$f_language_id";
                    $audioclipEditLink = '<a href="javascript: void(0);" onclick="window.open(\''.$aClipEditUrl.'\', \'attach_audioclip\', \'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=750, height=600, top=200, left=100\');" onmouseover="domTT_activate(this, event, \'content\', \''.$allTags.'\', \'trail\', true, \'delay\', 0);">'.wordwrap($articleAudioclip->getMetatagValue('title'), '25', '<br />', true).'</a>';
                    $audioclipDeleteLink = '<a href="'.$aClipDeleteUrl.'" onclick="return confirm(\''.getGS("Are you sure you want to remove the audio file \'$1\' from the article?", camp_javascriptspecialchars($articleAudioclip->getMetatagValue('title'))).'\');"><img src="'.$Campsite['ADMIN_IMAGE_BASE_URL'].'/unlink.png" border="0" /></a>';
                    $audioclipLink = $audioclipEditLink . ' ' . $audioclipDeleteLink;
                } else {
                    $audioclipLink = '<a href="#" onmouseover="domTT_activate(this, event, \'content\', \''.$allTags.'\', \'trail\', true, \'delay\', 0);">'.wordwrap($articleAudioclip->getMetatagValue('title'), '25', '<br />', true).'</a>';
                }
            ?>
            <tr>
                <td align="center" width="100%" style="border-top: 1px solid #EEEEEE;">
                    <table>
					<tr>
						<td align="center" valign="middle" nowrap>
                        <?php putGS("Title"); ?>: <?php p($audioclipLink); ?>
                        <br />
                        <?php putGS("Creator"); ?>: <?php p($articleAudioclip->getMetatagValue('creator')); ?>
                        <br />
                        <?php putGS("Length"); ?>: <?php p(camp_time_format($articleAudioclip->getMetatagValue('extent'))); ?>
                        </td>
                    </tr>
                    </table>
                </td>
            </tr>
            <?php
            } // foreach($articleAudioclips as $articleAudioclip) {
            ?>
            </table>
            <!-- END AUDIO CLIPS table -->
          </td></tr>
		</TABLE>
	</TD>
</TR>
</TABLE>
</FORM>
<?php
if ($showComments && $f_show_comments) {
    include("comments/show_comments.php");
}

camp_html_copyright_notice();
?>
