<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/section_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission("ManageSection")) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS("You do not have the right to add sections." )));
	exit;
}
if (!$User->hasPermission("AddArticle")) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS("You do not have the right to add articles." )));
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$DestPublication = Input::Get('destination_publication', 'int', 0, true);
$DestIssue = Input::Get('destination_issue', 'int', 0, true);
$BackLink = Input::Get('Back', 'string', "/$ADMIN/pub/issues/sections/index.php?Pub=$Pub&Issue=$Issue&Language=$Language", true);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(array("Invalid input: $1", Input::GetErrorString()), $BackLink);
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

$languageObj =& new Language($Language);

$allPublications =& Publication::GetAllPublications();
$allIssues = array();
if ($DestPublication > 0) {
	$allIssues =& Issue::GetIssuesInPublication($DestPublication);
}

SectionTop($sectionObj, $Language, "Duplicate section");
?>

<P>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" ALIGN="CENTER" class="table_input">
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT DISABLED TYPE="TEXT" NAME="cName" SIZE="64" MAXLENGTH="64" VALUE="<?php  p(htmlspecialchars($sectionObj->getName())); ?>" class="input_text_disabled">
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
		<input type="hidden" name="Back" value="<?php p($BackLink); ?>">
		<SELECT NAME="destination_publication" class="input_select" ONCHANGE="if ((this.selectedIndex != 0) && (this.options[this.selectedIndex].value != <?php p($DestPublication); ?>)) {this.form.submit();}">
		<OPTION VALUE="0"><?php  putGS('---Select publication---'); ?></option>
		<?php 
		foreach ($allPublications as $tmpPublication) {
			?><option value="<?php p($tmpPublication->getPublicationId());?>" <?php if ($tmpPublication->getPublicationId() == $DestPublication) {	?>selected<?php	} ?>><?php p(htmlspecialchars($tmpPublication->getName())); ?></option>
		<?php
		}
		?>
		</SELECT>
		<?php
		} else {
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
			<?php
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

<FORM NAME="SECT_DUP" METHOD="POST" action="do_duplicate.php">
<?php if ($DestPublication > 0 && $DestIssue > 0) { ?>
<tr>
	<td><?php putGS("Destination section number"); ?>:</td>
	<td><input type="text" class="input_text" name="destination_section" value="<?php echo $Section; ?>"></td>
</tr>
<?php } ?>

<tr>
	<td colspan="2"><?php 
		if ( ($Pub == $DestPublication) && ($Issue == $DestIssue)) {
			putGS("The destination issue is the same as the source issue."); echo "<BR>\n";
		}
	?></td>
</tr>

<tr>
	<td align="center" colspan="2">
		<input type="hidden" name="Pub" value="<?php p($Pub); ?>">
		<input type="hidden" name="Issue" value="<?php p($Issue); ?>">
		<input type="hidden" name="Section" value="<?php p($Section); ?>">
		<input type="hidden" name="Language" value="<?php p($Language); ?>">
		<input type="hidden" name="destination_publication" value="<?php p($DestPublication); ?>">
		<input type="hidden" name="destination_issue" value="<?php p($DestIssue); ?>">
		<INPUT TYPE="button" Name="Duplicate" Value="<?php putGS("Duplicate section"); ?>" <?php if (($DestPublication <= 0) || ($DestIssue <=0)) { echo 'class="button_disabled"'; } else { echo 'class="button" onclick="this.form.submit();"'; }?> >
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='<?php p($BackLink); ?>'" class="button">
	</td>
</tr>
</FORM>
</table>
<p>

<?php CampsiteInterface::CopyrightNotice(); ?>
