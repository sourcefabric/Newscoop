<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission("AddArticle")) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS("You do not have the right to add articles." )));
	exit;
}

$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);
$Section = Input::get('Section', 'int', 0);
$Language = Input::get('Language', 'int', 0);
$sLanguage = Input::get('sLanguage', 'int', 0);
$Article = Input::get('Article', 'int', 0);
$DestPublication = Input::get('destination_publication', 'int', 0, true);
$DestIssue = Input::get('destination_issue', 'int', 0, true);
$DestSection = Input::get('destination_section', 'int', 0, true);
$BackLink = Input::get('Back', 'string', "/$ADMIN/pub/issues/sections/articles/index.php", true);

if (!Input::isValid()) {
	header("Location: /$ADMIN/logout.php");
	exit;	
}

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Publication does not exist.')));
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Issue does not exist.')));
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Section does not exist.')));
	exit;	
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Article does not exist.')));
	exit;
}

$languageObj =& new Language($Language);
$sLanguageObj =& new Language($sLanguage);

$allPublications =& Publication::GetAllPublications();
$allIssues = array();
if ($DestPublication > 0) {
	$allIssues =& Issue::GetIssuesInPublication($DestPublication);
}
$allSections = array();
if ($DestIssue > 0) {
	$allSections =& Section::GetSectionsInIssue($DestPublication, $DestIssue, $Language);
}

ArticleTop($articleObj, $Language, "Duplicate article");
?>

<P>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" ALIGN="CENTER" class="table_input">
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT DISABLED TYPE="TEXT" NAME="cName" SIZE="64" MAXLENGTH="64" VALUE="<?php  p(htmlspecialchars($articleObj->getTitle())); ?>" class="input_text_disabled">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Type"); ?>:</TD>
	<TD>
	<B><?php p(htmlspecialchars($articleObj->getType())); ?></B>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Uploaded"); ?>:</TD>
	<TD>
	<B><?php p(htmlspecialchars($articleObj->getUploadDate())); ?> <?php  putGS('(yyyy-mm-dd)'); ?></B>
	</TD>
</TR>

<TR>
	<td colspan="2">&nbsp;</TD>
</TR>
<TR>
	<td colspan="2" style="padding-left: 20px; font-size: 12pt; font-weight: bold;"><?php  putGS("Select destination"); ?></TD>
</TR>
<TR>
	<TD VALIGN="middle" ALIGN="RIGHT" style="padding-left: 20px;"><?php  putGS('Publication'); ?>: </TD>
	<TD valign="middle" ALIGN="LEFT">
		<?php if (count($allPublications) > 0) { ?>
		<FORM NAME="FORM_PUB" METHOD="POST">
		<input type="hidden" name="Pub" value="<?php p($Pub); ?>">
		<input type="hidden" name="Issue" value="<?php p($Issue); ?>">
		<input type="hidden" name="Section" value="<?php p($Section); ?>">
		<input type="hidden" name="Language" value="<?php p($Language); ?>">
		<input type="hidden" name="Article" value="<?php p($Article); ?>">
		<input type="hidden" name="sLanguage" value="<?php p($sLanguage); ?>">
		<input type="hidden" name="Back" value="<?php p($BackLink); ?>">
		<SELECT NAME="destination_publication" class="input_select" ONCHANGE="if ((this.selectedIndex != 0) && (this.options[this.selectedIndex].value != <?php p($DestPublication); ?>)) {this.form.submit();}">
		<OPTION VALUE="0"><?php  putGS('---Select publication---'); ?></option>
		<?php 
		foreach ($allPublications as $tmpPublication) {
			?><option value="<?php p($tmpPublication->getPublicationId());?>" <?php if ($tmpPublication->getPublicationId() == $DestPublication) {	?>selected<?php	} ?>><?php p(htmlspecialchars($tmpPublication->getName())); ?></option>
			<?
		}
		?>
		</SELECT>
		<?php
		}
		else {
			?>
			<SELECT class="input_select" DISABLED><OPTION><?php  putGS('No publications'); ?></option></SELECT>
			<?php
		}
		?>
		</form>
	</td>
</tr>

<tr>
	<TD VALIGN="middle" ALIGN="RIGHT" style="padding-left: 20px;"><?php  putGS('Issue'); ?>: </TD>
	<TD valign="middle" ALIGN="LEFT">
		<?php if (($DestPublication > 0) && (count($allIssues) > 0)) { ?>
		<FORM NAME="FORM_ISS" METHOD="POST">
		<input type="hidden" name="Pub" value="<?php p($Pub); ?>">
		<input type="hidden" name="Issue" value="<?php p($Issue); ?>">
		<input type="hidden" name="Section" value="<?php p($Section); ?>">
		<input type="hidden" name="Language" value="<?php p($Language); ?>">
		<input type="hidden" name="Article" value="<?php p($Article); ?>">
		<input type="hidden" name="sLanguage" value="<?php p($sLanguage); ?>">
		<input type="hidden" name="Back" value="<?php p($BackLink); ?>">
		<input type="hidden" name="destination_publication" value="<?php p($DestPublication); ?>">
		<SELECT NAME="destination_issue" class="input_select" ONCHANGE="if ((this.selectedIndex != 0) && (this.options[this.selectedIndex].value != <?php p($DestIssue); ?>)) { this.form.submit(); }">
		<OPTION VALUE="0"><?php  putGS('---Select issue---'); ?></option>
		<?php 
		foreach ($allIssues as $tmpIssue) {
			?>
			<option value="<?php p($tmpIssue->getIssueId());?>"
			<?php
			if ($tmpIssue->getIssueId() == $DestIssue) {
				?>selected<?php
			}
			?>
			><?php p(htmlspecialchars($tmpIssue->getName())); ?></option>
			<?			
		}
		?>
		</SELECT>
		</FORM>
		<?php  
		} 
		else { 
			?><SELECT class="input_select" DISABLED><OPTION><?php  putGS('No issues'); ?></SELECT>
			<?php  
		} 
		?>
	</td>
</tr>

<tr>	
	<TD VALIGN="middle" ALIGN="RIGHT" style="padding-left: 20px;"><?php  putGS('Section'); ?>: </TD>
	<TD valign="middle" ALIGN="LEFT">
		<?php if (($DestIssue > 0) && (count($allSections) > 0)) { ?>
		<FORM NAME="FORM_SECT" METHOD="POST">
		<input type="hidden" name="Pub" value="<?php p($Pub); ?>">
		<input type="hidden" name="Issue" value="<?php p($Issue); ?>">
		<input type="hidden" name="Section" value="<?php p($Section); ?>">
		<input type="hidden" name="Language" value="<?php p($Language); ?>">
		<input type="hidden" name="Article" value="<?php p($Article); ?>">
		<input type="hidden" name="sLanguage" value="<?php p($sLanguage); ?>">
		<input type="hidden" name="Back" value="<?php p($BackLink); ?>">
		<input type="hidden" name="destination_publication" value="<?php p($DestPublication); ?>">
		<input type="hidden" name="destination_issue" value="<?php p($DestIssue); ?>">
		<SELECT NAME="destination_section" class="input_select" ONCHANGE="if ((this.selectedIndex != 0) && (this.options[this.selectedIndex].value != <?php p($DestSection); ?>)) { this.form.submit(); }">
		<OPTION VALUE="0"><?php  putGS('---Select section---'); ?>
		<?php 
		foreach ($allSections as $tmpSection) {
			?>
			<option value="<?php p($tmpSection->getSectionId());?>"
			<?php
			if ($tmpSection->getSectionId() == $DestSection) {
				?>selected<?php
			}
			?>
			><?php p(htmlspecialchars($tmpSection->getName())); ?></option>
			<?php
		}
		?>
		</SELECT>
		</form>
		<?php  
		} 
		else { 
			?><SELECT class="input_select" DISABLED><OPTION><?php  putGS('No sections'); ?></SELECT>
			<?php  
		}
		?>
		</TD>
</tr>

<tr>
	<td colspan="2"><?php 
		if ( ($Pub == $DestPublication) && ($Issue == $DestIssue) && ($Section == $DestSection)) {
			putGS("The destination section is the same as the source section."); echo "<BR>\n";
		}
	?></td>
</tr>

<tr>
	<td align="center" colspan="2">
		<FORM NAME="ART_DUP" METHOD="POST" action="do_duplicate.php">
		<input type="hidden" name="Pub" value="<?php p($Pub); ?>">
		<input type="hidden" name="Issue" value="<?php p($Issue); ?>">
		<input type="hidden" name="Section" value="<?php p($Section); ?>">
		<input type="hidden" name="Language" value="<?php p($Language); ?>">
		<input type="hidden" name="Article" value="<?php p($Article); ?>">
		<input type="hidden" name="sLanguage" value="<?php p($sLanguage); ?>">
		<input type="hidden" name="destination_publication" value="<?php p($DestPublication); ?>">
		<input type="hidden" name="destination_issue" value="<?php p($DestIssue); ?>">
		<input type="hidden" name="destination_section" value="<?php p($DestSection); ?>">
		<INPUT TYPE="button" Name="Duplicate" Value="<?php putGS("Duplicate article"); ?>" <?php if (($DestPublication <= 0) || ($DestIssue <=0) || ($DestSection <= 0)) { echo 'class="button_disabled"'; } else { echo 'class="button" onclick="this.form.submit();"'; }?> >
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='<?php p($BackLink); ?>'" class="button">
		</FORM>
	</td>
</tr>
</table>
<p>

<?php CampsiteInterface::CopyrightNotice(); ?>