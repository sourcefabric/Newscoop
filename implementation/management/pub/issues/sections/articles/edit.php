<?php  
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticlePublish.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$Saved = Input::Get('Saved', 'int', 0, true);
$Unlock = Input::Get('Unlock', 'string', false, true);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}

$errorStr = "";

// Fetch article
$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	$errorStr = getGS('No such article.');
}
$articleType =& $articleObj->getArticleTypeObject();
$lockUserObj =& new User($articleObj->getLockedByUser());
$languageObj =& new Language($Language);
$sLanguageObj =& new Language($sLanguage);
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
$articleTemplate =& new Template($issueObj->getArticleTemplateId());

// If the user has the ability to change the article OR
// the user created the article and it hasnt been published.
$hasAccess = false;
if ($articleObj->userCanModify($User)) {
	$hasAccess = true;
	$edit_ok = 0;
	
	//
	// Automatic unlocking
	//
	// If the article hasnt been touched in 24 hours
	$timeDiff = camp_time_diff_str($articleObj->getLockTime());
	if ( $timeDiff['days'] > 0 ) {
		$articleObj->unlock();
		$edit_ok = 1;		
	}
	// If the user who locked the article doesnt exist anymore, unlock the article.
	elseif (($articleObj->getLockedByUser() != 0) && !$lockUserObj->exists()) {
		$articleObj->unlock();
		$edit_ok = 1;
	}
	
	// Automatic locking
	// If the article has not been unlocked and is not locked by a user.
	if ($Unlock === false) {
	    if (!$articleObj->isLocked()) {
    		// Lock the article
    		$articleObj->lock($User->getId());
	    }
	} 
	
	// If the article is locked by the current user, OK to edit.
	if ($articleObj->getLockedByUser() == $User->getId()) {
	    $edit_ok = 1;
	}
}

if ($User->hasPermission('AddArticle')) { 
	// Added by sebastian.
	if (function_exists ("incModFile")) {
		incModFile ();
	}
}

// Check if everything needed for Article Import is available.
$zipLibAvailable = function_exists("zip_open");
$xsltLibAvailable = function_exists("xslt_create");
@include_once("XML/Parser.php");
$xmlLibAvailable = class_exists("XML_Parser");
$xmlLibAvailable |= function_exists("xml_parser_create");
// Verify this article type has the body & intro fields.
$introSupport = false;
$bodySupport = false;
$dbColumns = $articleType->getUserDefinedColumns();
foreach ($dbColumns as $dbColumn) {
	if ($dbColumn->getName() == "Fintro") {
		$introSupport = true;
	}
	if ($dbColumn->getName() == "Fbody") {
		$bodySupport = true;
	}
}

// Begin Display of page
$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
				  'Section' => $sectionObj, 'Article'=>$articleObj);
CampsiteInterface::ContentTop(getGS("Edit article details"), $topArray);
editor_load_xinha($dbColumns, $User);

if ($errorStr != "") {
	CampsiteInterface::DisplayError($errorStr);
	return;
}

if (!$hasAccess) {
	?>
	<P>
	<CENTER>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" ALIGN="CENTER">
		<TR>
			<TD COLSPAN="2">
				<B> <font color="red"><?php  putGS("Access denied"); ?> </font></B>
				<HR NOSHADE SIZE="1" COLOR="BLACK">
			</TD>
		</TR>
		<TR>
			<TD COLSPAN="2"><BLOCKQUOTE><font color=red><li><?php  putGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users." ); ?></li></font></BLOCKQUOTE></TD>
		</TR>
		<TR>
			<TD COLSPAN="2">
			<DIV ALIGN="CENTER">
			<INPUT TYPE="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" class="button" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/sections/articles?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>'">
			</DIV>
			</TD>
		</TR>
	</TABLE></CENTER>
	</FORM>
	<P>
	<?php	
}

// If the article is locked.
if ($hasAccess && !$edit_ok) {
	?><P>
	<CENTER>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" ALIGN="CENTER">
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
						  '<B>'.htmlspecialchars($lockUserObj->getName()),
						  htmlspecialchars($lockUserObj->getUserName()).'</B>',
						  $timeDiff['hours'], $timeDiff['minutes']); 
				}
				else {
					putGS('The article has been locked by $1 ($2) $3 minute(s) ago.',
						  '<B>'.htmlspecialchars($lockUserObj->getName()),
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
		<INPUT TYPE="button" NAME="Yes" VALUE="<?php  putGS('Yes'); ?>" class="button" ONCLICK="location.href='<?php echo CampsiteInterface::ArticleUrl($articleObj, $sLanguage, "do_unlock.php"); ?>'">
		<INPUT TYPE="button" NAME="No" VALUE="<?php  putGS('No'); ?>" class="button" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php p($Language); ?>&Section=<?php  p($Section); ?>'">
		</DIV>
		</TD>
	</TR>
	</TABLE></CENTER>
	<P>
	<?php  
}

if ($edit_ok) { ?>
<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
<TR>
	<TD>
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
		<?php 
		if ($articleObj->getPublished() == "Y") { 
			if ($User->hasPermission('Publish')) { 
				?>
				<TD class="action_link_container">
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
					<TR>
						<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $languageObj->getLanguageId(), "status.php", $_SERVER['REQUEST_URI']); ?><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/unpublish.png" BORDER="0"></A></TD>
						<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $languageObj->getLanguageId(), "status.php", $_SERVER['REQUEST_URI']); ?><B><?php  putGS("Unpublish"); ?></B></A></TD>
					</TR>
					</TABLE>
				</TD>
				<?php  
			} 
		} 
		elseif ($articleObj->getPublished() == "S") { 
			if ($User->hasPermission('Publish')) { 
				?>
				<TD class="action_link_container">
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
					<TR>
						<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $languageObj->getLanguageId(), "status.php", $_SERVER['REQUEST_URI']); ?><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/publish.png" BORDER="0"></A></TD>
						<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $languageObj->getLanguageId(), "status.php", $_SERVER['REQUEST_URI']); ?><B><?php  putGS("Publish"); ?></B></A></TD>
					</TR>
					</TABLE>
				</TD>
				<?php
			} 
		} 
		elseif ($articleObj->getPublished() == "N") { 
			if ($User->hasPermission("Publish") || ($articleObj->getUserId() == $User->getId())) {
				?>
				<TD class="action_link_container">
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
					<TR>
						<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $languageObj->getLanguageId(), "status.php", $_SERVER['REQUEST_URI']); ?>
						<IMG SRC="/<?php echo $ADMIN; ?>/img/icon/submit.png" BORDER="0"></A></TD>
						<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $languageObj->getLanguageId(), "status.php", $_SERVER['REQUEST_URI']); ?><B><?php  putGS("Submit"); ?></B></A></TD>
					</TR>
					</TABLE>
				</TD>
				<?php  
			}
		} 
		
		if ($User->hasPermission('AddImage') || $User->hasPermission('DeleteImage') || $articleObj->userCanModify($User) || $User->hasPermission('ChangeImage')) {
		?>
			<TD class="action_link_container">
				<!-- Images Link -->
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
				<TR>
					<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $Language, "images/"); ?><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/image_archive.png" BORDER="0"></A></TD>
					<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $Language, "images/"); ?><B><?php  putGS("Images"); ?></B></A></TD>
				</TR>
				</TABLE>
			</TD>
		<?php
		}
		?>
			<TD class="action_link_container">
				<!-- Topics Link -->
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
				<TR>
					<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $languageObj->getLanguageId(), "topics/"); ?><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/topics.png" BORDER="0"></A></TD>
					<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $languageObj->getLanguageId(), "topics/"); ?><B><?php  putGS("Topics"); ?></B></A></TD>
				</TR>
				</TABLE>
			</TD>

		<?php if ($User->getId() == $articleObj->getLockedByUser()) { ?>
			<TD class="action_link_container">
				<!-- Unlock Link -->
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
				<TR>
					<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $languageObj->getLanguageId(), "do_unlock.php"); ?><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/unlock.png" BORDER="0"></A></TD>
					<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $languageObj->getLanguageId(), "do_unlock.php"); ?><B><?php  putGS("Unlock"); ?></B></A></TD>
				</TR>
				</TABLE>
			</TD>
		<?php } ?>

		<?php 
		if ($User->hasPermission('Publish')) { 
			$automaticPublishingActive = (count(ArticlePublish::GetArticleEvents(
				$articleObj->getArticleId(), $articleObj->getLanguageId())) > 0);
			?>
			<TD class="action_link_container">
				<!-- Autopublish Link -->
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
				<TR>
					<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $languageObj->getLanguageId(), "autopublish.php", $_SERVER['REQUEST_URI']); ?><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/<?php if ($automaticPublishingActive) { ?>automatic_publishing_active.png<?php } else { ?>automatic_publishing.png<?php } ?>" BORDER="0"></A></TD>
					<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $languageObj->getLanguageId(), "autopublish.php", $_SERVER['REQUEST_URI']); ?><B><?php  putGS("Scheduled Publishing"); ?></B></A></TD>
				</TR>
				</TABLE>
			</TD>
		<?php } ?>
		</TR>		
		
		<TR>
			<TD class="action_link_container">
				<!-- Preview Link -->
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
				<TR>	
					<TD><A HREF="" ONCLICK="window.open('/<?php echo $ADMIN; ?>/pub/issues/sections/articles/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=no, width=680, height=560'); return false"><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/preview.png" BORDER="0"></A></TD>
					<TD><A HREF="" ONCLICK="window.open('/<?php echo $ADMIN; ?>/pub/issues/sections/articles/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>', 'fpreview', 'resizable=yes, menubar=yes, toolbar=yes, width=680, height=560'); return false"><B><?php  putGS("Preview"); ?></B></A></TD>
				</TR>
				</TABLE>
			</TD>

			<?php  
			if ($User->hasPermission('AddArticle')) { 
				?>
				<TD class="action_link_container">
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
					<TR>
						<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $languageObj->getLanguageId(), "translate.php", $_SERVER['REQUEST_URI']); ?><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/translate.png" BORDER="0"></A></TD>
						<TD><?php echo CampsiteInterface::ArticleLink($articleObj, $languageObj->getLanguageId(), "translate.php", $_SERVER['REQUEST_URI']); ?><B><?php  putGS("Translate"); ?></B></A></TD>
					</TR>
					</TABLE>
				</TD>
				<?php  
			} 

			if ($User->hasPermission('DeleteArticle')) { 
				?>
				<TD class="action_link_container">
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
					<TR>
						<TD><a href="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/do_del.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the article $1 ($2)?', '&quot;'.camp_javascriptspecialchars($articleObj->getTitle()).'&quot;', camp_javascriptspecialchars($sLanguageObj->getName())); ?>');"><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/delete.png" BORDER="0"></A></TD>
						<TD style="padding-left: 6px;"><a href="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/do_del.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the article $1 ($2)?', '&quot;'.camp_javascriptspecialchars($articleObj->getTitle()).'&quot;', camp_javascriptspecialchars($sLanguageObj->getName())); ?>');"><B><?php  putGS("Delete"); ?></B></A></TD>
					</TR>
					</TABLE>
				</TD>
				<?php  
			} 

			if ($User->hasPermission('AddArticle')) { 
				?>
				<TD class="action_link_container">
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
					<TR>
						<TD><A HREF="<?php echo CampsiteInterface::ArticleUrl($articleObj, $languageObj->getLanguageId(), "duplicate.php"); ?>&Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/duplicate.png" BORDER="0"></A></TD>
						<TD><A HREF="<?php echo CampsiteInterface::ArticleUrl($articleObj, $languageObj->getLanguageId(), "duplicate.php"); ?>&Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><B><?php  putGS("Duplicate"); ?></B></A></TD>
					</TR>
					</TABLE>
				</TD>
				<?php  
			} 
			?>
		</TR>
		</TABLE>
	</TD>
	
</TR>
</TABLE>

<?php if ($Saved > 0) { ?>
<TABLE BORDER="0" cellpadding="0" cellspacing="0" align="center">
<tr>
	<td class="info_message">
		<?php 
		if ($Saved == 1) {
			putGS('The article has been updated.'); 
		}
		elseif ($Saved == 2) {
			putGS('The article cannot be updated or no changes have been made.');
		}
		?>
	</td>
</tr>
</table>
<?php } ?>


<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php">
<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($sLanguage); ?>">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" align="center" class="table_input">
<TR>
	<TD COLSPAN="2">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td align="left">
				<B><?php putGS("Edit article details"); ?></B>
			</td>
        	<TD ALIGN="RIGHT">
        		<?php 
        		$languageUrl = "edit.php?Pub=$Pub&Issue=$Issue&Section=$Section"
        		              ."&Article=$Article&Language=$Language&sLanguage=";
        		?>
        		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
        		<TR>
        			<TD><?php  putGS('Language'); ?>:</TD>
        			<TD>
        				<SELECT NAME="sLanguage" class="input_select" onchange="dest = '<?php p($languageUrl); ?>'+this.options[this.selectedIndex].value; location.href=dest;">
        				<?php 
        					$articleLanguages = $articleObj->getLanguages();
        					foreach ($articleLanguages as $articleLanguage) {
        					    pcomboVar($articleLanguage->getLanguageId(), $sLanguage, htmlspecialchars($articleLanguage->getName()));
        					}
        				?></SELECT>
        			</TD>
        		</TR>
        		</TABLE>
        	</TD>
			<?php 
			if ($zipLibAvailable && $xsltLibAvailable && $xmlLibAvailable 
					&& $introSupport && $bodySupport) {
				// Article Import Link
				?>
    			<td align="right">
				<b><a href="/<?php echo $ADMIN; ?>/article_import/index.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>">Import Article</a></b>
    			</td>
				<?php
			}
			?>
		</tr>
		</table>
		<HR NOSHADE SIZE="1" COLOR="BLACK"> 
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
		<INPUT TYPE="TEXT" NAME="cName" SIZE="64" MAXLENGTH="140" VALUE="<?php  print htmlspecialchars($articleObj->getTitle()); ?>" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Type"); ?>:</TD>
	<TD>
		<?php print htmlspecialchars($articleObj->getType()); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Uploaded"); ?>:</TD>
	<TD>
		<?php print htmlspecialchars($articleObj->getUploadDate()); ?> <?php  putGS('(yyyy-mm-dd)'); ?>
	</TD>
</TR>
<TR>
	<TD>&nbsp;</TD><TD>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cOnFrontPage" class="input_checkbox" <?php  if ($articleObj->onFrontPage()) { ?> CHECKED<?php  } ?>></TD>
		<TD>
		<?php  putGS('Show article on front page'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cOnSection" class="input_checkbox" <?php  if ($articleObj->onSectionPage()) { ?> CHECKED<?php  } ?>></TD>
		<TD>
		<?php  putGS('Show article on section page'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cPublic" class="input_checkbox" <?php  if ($articleObj->isPublic()) { ?> CHECKED<?php  } ?>></TD>
		<TD>
		<?php putGS('Allow users without subscriptions to view the article'); ?>
		</TD>
	</TR>
		</TABLE>
	</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Keywords"); ?>:</TD>
		<TD>
			<INPUT TYPE="TEXT" NAME="cKeywords" VALUE="<?php print htmlspecialchars($articleObj->getKeywords()); ?>" class="input_text" SIZE="64" MAXLENGTH="255">
		</TD>
	</TR>

	<?php 
	// Display the article type fields.
	foreach ($dbColumns as $dbColumn) {
		if (stristr($dbColumn->getType(), "char")) { 
			// Single line text fields
			?>
			<TR>
				<TD ALIGN="RIGHT" >
					<TABLE cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td align="left" style="padding-right: 5px;">
							<input type="image" src="/<?php p($ADMIN); ?>/img/icon/save.png" name="save" value="save">
						</td>
						<td align="right">				
							<?php echo htmlspecialchars($dbColumn->getPrintName()); ?>:
						</td>
					</tr>
					</table>
				</TD>
				<TD>
		        <INPUT NAME="<?php echo htmlspecialchars($dbColumn->getName()); ?>" 
					   TYPE="TEXT" 
					   VALUE="<?php print $articleType->getProperty($dbColumn->getName()) ?>" 
					   class="input_text"
					   SIZE="64" 
					   MAXLENGTH="100">
				</TD>
			</TR>
			<?php  
		} elseif (stristr($dbColumn->getType(), "date")) { 
			// Date fields
			if ($articleType->getProperty($dbColumn->getName()) == "0000-00-00") {
				$articleType->setProperty($dbColumn->getName(), "CURDATE()", true, true);
			}
			?>		
			<TR>
				<TD ALIGN="RIGHT" >
					<TABLE cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td align="left" style="padding-right: 5px;">
							<input type="image" src="/<?php p($ADMIN); ?>/img/icon/save.png" name="save" value="save">
						</td>
						<td align="right">				
							<?php echo htmlspecialchars($dbColumn->getPrintName()); ?>:
						</td>
					</tr>
					</table>	
				</TD>
				<TD>
				<INPUT NAME="<?php echo htmlspecialchars($dbColumn->getName()); ?>" 
					   TYPE="TEXT" 
					   VALUE="<?php echo htmlspecialchars($articleType->getProperty($dbColumn->getName())); ?>" 
					   class="input_text"
					   SIZE="11" 
					   MAXLENGTH="10"> 
				<?php putGS('YYYY-MM-DD'); ?>
				</TD>
			</TR>
			<?php
		} elseif (stristr($dbColumn->getType(), "blob")) {
			// Multiline text fields
			// Transform Campsite-specific tags into editor-friendly tags.
			$text = $articleType->getProperty($dbColumn->getName());
			
			// Subheads
			$text = preg_replace("/<!\*\*\s*Title\s*>/i", "<span class=\"campsite_subhead\">", $text);
			$text = preg_replace("/<!\*\*\s*EndTitle\s*>/i", "</span>", $text);
			
			// Internal Links with targets
			$text = preg_replace("/<!\*\*\s*Link\s*Internal\s*([\w=&]*)\s*target\s*([\w_]*)\s*>/i", '<a href="campsite_internal_link?$1" target="$2">', $text);
			// Internal Links without targets
			$text = preg_replace("/<!\*\*\s*Link\s*Internal\s*([\w=&]*)\s*>/i", '<a href="campsite_internal_link?$1">', $text);
			// End link
			$text = preg_replace("/<!\*\*\s*EndLink\s*>/i", "</a>", $text);
			
			// External Links
			// Match the case when there is a target with the link
			//$text = preg_replace("/<!\*\*\s*Link\s*external\s*(['\"][^'\"]*['\"])\s*(target)\s*(['\"][^'\"]*['\"])\s*>/i", '<a href=$1 $3=$4>', $text);
			// Match the case when there isnt a target
			//$text = preg_replace("/<!\*\*\s*Link\s*external\s*(['\"][^'\"]*['\"])\s*>/i", '<a href=$1>', $text);
			
			// Images
			preg_match_all("/<!\*\*\s*Image\s*([\d]*)\s*/i",$text, $imageMatches);
			if (isset($imageMatches[1][0])) {
				foreach ($imageMatches[1] as $templateId) {
					// Get the image URL
					$articleImage =& new ArticleImage($Article, null, $templateId);
					$image =& new Image($articleImage->getImageId());
					$imageUrl = $image->getImageUrl();
					$text = preg_replace("/<!\*\*\s*Image\s*".$templateId."\s*/i", '<img src="'.$imageUrl.'" ', $text);
				}
			}			
			?>
			<TR>
			<TD ALIGN="RIGHT" VALIGN="TOP" style="padding-top: 25px;">
				<TABLE cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="left" style="padding-right: 5px;">
						<input type="image" src="/<?php p($ADMIN); ?>/img/icon/save.png" name="save" value="save">
					</td>
					<td align="right">				
						<?php echo htmlspecialchars($dbColumn->getPrintName()); ?>:
					</td>
				</tr>
				</table>
			</TD>
			<TD>
				<HR NOSHADE SIZE="1" COLOR="BLACK">
				<table width=100% border=2>
				<tr bgcolor=LightBlue>
					<td><textarea name="<?php print $dbColumn->getName() ?>" 
								  id="<?php print $dbColumn->getName() ?>" 
								  rows="20" cols="80" ><?php print $text; ?></textarea>
					</td>
				</tr>
				</table>
			<BR><P>
			</TD>
			</TR>
			<?php  
		}
	} // foreach ($dbColumns as $dbColumn)  
	?>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>" class="button">
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" class="button" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE>
</FORM>
<?php  
} // if ($edit_ok)
CampsiteInterface::CopyrightNotice();
?>
