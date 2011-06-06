<?php

require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/sections/section_common.php");

if (!$g_user->hasPermission("ManageSection")) {
	camp_html_display_error(getGS("You do not have the right to add sections."));
	exit;
}
if (!$g_user->hasPermission("AddArticle")) {
	camp_html_display_error(getGS("You do not have the right to add articles."));
	exit;
}

$f_src_publication_id = Input::Get('Pub', 'int', 0);
$f_src_issue_number = Input::Get('Issue', 'int', 0);
$f_src_section_number = Input::Get('Section', 'int', 0);
$f_language_id = Input::Get('Language', 'int', 0);
$f_dest_publication_id = Input::Get('f_dest_publication_id', 'int', 0, true);
$f_dest_issue_number = Input::Get('f_dest_issue_number', 'string', 0, true);
$BackLink = Input::Get('Back', 'string', "/$ADMIN/sections/index.php?Pub=$f_src_publication_id&Issue=$f_src_issue_number&Language=$f_language_id", true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS("Invalid input: $1", Input::GetErrorString()), $BackLink);
	exit;
}

$publicationObj = new Publication($f_src_publication_id);
if (!$publicationObj->exists()) {
	camp_html_display_error(getGS('Publication does not exist.'));
	exit;
}

$issueObj = new Issue($f_src_publication_id, $f_language_id, $f_src_issue_number);
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('Issue does not exist.'));
	exit;
}

$sectionObj = new Section($f_src_publication_id, $f_src_issue_number, $f_language_id, $f_src_section_number);
if (!$sectionObj->exists()) {
	camp_html_display_error(getGS('Section does not exist.'));
	exit;
}

$allPublications = Publication::GetPublications();
if (count($allPublications) == 1) {
	$f_dest_publication_id = $f_src_publication_id;
	$destPublicationObj = camp_array_peek($allPublications);
}
$allIssues = array();
if ($f_dest_publication_id > 0) {
    // Get the most recent 50 Issues...if they want something farther back, we are in trouble.
    $sqlOptions = array("LIMIT" => 50, "ORDER BY" => array("Number" => "DESC"));
	$allIssues = Issue::GetIssues($f_dest_publication_id, $f_language_id, null, null, null, false, $sqlOptions, true);
	if (count($allIssues) == 1) {
		$f_dest_issue_number = $f_src_issue_number;
	}
}

$allSections = array();
$destIssueObj = null;
if ($f_dest_issue_number > 0) {
    $destIssueObj = new Issue($f_dest_publication_id, $sectionObj->getLanguageId(), $f_dest_issue_number);
	$allSections = Section::GetSections($f_dest_publication_id, $f_dest_issue_number, $sectionObj->getLanguageId(), null, null, null, true);
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

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/sections/?Pub=<?php p($f_src_publication_id); ?>&Issue=<?php p($f_src_issue_number); ?>&Language=<?php p($f_language_id); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/sections/?Pub=<?php p($f_src_publication_id); ?>&Issue=<?php p($f_src_issue_number); ?>&Language=<?php p($f_language_id); ?>"><B><?php  putGS("Section List"); ?></B></A></TD>

	<TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/sections/edit.php?Pub=<?php p($f_src_publication_id); ?>&f_issue_number=<?php  p($f_src_issue_number); ?>&f_section_number=<?php p($f_src_section_number); ?>&f_language_id=<?php  p($f_language_id); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/sections/edit.php?Pub=<?php p($f_src_publication_id); ?>&Issue=<?php p($f_src_issue_number); ?>&Section=<?php p($f_src_section_number); ?>&Language=<?php  p($f_language_id); ?>"><B><?php  putGS("Section"); ?>: <?php p(htmlspecialchars($sectionObj->getName())); ?></B></A></TD>
</TR>
</TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="table_input" style="padding-top: 10px; padding-bottom: 10px; padding-left: 20px; padding-right: 20px;">
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
		<?php
		if (count($allPublications) > 0) {
			if (count($allPublications) == 1) {
				echo htmlspecialchars($destPublicationObj->getName());
			} else { ?>
		<FORM NAME="FORM_PUB" METHOD="POST">
		<input type="hidden" name="Pub" value="<?php p($f_src_publication_id); ?>">
		<input type="hidden" name="Issue" value="<?php p($f_src_issue_number); ?>">
		<input type="hidden" name="Section" value="<?php p($f_src_section_number); ?>">
		<input type="hidden" name="Language" value="<?php p($f_language_id); ?>">
		<input type="hidden" name="Back" value="<?php p($BackLink); ?>">
		<SELECT NAME="f_dest_publication_id" class="input_select" ONCHANGE="if (this.options[this.selectedIndex].value != '<?php p($f_dest_publication_id); ?>') { this.form.submit();}">
		<OPTION VALUE="0"><?php  putGS('---Select publication---'); ?></option>
		<?php
		foreach ($allPublications as $tmpPublication) {
			camp_html_select_option($tmpPublication->getPublicationId(), $f_dest_publication_id, $tmpPublication->getName());
		}
		?>
		</SELECT>
		</form>
		<?php
		}
		} else {
		?>
			<SELECT class="input_select" DISABLED><OPTION><?php  putGS('No publications'); ?></option></SELECT>
		<?php
		}
		?>
	</td>
</tr>

<tr >
	<TD VALIGN="middle" style="padding-left: 20px; padding-top: 6px;"><?php  putGS('Issue'); ?>: </TD>
	<TD valign="middle" ALIGN="LEFT" style="padding-top: 6px;">
		<?php
		if (($f_dest_publication_id > 0) && (count($allIssues) > 0)) {
			if (count($allIssues) == 1) {
				echo htmlspecialchars($destIssueObj->getName());
			} else { ?>
		<FORM NAME="FORM_ISS" METHOD="POST">
		<input type="hidden" name="Pub" value="<?php p($f_src_publication_id); ?>">
		<input type="hidden" name="Issue" value="<?php p($f_src_issue_number); ?>">
		<input type="hidden" name="Section" value="<?php p($f_src_section_number); ?>">
		<input type="hidden" name="Language" value="<?php p($f_language_id); ?>">
		<input type="hidden" name="Back" value="<?php p($BackLink); ?>">
		<input type="hidden" name="f_dest_publication_id" value="<?php p($f_dest_publication_id); ?>">
		<SELECT NAME="f_dest_issue_number" class="input_select" ONCHANGE="if ((this.selectedIndex != 0) && (this.options[this.selectedIndex].value != <?php p($f_dest_issue_number); ?>)) { this.form.submit(); }">
		<OPTION VALUE="0"><?php  putGS('---Select issue---'); ?></option>
		<?php
		foreach ($allIssues as $tmpIssue) {
			camp_html_select_option($tmpIssue->getIssueNumber(), $f_dest_issue_number, $tmpIssue->getName());
		}
		?>
		</SELECT>
		</FORM>
		<?php
		}
		}
		else {
			?><SELECT class="input_select" DISABLED><OPTION><?php  putGS('No issues'); ?></SELECT>
			<?php
		}
		?>
	</td>
</tr>

<?php
if ( ($f_src_publication_id == $f_dest_publication_id) && ($f_src_issue_number == $f_dest_issue_number)) { ?>
<tr>
	<td colspan="2" style="padding-top: 10px; padding-bottom: 7px;">
			<b><?php putGS("Warning"); echo ':'; putGS("The destination issue is the same as the source issue."); ?></b>
	</td>
</tr>
<?php } ?>
<FORM METHOD="GET" action="/<?php echo $ADMIN; ?>/sections/do_duplicate.php" onsubmit="return CustomValidator_DuplicateSection(this);">
<?php echo SecurityToken::FormParameter(); ?>

<input type="hidden" name="f_src_publication_id" value="<?php p($f_src_publication_id); ?>">
<input type="hidden" name="f_src_issue_number" value="<?php p($f_src_issue_number); ?>">
<input type="hidden" name="f_src_section_number" value="<?php p($f_src_section_number); ?>">
<input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
<input type="hidden" name="f_dest_publication_id" value="<?php p($f_dest_publication_id); ?>">
<input type="hidden" name="f_dest_issue_number" value="<?php p($f_dest_issue_number); ?>">
<tr>
	<td style="padding-left: 40px; padding-top: 10px;">
	   <table cellpadding="0" cellspacing="0">
	   <tr>
	       <td style="border-top: 1px solid black; border-left: 1px solid black; padding-top: 5px;  padding-bottom: 5px;" valign="top">
	           <input type="radio" name="f_section_chooser" value="existing_section" <?php if ($f_dest_issue_number <= 0) { ?> disabled <?php }?>>
	       </td>
	       <td style="padding-top: 8px; padding-bottom: 3px;">
	           <?php putGS("Existing Section"); ?>:
	       </td>
	   </tr>
	   </table>
	</td>
	<td style="padding-top: 12px; padding-bottom: 0px;">
		<SELECT NAME="f_dest_section_existing" class="input_select" <?php if (($f_dest_issue_number <= 0) || (count($allSections) <= 0)) { ?> disabled <?php } ?> onchange="this.form.f_section_chooser[0].checked = true;">
		<?php if (($f_dest_issue_number <= 0) || (count($allSections) <= 0)) { ?>
		<OPTION VALUE="0"><?php  putGS('No sections'); ?></option>
		<?php } else { ?>
		<OPTION VALUE="0"><?php  putGS('---Select section---'); ?></option>
		<?php } ?>
		<?php
		foreach ($allSections as $tmpSection) {
			camp_html_select_option($tmpSection->getSectionNumber(), null, $tmpSection->getName());
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
	           <input type="radio" name="f_section_chooser" value="new_section" <?php if ($f_dest_issue_number <= 0) { ?> disabled <?php }?>>
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
	       <td><input type="text" class="input_text" name="f_dest_section_new_number" size="4" value="<?php echo $f_src_section_number; ?>" <?php if (($f_dest_publication_id <= 0) || ($f_dest_issue_number <= 0)) { ?>disabled<?php } ?> onclick="this.form.f_section_chooser[1].checked = true;"></td>
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
           <td><input type="text" class="input_text" name="f_dest_section_new_name" size="20" value="<?php echo $sectionObj->getName(); ?>" <?php if (($f_dest_publication_id <= 0) || ($f_dest_issue_number <= 0)) { ?>disabled<?php } ?> onclick="this.form.section_chooser[1].checked = true;"></td>
        </tr>
        </table>
	</td>
</tr>

<tr>
	<td align="center" colspan="2" style="padding-top: 15px;">
	   <table>
	   <tr>
	       <td>
		      <INPUT TYPE="submit" Name="Duplicate" Value="<?php putGS("Duplicate section"); ?>" <?php if (($f_dest_publication_id <= 0) || ($f_dest_issue_number <=0)) { echo 'class="button_disabled"'; } else { echo 'class="button"'; }?> >
		   </td>

	   </tr>
	   </table>
	</td>
</tr>
</table>
</FORM>
</td>
</tr>
</table>
<p>

<?php camp_html_copyright_notice(); ?>
