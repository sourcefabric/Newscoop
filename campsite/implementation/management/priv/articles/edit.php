<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/editor_load_xinha.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticlePublish.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleAttachment.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

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
$f_language_selected = camp_session_get('f_language_selected', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$errorStr = "";

// Fetch article
$articleObj =& new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	$errorStr = getGS('No such article.');
}
$articleData = $articleObj->getArticleData();
// Get article type fields.
$dbColumns = $articleData->getUserDefinedColumns();
$articleImages = ArticleImage::GetImagesByArticleNumber($f_article_number);
$lockUserObj =& new User($articleObj->getLockedByUser());
$articleCreator =& new User($articleObj->getCreatorId());
$articleEvents = ArticlePublish::GetArticleEvents($f_article_number, $f_language_selected, true);
$articleTopics = ArticleTopic::GetArticleTopics($f_article_number);

$articleFiles = ArticleAttachment::GetAttachmentsByArticleNumber($f_article_number, $f_language_selected);
$articleLanguages = $articleObj->getLanguages();

if ($f_publication_id > 0) {
	$publicationObj =& new Publication($f_publication_id);
	$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
	$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
}

// Automatically switch to "view" mode if user doesnt have permissions.
if (!$articleObj->userCanModify($User)) {
	$f_edit_mode = "view";
}

//
// Automatic unlocking
//
$locked = true;
// If the article hasnt been touched in 24 hours
$timeDiff = camp_time_diff_str($articleObj->getLockTime());
if ( $timeDiff['days'] > 0 ) {
	$articleObj->unlock();
	$locked = false;
}
// If the user who locked the article doesnt exist anymore, unlock the article.
elseif (($articleObj->getLockedByUser() != 0) && !$lockUserObj->exists()) {
	$articleObj->unlock();
	$locked = false;
}

//
// Automatic locking
//

// If the article has not been unlocked and is not locked by a user.
if ($f_unlock === false) {
    if (!$articleObj->isLocked()) {
		// Lock the article
		$articleObj->lock($User->getUserId());
    }
}
else {
	$f_edit_mode = "view";
}

// If the article is locked by the current user, OK to edit.
if ($articleObj->getLockedByUser() == $User->getUserId()) {
    $locked = false;
}

//
// Begin Display of page
//
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
	editor_load_xinha($dbColumns, $User);
}

if ($errorStr != "") {
	camp_html_display_error($errorStr);
	return;
}

// If the article is locked.
if ($articleObj->userCanModify($User) && $locked) {
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
				<?php putGS('Are you sure you want to unlock it?'); ?>
			</BLOCKQUOTE>
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" NAME="Yes" VALUE="<?php  putGS('Yes'); ?>" class="button" ONCLICK="location.href='<?php echo camp_html_article_url($articleObj, $f_language_id, "do_unlock.php"); ?>'">
		<INPUT TYPE="button" NAME="No" VALUE="<?php  putGS('No'); ?>" class="button" ONCLICK="location.href='/<?php echo $ADMIN; ?>/articles/?f_publication_id=<?php  p($f_publication_id); ?>&f_issue_number=<?php  p($f_issue_number); ?>&f_language_id=<?php p($f_language_id); ?>&f_section_number=<?php  p($f_section_number); ?>'">
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

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="<?php echo "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"; ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="<?php echo "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"; ?>"><B><?php  putGS("Article List"); ?></B></A></TD>

	<?php if ($User->hasPermission('AddArticle')) { ?>
	<TD style="padding-left: 20px;"><A HREF="add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
	<TD><A HREF="add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>" ><B><?php  putGS("Add new article"); ?></B></A></TD>
<?php  } ?>
</tr>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" class="table_input" width="900px" style="margin-top: 5px;">
<TR>
	<TD width="700px" style="border-bottom: 1px solid #8baed1;" colspan="2">
		<!-- for the left side of the article edit screen -->
		<TABLE cellpadding="0" cellspacing="0">
		<tr>
			<td>

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
				<TR>
					<?PHP
					if ($articleObj->userCanModify($User)) {
					$switchModeUrl = camp_html_article_url($articleObj, $f_language_id, "edit.php")
						."&f_edit_mode=".( ($f_edit_mode =="edit") ? "view" : "edit");
					?>
					<TD style="padding-left: 8px;"><a href="<?php p($switchModeUrl); ?>"><b><?php if ($f_edit_mode == "edit") { putGS("View"); } else { putGS("Edit"); } ?></b></a></TD>
					<?php } ?>

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

						<?php if ($articleObj->userCanModify($User) && $articleObj->isLocked()) { ?>
						<OPTION value="unlock"><?php putGS("Unlock"); ?></OPTION>
						<?php } ?>

						<?php  if ($User->hasPermission('DeleteArticle')) { ?>
						<OPTION value="delete"><?php putGS("Delete"); ?></OPTION>
						<?php } ?>

						<?php  if ($User->hasPermission('AddArticle')) { ?>
						<OPTION value="copy"><?php putGS("Duplicate"); ?></OPTION>
						<?php } ?>

						<?php if ($User->hasPermission('TranslateArticle')) { ?>
						<OPTION value="translate"><?php putGS("Translate"); ?></OPTION>
						<?php } ?>

						<?php if ($User->hasPermission('MoveArticle')) { ?>
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
						if ($User->hasPermission("Publish")) { ?>
						<SELECT name="f_action_workflow" class="input_select" onchange="this.form.submit();">
						<?php
						camp_html_select_option("Y", $articleObj->getPublished(), getGS("Status: Published"));
						camp_html_select_option("S", $articleObj->getPublished(), getGS("Status: Submitted"));
						camp_html_select_option("N", $articleObj->getPublished(), getGS("Status: New"));
						?>
						</SELECT>
						<?php } elseif ($articleObj->userCanModify($User) && ($articleObj->getPublished() != 'Y')) { ?>
						<SELECT name="f_action_workflow" class="input_select" onchange="this.form.submit();">
						<?php
						camp_html_select_option("S", $articleObj->getPublished(), getGS("Status: Submitted"));
						camp_html_select_option("N", $articleObj->getPublished(), getGS("Status: New"));
						?>
						</SELECT>
						<?php } else {
							switch ($articleObj->getPublished()) {
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
							<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/automatic_publishing.png" alt="<?php  putGS("Scheduled Publishing"); ?>" title="<?php  putGS("Scheduled Publishing"); ?>" border="0" width="22" height="22" align="middle" style="padding-bottom: 1px;">
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
	<table>
	<TR>
		<TD style="padding-top: 3px;">
			<TABLE>
			<TR>
				<TD ALIGN="RIGHT" valign="top" ><b><?php  putGS("Name"); ?>:</b></TD>
				<TD rowspan="2" align="left" valign="top">
					<?php if ($f_edit_mode == "edit") { ?>
					<TEXTAREA name="f_article_title" cols="30" rows="2" class="input_text"><?php  print htmlspecialchars($articleObj->getTitle()); ?></TEXTAREA>
					<?php } else {
						print wordwrap(htmlspecialchars($articleObj->getTitle()), 60, "<br>");
					}
					?>
				</TD>
				<TD ALIGN="RIGHT" valign="top"><b><?php  putGS("Created by"); ?>:</b></TD>
				<TD align="left" valign="top"><?php p(htmlspecialchars($articleCreator->getRealName())); ?></TD>
				<TD ALIGN="RIGHT" valign="top"><INPUT TYPE="CHECKBOX" NAME="f_on_front_page" class="input_checkbox" <?php  if ($articleObj->onFrontPage()) { ?> CHECKED<?php  } ?> <?php if ($f_edit_mode == "view") { ?>disabled<?php }?>></TD>
				<TD align="left" valign="top" style="padding-top: 0.25em;">
				<?php  putGS('Show article on front page'); ?>
				</TD>
			</TR>
			<TR>
				<Td>&nbsp;</TD>
				<TD ALIGN="RIGHT" valign="top" style="padding-left: 1em;"><b><nobr><?php  putGS("Creation date"); ?>:</nobr></b></TD>
				<TD align="left" valign="top" nowrap>
					<?php if ($f_edit_mode == "edit") { ?>
					<input type="hidden" name="f_creation_date" value="<?php p($articleObj->getCreationDate()); ?>" id="f_creation_date">
					<table cellpadding="0" cellspacing="2"><tr>
						<td><span id="show_date"><?php p($articleObj->getCreationDate()); ?></span></td>
						<td><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/calendar.gif" id="f_trigger_c"
					    	 style="cursor: pointer; border: 1px solid red;"
					     	 title="Date selector"
					     	 onmouseover="this.style.background='red';"
					     	 onmouseout="this.style.background=''" /></td>
					</tr></table>
					<script type="text/javascript">
					    Calendar.setup({
					        inputField     :    "f_creation_date",
					        ifFormat       :    "%Y-%m-%d",
					        displayArea    :    "show_date",
					        daFormat	   :    "%Y-%m-%d",
					        button		   :    "f_trigger_c"
					    });
					</script>
					<?php } else { ?>
					<?php print $articleObj->getCreationDate(); ?>
					<?php } ?>
				</TD>
				<TD ALIGN="RIGHT" valign="top" style="padding-left: 1em;"><INPUT TYPE="CHECKBOX" NAME="f_on_section_page" class="input_checkbox" <?php  if ($articleObj->onSectionPage()) { ?> CHECKED<?php  } ?> <?php if ($f_edit_mode == "view") { ?>disabled<?php }?>></TD>
				<TD align="left" valign="top"  style="padding-top: 0.25em;">
				<?php  putGS('Show article on section page'); ?>
				</TD>
			</TR>
			<TR>
				<TD ALIGN="RIGHT" valign="top" style="padding-left: 1em;"><b><?php  putGS("Type"); ?>:</b></TD>
				<TD align="left" valign="top">
					<?php print htmlspecialchars($articleObj->getType()); ?>
				</TD>
				<TD ALIGN="RIGHT" valign="top" style="padding-left: 1em;"><b><?php  putGS("Publish date"); ?>:</b></TD>
				<TD align="left" valign="top">
					<?php print htmlspecialchars($articleObj->getPublishDate()); ?>
				</TD>
				<TD ALIGN="RIGHT" valign="top" style="padding-left: 1em;"><INPUT TYPE="CHECKBOX" NAME="f_is_public" class="input_checkbox" <?php  if ($articleObj->isPublic()) { ?> CHECKED<?php  } ?> <?php if ($f_edit_mode == "view") { ?>disabled<?php }?>></TD>
				<TD align="left" valign="top" style="padding-top: 0.25em;">
				<?php putGS('Allow users without subscriptions to view the article'); ?>
				</TD>
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
					<INPUT TYPE="TEXT" NAME="f_keywords" VALUE="<?php print htmlspecialchars($articleObj->getKeywords()); ?>" class="input_text" SIZE="64" MAXLENGTH="255">
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
					<?php echo htmlspecialchars($dbColumn->getPrintName()); ?>:
				</td>
				<TD>
				<?php
				if ($f_edit_mode == "edit") { ?>
		        <INPUT NAME="<?php echo $dbColumn->getName(); ?>"
					   TYPE="TEXT"
					   VALUE="<?php print $articleData->getProperty($dbColumn->getName()); ?>"
					   class="input_text"
					   SIZE="64"
					   MAXLENGTH="100">
		        <?php } else {
		        	print $articleData->getProperty($dbColumn->getName());
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
					<?php echo htmlspecialchars($dbColumn->getPrintName()); ?>:
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
				$text = preg_replace("/<!\*\*\s*Link\s*Internal\s*([\w=&]*)\s*target\s*([\w_]*)\s*>/i", '<a href="campsite_internal_link?$1" target="$2">', $text);
				// Internal Links without targets
				$text = preg_replace("/<!\*\*\s*Link\s*Internal\s*([\w=&]*)\s*>/i", '<a href="campsite_internal_link?$1">', $text);
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
						$text = preg_replace("/<!\*\*\s*Image\s*".$templateId."\s*/i", '<img src="'.$imageUrl.'" ', $text);
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
				<?php echo htmlspecialchars($dbColumn->getPrintName()); ?>:
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
				<?php echo $articleTypeField->getPrintName(); ?>:
			</td>
			<td>
				<select class="input_select" name="<?php echo $dbColumn->getName(); ?>">
				<option value="0"></option>
				<?php
				foreach ($subtopics as $topicPath) {
					$printTopic = array();
					foreach ($topicPath as $topicId => $topic) {
						$translations = $topic->getTranslations();
						if (array_key_exists($currentLanguageId, $translations)) {
							$currentTopic = $translations[$currentLanguageId];
						} elseif ($currentLanguageId != 1 && array_key_exists(1, $translations)) {
							$currentTopic = $translations[1];
						} else {
							$currentTopic = end($translations);
						}
						$printTopic[] = $currentTopic;
					}
					camp_html_select_option($topicId, $articleTopicId,
											htmlspecialchars(implode(" / ", $printTopic)));
				}
				?>
				</select>
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
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save'); ?>" class="button">
		</DIV>
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
		<?php if ($articleObj->getPublished() != 'N') { ?>
		<TR><TD>
			<TABLE width="100%" style="border: 1px solid #EEEEEE;">
			<TR>
				<TD>
					<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<TR>
						<TD align="left">
						<b><?php putGS("Publish Schedule"); ?></b>
						</td>
						<?php if (($f_edit_mode == "edit") && $User->hasPermission('Publish')) {  ?>
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
						<?php if (($f_edit_mode == "edit") && $User->hasPermission('Publish')) { ?>
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
						<?php if (($f_edit_mode == "edit") && $User->hasPermission('AttachImageToArticle')) {  ?>
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
						<?php if (($f_edit_mode == "edit") && $User->hasPermission('AttachImageToArticle')) { ?>
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
						<?php if (($f_edit_mode == "edit") && $User->hasPermission('AddFile')) {  ?>
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
			?>
			<tr>
				<td align="center" width="100%">
					<table>
					<tr>
						<td align="center" valign="middle">
							<?php if ($f_edit_mode == "edit") { ?><a href="<?php p($fileEditUrl); ?>"><?php } p(wordwrap($file->getFileName(), "25", "<br>", true)); ?><?php if ($f_edit_mode == "edit") { ?></a><?php } ?><br><?php p($file->getDescription($f_language_selected)); ?>
						</td>
						<?php if (($f_edit_mode == "edit") && $User->hasPermission('DeleteFile')) { ?>
						<td>
							<a href="<?php p($deleteUrl); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the file \\'$1\\' from the article?", camp_javascriptspecialchars($file->getFileName())); ?>');"><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/unlink.png" border="0"></a>
						</td>
						<?php } ?>
					</tr>
					<tr>
						<td align="center"><?php p(camp_format_bytes($file->getSizeInBytes())); ?></td>
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
						<?php if (($f_edit_mode == "edit") && $User->hasPermission('AttachTopicToArticle')) {  ?>
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
								$pathStr .= " / ". $name;
							}

							// Get the topic name for the 'detach topic' dialog box, below.
							$tmpTopicName = $tmpArticleTopic->getName($f_language_selected);
							// For backwards compatibility.
							if (empty($tmpTopicName)) {
								$tmpTopicName = $tmpArticleTopic->getName(1);
							}
							?>
							<?php p(wordwrap($pathStr, 25, "<br>&nbsp;&nbsp;", true)); ?>
						</td>
						<?php if (($f_edit_mode == "edit") && $User->hasPermission('AttachTopicToArticle')) { ?>
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

		</TABLE>
	</TD>
</TR>
</TABLE>
</FORM>
<?php
camp_html_copyright_notice();
?>