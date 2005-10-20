<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/sections/section_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission("ManageSection")) {
	camp_html_display_error(getGS("You do not have the right to add sections."));
	exit;
}
if (!$User->hasPermission("AddArticle")) {
	camp_html_display_error(getGS("You do not have the right to add articles."));
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$DestPublicationId = Input::Get('destination_publication', 'int', 0, true);
$DestIssueInput = Input::Get('destination_issue', 'string', 0, true);
$DestIssueId = 0;
$DestIssueLanguage = 0;
if ($DestIssueInput != 0) {
    $tmpStr = split('_', $DestIssueInput);
    $DestIssueId = $tmpStr[0];
    $DestIssueLanguage = $tmpStr[1];
}
$BackLink = Input::Get('Back', 'string', "/$ADMIN/sections/index.php?Pub=$Pub&Issue=$Issue&Language=$Language", true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS("Invalid input: $1", Input::GetErrorString()), $BackLink);
	exit;	
}

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	camp_html_display_error(getGS('Publication does not exist.'));
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('Issue does not exist.'));
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	camp_html_display_error(getGS('Section does not exist.'));
	exit;	
}

$languageObj =& new Language($Language);

$allPublications =& Publication::GetPublications();
$allIssues = array();
if ($DestPublicationId > 0) {
    // Get the most recent 50 Issues...if they want something farther back, we are in trouble.
    $sqlOptions = array("LIMIT" => 50, "ORDER BY" => array("Number" => "DESC")); 
	$allIssues =& Issue::GetIssues($DestPublicationId, null, null, null, $sqlOptions);
}

$allSections = array();
$destIssueObj = null;
if ($DestIssueId > 0) {
    $destIssueObj =& new Issue($DestPublicationId, $DestIssueLanguage, $DestIssueId);
	$allSections =& Section::GetSections($DestPublicationId, $DestIssueId, $DestIssueLanguage);
}

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 'Section' => $sectionObj);
camp_html_content_top(getGS('Duplicate section'), $topArray, true, true);
?>
<script>
function CustomValidator_DuplicateSection(form) {
    // Verify radio button checked
    if (!form.section_chooser[0].checked && !form.section_chooser[1].checked) {
        alert("<?php putGS("Please select either '$1' or '$2'.", getGS('Existing Section'), getGS('New Section')); ?>");
        return false;
    }
    
    // Existing section checking
    if (form.section_chooser[0].checked) {
        if (form.destination_section_existing.selectedIndex == 0) {
            alert('<?php putGS("You must select a section."); ?>');
            return false;
        }
    }
    else {
        // New Section checking
        // Verify there is a number for the section
        newSectionNumber = form.destination_section_new_id.value.trim();
        if (form.section_chooser[1].checked && (newSectionNumber == "")) {
            alert('<?php putGS("You must select a section."); ?>');
            return false;
        }
        
        // Verify there is a name for the section
        if (form.section_chooser[1].checked && (form.destination_section_new_name.value.trim() == "")) {
            alert('<?php putGS("You must specify a name for the section."); ?>');
            return false;
        }
        
        // Check if user specified an existing section in the "New Section" dialog.
        existingSections = [ <?php p(implode(',', DbObjectArray::GetColumn($allSections, 'Number'))); ?> ];
        for (i = 0; i < existingSections.length; i++ ) {
            if (newSectionNumber == existingSections[i]) {
                alert('<?php putGS("The section number specified already exists, please specify a different value or use the dropdown to find an existing section."); ?>');
                return false;   
            }
        }
    }
    return true;
} // fn CustomValidator_DuplicateSection
</script>


<P>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" ALIGN="CENTER" class="table_input" width="600px" style="padding-top: 10px; padding-bottom: 10px; padding-left: 40px; padding-right: 40px;">
<tr>
<td>
<table BORDER="0" CELLSPACING="0" CELLPADDING="0" ALIGN="CENTER" >
<TR>
	<TD colspan="2" style="font-size: 14pt; padding-top: 10px;"><b><?php  putGS("Duplicate Section:"); ?> <?php  p(htmlspecialchars($sectionObj->getName())); ?></b></TD>
</TR>
<TR>
	<td colspan="3">&nbsp;</TD>
</TR>
<TR>
	<td colspan="3" style="padding-left: 20px; font-size: 12pt; font-weight: bold;"><?php  putGS("Select destination section:"); ?></TD>
</TR>
<TR>
	<TD VALIGN="middle" style="padding-left: 20px; padding-top: 6px;"><?php  putGS('Publication'); ?>: </TD>
	<TD valign="middle" ALIGN="LEFT" style="padding-top: 6px;">
		<?php if (count($allPublications) > 0) { ?>
		<FORM NAME="FORM_PUB" METHOD="POST">
		<input type="hidden" name="Pub" value="<?php p($Pub); ?>">
		<input type="hidden" name="Issue" value="<?php p($Issue); ?>">
		<input type="hidden" name="Section" value="<?php p($Section); ?>">
		<input type="hidden" name="Language" value="<?php p($Language); ?>">
		<input type="hidden" name="Back" value="<?php p($BackLink); ?>">
		<SELECT NAME="destination_publication" class="input_select" ONCHANGE="if ((this.selectedIndex != 0) && (this.options[this.selectedIndex].value != <?php p($DestPublicationId); ?>)) {this.form.submit();}">
		<OPTION VALUE="0"><?php  putGS('---Select publication---'); ?></option>
		<?php 
		foreach ($allPublications as $tmpPublication) {
			?><option value="<?php p($tmpPublication->getPublicationId());?>" <?php if ($tmpPublication->getPublicationId() == $DestPublicationId) {	?>selected<?php	} ?>><?php p(htmlspecialchars($tmpPublication->getName())); ?></option>
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

<tr >
	<TD VALIGN="middle" style="padding-left: 20px; padding-top: 6px;"><?php  putGS('Issue'); ?>: </TD>
	<TD valign="middle" ALIGN="LEFT" style="padding-top: 6px;">
		<?php if (($DestPublicationId > 0) && (count($allIssues) > 0)) { ?>
		<FORM NAME="FORM_ISS" METHOD="POST">
		<input type="hidden" name="Pub" value="<?php p($Pub); ?>">
		<input type="hidden" name="Issue" value="<?php p($Issue); ?>">
		<input type="hidden" name="Section" value="<?php p($Section); ?>">
		<input type="hidden" name="Language" value="<?php p($Language); ?>">
		<input type="hidden" name="Back" value="<?php p($BackLink); ?>">
		<input type="hidden" name="destination_publication" value="<?php p($DestPublicationId); ?>">
		<SELECT NAME="destination_issue" class="input_select" ONCHANGE="if ((this.selectedIndex != 0) && (this.options[this.selectedIndex].value != <?php p($DestIssueId); ?>)) { this.form.submit(); }">
		<OPTION VALUE="0"><?php  putGS('---Select issue---'); ?></option>
		<?php 
		foreach ($allIssues as $tmpIssue) {
			?>
			<option value="<?php p($tmpIssue->getIssueId().'_'.$tmpIssue->getLanguageId()); ?>"
			<?php
			if (($tmpIssue->getIssueId().'_'.$tmpIssue->getLanguageId()) == $DestIssueInput) {
				?>selected<?php
			}
			?>
			><?php p(htmlspecialchars($tmpIssue->getName().' ('.$tmpIssue->getLanguageName().')')); ?></option>
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

<?php 
if ( ($Pub == $DestPublicationId) && ($Issue == $DestIssueId)) { ?>
<tr>
	<td colspan="2" style="padding-top: 10px; padding-bottom: 7px;">
			<b><?php putGS("Warning"); echo ':'; putGS("The destination issue is the same as the source issue."); ?></b>
	</td>
</tr>
<?php } ?>
<FORM METHOD="POST" action="do_duplicate.php" onsubmit="return CustomValidator_DuplicateSection(this);">
<input type="hidden" name="Pub" value="<?php p($Pub); ?>">
<input type="hidden" name="Issue" value="<?php p($Issue); ?>">
<input type="hidden" name="Section" value="<?php p($Section); ?>">
<input type="hidden" name="Language" value="<?php p($Language); ?>">
<input type="hidden" name="destination_publication" value="<?php p($DestPublicationId); ?>">
<input type="hidden" name="destination_issue" value="<?php p($DestIssueId); ?>">
<input type="hidden" name="destination_issue_language" value="<?php p($DestIssueLanguage); ?>">
<tr>
	<td style="padding-left: 40px; padding-top: 10px;">
	   <table cellpadding="0" cellspacing="0">
	   <tr>
	       <td style="border-top: 1px solid black; border-left: 1px solid black; padding-top: 5px;  padding-bottom: 5px;" valign="top">
	           <input type="radio" name="section_chooser" value="existing_section" <?php if ($DestIssueId <= 0) { ?> disabled <?php }?>>
	       </td>
	       <td style="padding-top: 8px; padding-bottom: 3px;">
	           <?php putGS("Existing Section"); ?>:
	       </td>
	   </tr>
	   </table>
	</td>
	<td style="padding-top: 12px; padding-bottom: 0px;">
		<SELECT NAME="destination_section_existing" class="input_select" <?php if (($DestIssueId <= 0) || (count($allSections) <= 0)) { ?> disabled <?php } ?> onchange="this.form.section_chooser[0].checked = true;">
		<?php if (($DestIssueId <= 0) || (count($allSections) <= 0)) { ?>
		<OPTION VALUE="0"><?php  putGS('No sections'); ?></option>
		<?php } else { ?>
		<OPTION VALUE="0"><?php  putGS('---Select section---'); ?></option>
		<?php } ?>
		<?php 
		foreach ($allSections as $tmpSection) {
			?>
			<option value="<?php p($tmpSection->getSectionId());?>"><?php p(htmlspecialchars($tmpSection->getName())); ?></option>
			<?php
		}
		?>
		</SELECT>
	</td>
</tr>

<tr>
	<td style="padding-left: 40px;">
	   <table cellpadding="0" cellspacing="0">
	   <tr>
	       <td style="border-left: 1px solid black; padding-left: 40px;"><b><u><?php putGS("OR"); ?></u></b></td>
	   </tr>
	   </table>

	</td>
</tr>

<tr>
	<td style="padding-left: 40px;">
	   <table cellpadding="0" cellspacing="0">
	   <tr>
	       <td style="border-left: 1px solid black; padding-bottom: 5px;" valign="top">
	           <input type="radio" name="section_chooser" value="new_section" <?php if ($DestIssueId <= 0) { ?> disabled <?php }?>>
	       </td>
	       <td style="padding-top: 3px; padding-bottom: 5px; padding-right: 10px;">
	           <?php putGS("New Section"); ?>:
	       </td>
	   </tr>
	   </table>
	</td>
	<td>
	   <table cellpadding="0" cellspacing="0">
	   <tr>
	       <td style="width: 5em;"><?php putGS("Number"); ?>:</td>
	       <td><input type="text" class="input_text" name="destination_section_new_id" size="4" maxlength="4" value="<?php echo $Section; ?>" <?php if (($DestPublicationId <= 0) || ($DestIssueId <= 0)) { ?>disabled<?php } ?> onclick="this.form.section_chooser[1].checked = true;"></td>
	   </tr>
	   </table>
	</td>
</tr>

<tr>
	<td style="padding-left: 40px;">
	   <table cellpadding="0" cellspacing="0">
	   <tr>
	       <td style="border-bottom: 1px solid black; border-left: 1px solid black; padding-bottom: 5px;">
	           <!-- This radio button is here to make the border match the top border -->
	           <input type="radio" name="" value="" disabled style="visibility:hidden;">
	       </td>
	       <td style="padding-top: 3px; padding-bottom: 5px; padding-right: 10px;">
	           &nbsp;
	       </td>
	   </tr>
	   </table>
	</td>
	<td>
        <table cellpadding="0" cellspacing="0">
        <tr>
           <td style="width: 5em;"><?php putGS("Name"); ?>:</td>
           <td><input type="text" class="input_text" name="destination_section_new_name" size="20" value="<?php echo $sectionObj->getName(); ?>" <?php if (($DestPublicationId <= 0) || ($DestIssueId <= 0)) { ?>disabled<?php } ?> onclick="this.form.section_chooser[1].checked = true;"></td>
        </tr>
        </table>
	</td>
</tr>

<tr>
	<td align="center" colspan="2" style="padding-top: 15px;">
	   <table>
	   <tr>
	       <td>
		      <INPUT TYPE="submit" Name="Duplicate" Value="<?php putGS("Duplicate section"); ?>" <?php if (($DestPublicationId <= 0) || ($DestIssueId <=0)) { echo 'class="button_disabled"'; } else { echo 'class="button"'; }?> >
		   </td>
		   
		  <!-- <td style="padding-left: 5px;">
		      <INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='<?php p($BackLink); ?>'" class="button">
		   </td>-->
	   </tr>
	   </table>
	</td>
</tr>
</FORM>
</table>
</td>
</tr>
</table>
<p>

<?php camp_html_copyright_notice(); ?>
