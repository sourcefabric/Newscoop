<?php  
require_once($_SERVER['DOCUMENT_ROOT']. "/priv/pub/issues/sections/articles/article_common.php");

list($access, $User, $XPerm) = check_basic_access($_REQUEST);
$Pub = isset($_REQUEST["Pub"])?$_REQUEST["Pub"]:0;
$Issue = isset($_REQUEST["Issue"])?$_REQUEST["Issue"]:0;
$Section = isset($_REQUEST["Section"])?$_REQUEST["Section"]:0;
$Language = isset($_REQUEST["Language"])?$_REQUEST["Language"]:0;
$sLanguage = isset($_REQUEST["sLanguage"])?$_REQUEST["sLanguage"]:0;
$Article = isset($_REQUEST["Article"])?$_REQUEST["Article"]:0;
$LockOk = isset($_REQUEST["LockOk"])?$_REQUEST["LockOk"]:0;

$errorStr = "";

// Fetch article
$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	$errorStr = 'No such article.';
}
$articleType =& $articleObj->getArticleTypeObject();
$lockUserObj =& new User($articleObj->getLockedByUser());
$issueLanguageObj =& new Language($Language);
$issueObj =& new Issue($Pub, $Language, $Issue);
$articleTemplate =& new Template($issueObj->getArticleTemplateId());

// If the user has the ability to change the article OR
// the user created the article and it hasnt been published.
$hasAccess = false;
if ($XPerm['ChangeArticle'] || (($articleObj->getUserId() == $User['Id']) && ($articleObj->getPublished() == 'N'))) {
	$hasAccess = true;
	$edit_ok= 0;
	// If the article is not locked by a user or its been locked by the current user.
	if (($articleObj->getLockedByUser() == 0) 
		|| ($articleObj->getLockedByUser() == $User['Id'])) {
		// Lock the article
		$articleObj->lock($User['Id']);
	    $edit_ok= 1;
	} 
}

if ($XPerm['AddArticle']) { 
	// Added by sebastian.
	if (function_exists ("incModFile")) {
		incModFile ();
	}
}

// Check if everything needed for Article Import is available.
$zipLibAvailable = function_exists("zip_open");
$xsltLibAvailable = function_exists("xslt_create");
@include("XML/Parser.php");
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
ArticleTop($articleObj, $Language, "Edit article details", $access);
HtmlArea_Campsite($dbColumns);

if ($errorStr != "") {
	CampsiteInterface::DisplayError($errorStr);
	return;
}

if (!$hasAccess) {
	?>
	<P>
	<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" BGCOLOR="#C0D0FF" ALIGN="CENTER">
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
			<A HREF="/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>"><IMG SRC="/priv/img/button/ok.gif" BORDER="0" ALT="OK"></A>
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
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("Article is locked"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  putGS('This article has been locked by $1 ($2) at','<B>'.htmlspecialchars($lockUserObj->getName()),htmlspecialchars($lockUserObj->getUName()).'</B>' ); ?>
		<B><?php print htmlspecialchars($articleObj->getLockTime()); ?></B></LI>
		<LI><?php putGS('Now is $1','<B>'.date("Y-m-d G:i:s").'</B>'); ?></LI>
		<LI><?php putGS('Are you sure you want to unlock it?'); ?></LI>
		</BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" NAME="Yes" VALUE="<?php  putGS('Yes'); ?>" ONCLICK="location.href='<?php p($REQUEST_URI); ?>&LockOk=1'">
		<INPUT TYPE="button" NAME="No" VALUE="<?php  putGS('No'); ?>" ONCLICK="location.href='/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php p($Language); ?>&Section=<?php  p($Section); ?>'">
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
			if ($XPerm['Publish']) { 
				?>
				<TD>
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
					<TR>
						<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "status.php", $REQUEST_URI); ?><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD>
						<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "status.php", $REQUEST_URI); ?><B><?php  putGS("Unpublish"); ?></B></A></TD>
					</TR>
					</TABLE>
				</TD>
				<?php  
			} 
		} 
		elseif ($articleObj->getPublished() == "S") { 
			if ($XPerm['Publish']) { 
				?>
				<TD>
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
					<TR>
						<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "status.php", $REQUEST_URI); ?><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD>
						<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "status.php", $REQUEST_URI); ?><B><?php  putGS("Publish"); ?></B></A></TD>
					</TR>
					</TABLE>
				</TD>
				<?php
			} 
		} 
		else { 
			?>
			<TD>
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
				<TR>
					<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "status.php", $REQUEST_URI); ?>
					<IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD>
					<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "status.php", $REQUEST_URI); ?><B><?php  putGS("Submit"); ?></B></A></TD>
				</TR>
				</TABLE>
			</TD>
			<?php  
		} 
		?>
			<TD>
				<!-- Images Link -->
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
				<TR>
					<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "images/"); ?><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD>
					<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "images/"); ?><B><?php  putGS("Images"); ?></B></A></TD>
				</TR>
				</TABLE>
			</TD>
			
			<TD>
				<!-- Topics Link -->
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
				<TR>
					<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "topics/"); ?><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD>
					<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "topics/"); ?><B><?php  putGS("Topics"); ?></B></A></TD>
				</TR>
				</TABLE>
			</TD>
			
			<TD>
				<!-- Unlock Link -->
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
				<TR>
					<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "do_unlock.php"); ?><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD>
					<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "do_unlock.php"); ?><B><?php  putGS("Unlock"); ?></B></A></TD>
				</TR>
				</TABLE>
			</TD>
		</TR>		
		
		<TR>
			<TD>
				<!-- Preview Link -->
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
				<TR>	
					<TD><A HREF="" ONCLICK="window.open('/priv/pub/issues/sections/articles/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=no, width=680, height=560'); return false"><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD>
					<TD><A HREF="" ONCLICK="window.open('/priv/pub/issues/sections/articles/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>', 'fpreview', 'resizable=yes, menubar=yes, toolbar=yes, width=680, height=560'); return false"><B><?php  putGS("Preview"); ?></B></A></TD>					
				</TR>
				</TABLE>
			</TD>

			<?php  
			if ($XPerm['AddArticle']) { 
				?>
				<TD>
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
					<TR>
						<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "translate.php", $REQUEST_URI); ?><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD>
						<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "translate.php", $REQUEST_URI); ?><B><?php  putGS("Translate"); ?></B></A></TD>
					</TR>
					</TABLE>
				</TD>
				<?php  
			} 

			if ($XPerm['DeleteArticle']) { 
				?>
				<TD>
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
					<TR>
						<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "del.php", $REQUEST_URI); ?><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD>
						<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "del.php", $REQUEST_URI); ?><B><?php  putGS("Delete"); ?></B></A></TD>
					</TR>
					</TABLE>
				</TD>
				<?php  
			} 

			if ($XPerm['AddArticle']) { 
				?>
				<TD>
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
					<TR>
						<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "fduplicate.php"); ?><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD>
						<TD><?php CampsiteInterface::ArticleLink($articleObj, $issueLanguageObj->getLanguageId(), "fduplicate.php"); ?><B><?php  putGS("Duplicate"); ?></B></A></TD>
					</TR>
					</TABLE>
				</TD>
				<?php  
			} 
			?>
		</TR>
		</TABLE>
	</TD>
	
	<TD ALIGN="RIGHT">
		<FORM METHOD="GET" ACTION="edit.php" NAME="">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" BGCOLOR="#C0D0FF">
		<TR>
			<TD><?php  putGS('Language'); ?>:</TD>
			<TD>
				<SELECT NAME="sLanguage">
				<?php 
					$articleLanguages = $articleObj->getLanguages();
					foreach ($articleLanguages as $articleLanguage) {
					    pcomboVar($articleLanguage->getLanguageId(), $sLanguage, htmlspecialchars($articleLanguage->getName()));
					}
				?></SELECT>
			</TD>
			<TD>
				<INPUT TYPE="submit" NAME="Search" VALUE="<?php  putGS('Search'); ?>">
			</TD>
		</TR>
		</TABLE>
		</FORM>
	</TD>
</TR>
</TABLE>

<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" BGCOLOR="#C0D0FF" align="center">
<TR>
	<TD COLSPAN="2">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td align="left">
				<B><?php putGS("Edit article details"); ?></B>
			</td>
			<td align="right">
				<?php 
				if ($zipLibAvailable && $xsltLibAvailable && $xmlLibAvailable 
						&& $introSupport && $bodySupport) {
					// Article Import Link
					?>
					<b><a href="/priv/article_import/index.php?Pub=<?p($Pub);?>&Issue=<?p($Issue);?>&Section=<?p($Section);?>&Article=<?p($Article)?>&Language=<?p($Language);?>&sLanguage=<?p($sLanguage);?>">Import Article</a></b>
					<?php
				}
				?>
			</td>
		</tr>
		</table>
		<HR NOSHADE SIZE="1" COLOR="BLACK"> 
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
		<INPUT TYPE="TEXT" NAME="cName" SIZE="64" MAXLENGTH="140" VALUE="<?php  print htmlspecialchars($articleObj->getTitle()); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Type"); ?>:</TD>
	<TD>
		<B><?php print htmlspecialchars($articleObj->getType()); ?></B>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Uploaded"); ?>:</TD>
	<TD>
		<B><?php print htmlspecialchars($articleObj->getUploadDate()); ?> <?php  putGS('(yyyy-mm-dd)'); ?></B>
	</TD>
</TR>
<TR>
	<TD>&nbsp;</TD><TD>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cOnFrontPage"<?php  if ($articleObj->onFrontPage()) { ?> CHECKED<?php  } ?>></TD>
		<TD>
		<?php  putGS('Show article on front page'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cOnSection"<?php  if ($articleObj->onSection()) { ?> CHECKED<?php  } ?>></TD>
		<TD>
		<?php  putGS('Show article on section page'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cPublic"<?php  if ($articleObj->isPublic()) { ?> CHECKED<?php  } ?>></TD>
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
			<INPUT TYPE="TEXT" NAME="cKeywords" VALUE="<?php print htmlspecialchars($articleObj->getKeywords()); ?>" SIZE="64" MAXLENGTH="255">
		</TD>
	</TR>

	<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
	<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
	<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
	<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
	<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
	<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($sLanguage); ?>">

	<?php 
	// Display the article type fields.
	foreach ($dbColumns as $dbColumn) {
		if (stristr($dbColumn->getType(), "char")) { 
			// Single line text fields
			?>
			<TR>
				<TD ALIGN="RIGHT" ><?php echo htmlspecialchars($dbColumn->getPrintName()); ?>:</TD>
				<TD>
		        <INPUT NAME="<?php echo htmlspecialchars($dbColumn->getName()); ?>" 
					   TYPE="TEXT" 
					   VALUE="<?php print $articleType->getColumnValue($dbColumn->getName()) ?>" 
					   SIZE="64" 
					   MAXLENGTH="100">
				</TD>
			</TR>
			<?php  
		} elseif (stristr($dbColumn->getType(), "date")) { 
			// Date fields
			if ($articleType->getColumnValue($dbColumn->getName()) == "0000-00-00") {
				$articleType->setColumnValue($dbColumn->getName(), "CURDATE()", true);
			}
			?>		
			<TR>
				<TD ALIGN="RIGHT" ><?php echo htmlspecialchars($dbColumn->getPrintName()); ?>:</TD>
				<TD>
				<INPUT NAME="<?php echo htmlspecialchars($dbColumn->getName()); ?>" 
					   TYPE="TEXT" 
					   VALUE="<?php echo htmlspecialchars($articleType->getColumnValue($dbColumn->getName())); ?>" 
					   SIZE="10" 
					   MAXLENGTH="10"> 
				<?php putGS('YYYY-MM-DD'); ?>
				</TD>
			</TR>
			<?php
		} elseif (stristr($dbColumn->getType(), "blob")) {
			// Multiline text fields
			?>
			<TR>
			<TD ALIGN="RIGHT" VALIGN="TOP"><BR><?php echo htmlspecialchars($dbColumn->getPrintName()); ?>:<BR> 
			</TD>
			<TD>
				<HR NOSHADE SIZE="1" COLOR="BLACK">
				<table width=100% border=2>
				<tr bgcolor=LightBlue>
					<td><textarea name="<?php print $dbColumn->getName() ?>" 
								  id="<?php print $dbColumn->getName() ?>" 
								  rows="20" cols="80" ><?php print $articleType->getColumnValue($dbColumn->getName()); ?></textarea>
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
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE>
</FORM>
<?php  
} // if ($edit_ok)
CampsiteInterface::CopyrightNotice();
?>