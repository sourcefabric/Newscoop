<?php
// Once entered here, in the TemplateManagement, because the parameter
// 'What' is 0 by default, even if the value set before is still set
// its meaning is lost, so we can use the variable to switch between
// file and folders; let's say 0 is for folders and 1 for files
// we only need it when deleting items

$basedir = $Campsite['TEMPLATE_DIRECTORY'].urldecode($listbasedir);
$handle = opendir($basedir);
while (($file = readdir($handle))!=false) {
    $full = "$basedir/$file";
    $filetype = filetype($full);
    $isDir = false;
    $isFile = false;
    // Avoid the links
    if ($filetype == "dir") {
        $isDir = true;
    } else if ($filetype != "link") {
        $isFile = true;
    }
    // If it's a file
    if ($isFile) {
        // Fill the array with filenames
        $files[] = $file;
    }
    // if it's a directory but not  '..' or '.'
    else if ($isDir && ($file != ".") && ($file != "..")){
        // filling the array with directory names
        $dirs[] = $file;
    }
}

$numItemsThisPage = 0;
if(isset($files) && is_array($files)) {
    $numItemsThisPage = count($files);
}

?>

<script>
/**
 * This array is used to remember mark status of rows in browse mode
 */
var marked_row = new Array;
var default_class = new Array;

function checkAll()
{
        for (i = 0; i < <?php p($numItemsThisPage); ?>; i++) {
                document.getElementById("row_"+i).className = 'list_row_click';
                document.getElementById("checkbox_"+i).checked = true;
        marked_row[i] = true;
        }
} // fn checkAll


function uncheckAll()
{
        for (i = 0; i < <?php p($numItemsThisPage); ?>; i++) {
                document.getElementById("row_"+i).className = default_class[i];
                document.getElementById("checkbox_"+i).checked = false;
        marked_row[i] = false;
        }
} // fn uncheckAll

/**
 * Sets/unsets the pointer and marker in browse mode
 *
 * @param   object    the table row
 * @param   integer  the row number
 * @param   string    the action calling this script (over, out or click)
 * @param   string    the default class
 *
 * @return  boolean  whether pointer is set or not
 */
function setPointer(theRow, theRowNum, theAction)
{
        newClass = null;
    // 4. Defines the new class
    // 4.1 Current class is the default one
    if (theRow.className == default_class[theRowNum]) {
        if (theAction == 'over') {
            newClass = 'list_row_hover';
        }
    }
    // 4.1.2 Current color is the hover one
    else if (theRow.className == 'list_row_hover'
             && (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {
        if (theAction == 'out') {
            newClass = default_class[theRowNum];
        }
    }

    if (newClass != null) {
        theRow.className = newClass;
    }
    return true;
} // end of the 'setPointer()' function

/**
 * Change the color of the row when the checkbox is selected.
 *
 * @param object  The checkbox object.
 * @param int     The row number.
 */
function checkboxClick(theCheckbox, theRowNum)
{
        if (theCheckbox.checked) {
        newClass = 'list_row_click';
        marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])
                              ? true
                              : null;
        } else {
        newClass = 'list_row_hover';
        marked_row[theRowNum] = false;
        }
        row = document.getElementById("row_"+theRowNum);
        row.className = newClass;
} // fn checkboxClick
</script>
<FORM name="template_list" action="do_template_list_action.php" method="POST">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" class="table_input" style="background-color: #D5C3DF; border-color: #A35ACF;">
<TR>
        <TD>
                <TABLE cellpadding="3" cellspacing="0">
                <TR>
                        <TD style="padding-left: 20px;">
				<script>
				function action_selected(dropdownElement)
				{
					// Verify that at least one checkbox has been selected.
					checkboxes = document.forms.template_list["f_template_code[]"];
					if (checkboxes) {
						isValid = false;
						numCheckboxesChecked = 0;
						// Special case for single checkbox
						// (when there is only one article in the section).
						if (!checkboxes.length) {
							isValid = checkboxes.checked;
							numCheckboxesChecked = isValid ? 1 : 0;
						} else {
							// Multiple checkboxes
							for (var index = 0; index < checkboxes.length; index++) {
								if (checkboxes[index].checked) {
									isValid = true;
									numCheckboxesChecked++;
								}
							}
						}
						if (!isValid) {
							alert("<?php putGS("You must select at least one template to perform an action."); ?>");
							dropdownElement.options[0].selected = true;
							return;
						}
					} else {
						dropdownElement.options[0].selected = true;
						return;
					}

					// Get the index of the "delete" option.
					deleteOptionIndex = -1;
					// translateOptionIndex = -1;
					for (var index = 0; index < dropdownElement.options.length; index++) {
						if (dropdownElement.options[index].value == "delete") {
							deleteOptionIndex = index;
						}
						// if (dropdownElement.options[index].value == "translate") {
							// translateOptionIndex = index;
						// }
					}

					// if the user has selected the "delete" option
					if (dropdownElement.selectedIndex == deleteOptionIndex) {
						ok = confirm("<?php putGS("Are you sure you want to delete the selected templates?"); ?>");
						if (!ok) {
							dropdownElement.options[0].selected = true;
							return;
						}
					}

					// if the user selected the "translate" option
					// if ( (dropdownElement.selectedIndex == translateOptionIndex)
						// && (numCheckboxesChecked > 1) ) {
						// alert("<?php putGS("You may only translate one article at a time."); ?>");
						// dropdownElement.options[0].selected = true;
						// return;
					// }

					// do the action if it isnt the first or second option
					if ( (dropdownElement.selectedIndex != 0) &&  (dropdownElement.selectedIndex != 1) ) {
						dropdownElement.form.submit();
					}
				}
				</script>

                                <SELECT name="f_template_list_action" class="input_select" onchange="action_selected(this);">
                                <OPTION value=""><?php putGS("Actions"); ?>...</OPTION>
                                <OPTION value="">-----------------------</OPTION>

                                <?php if ($g_user->hasPermission('ManageTempl')) { ?>
                                <OPTION value="move"><?php putGS("Move"); ?></OPTION>
                                <?php } ?>

                                <?php if ($g_user->hasPermission('ManageTempl')) { ?>
                                <OPTION value="delete"><?php putGS("Delete"); ?></OPTION>
                                <?php } ?>
                                </SELECT>
                        </TD>
                        <TD style="padding-left: 5px; font-weight: bold;">
                                <input type="button" class="button" value="<?php putGS("Select All"); ?>" onclick="checkAll();">
                                <input type="button" class="button" value="<?php putGS("Select None"); ?>" onclick="uncheckAll();">
                        </TD>
                </TR>
                </TABLE>
        </TD>
</TR>
</TABLE>
<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="0" style="padding-top: 5px;">
<TR>
	<TD VALIGN="TOP">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
		<TR class="table_list_header">
			<TD VALIGN="TOP"><B><?php  putGS('Folders'); ?> </B></TD>
			<?php
			if ($g_user->hasPermission("DeleteTempl")) {
				echo '<TD><B> '.getGS('Delete').' </B></TD>';
			}
			?>
		</TR>
		<?php
		$currentFolder = $listbasedir;
		if (empty($currentFolder)) {
			$currentFolder = "/";
		}
		if (isset($dirs)) {
			sort($dirs);
			$color = 0;
			foreach ($dirs as $dirname) {
				$tr_class = "";
				if ($color) {
					$color = 0;
					$tr_class = "class=\"list_row_even\"";
				} else {
					$color = 1;
					$tr_class = "class=\"list_row_odd\"";
				}
				print "<TR $tr_class><TD valign=\"center\"><IMG SRC='".$Campsite["ADMIN_IMAGE_BASE_URL"]."/folder.png' BORDER='0'>&nbsp;<A HREF='?Path=".urlencode("$listbasedir/$dirname")."'>$dirname</A></TD>";

				if ($g_user->hasPermission("DeleteTempl")) {
					print '<TD ALIGN="CENTER"><A HREF="/'.$ADMIN.'/templates/do_del.php?What=0&Path='.urlencode($listbasedir).'&Name='.urlencode($dirname).'" onclick="return confirm(\''.getGS('Are you sure you want to delete the folder $1 from $2?',htmlspecialchars($dirname),htmlspecialchars($currentFolder)).'\');"><IMG SRC="'.$Campsite["ADMIN_IMAGE_BASE_URL"].'/delete.png" BORDER="0" ALT="'.getGS('Delete folder').'" TITLE="'.getGS('Delete folder').'"></A></TD></TR>';
				} else {
					echo '</TR>';
				}
		    }
		} else {
			echo '<TR><TD COLSPAN="2">'.getGS('No folders.').'</TD></TR>' ;
		}
		?>
		</TABLE>
	</TD>
	<TD VALIGN="TOP">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
		<TR class="table_list_header">
			<TD>&nbsp;</TD>
			<TD VALIGN="TOP"><?php  putGS('Files'); ?></TD>
			<TD VALIGN="TOP"><?php  putGS('Template ID'); ?></TD>
			<?php
			if ($g_user->hasPermission("ManageTempl")) {
				echo '<TD VALIGN="TOP"><B> '.getGS('Duplicate').' </B></TD>';
				echo '<TD VALIGN="TOP"><B> '.getGS('Rename').' </B></TD>';
			}
			if($g_user->hasPermission("DeleteTempl")) {
				echo '<TD VALIGN="TOP"><B> '.getGS('Delete').' </B></TD>';
			}
			?>
		</TR>
		<?php
		if (isset($files)) {
			sort($files);
			$counter = 0;
			$color = 0;
			$numItemsThisPage = count($files);
			foreach ($files as $filename) {
				$templateName = (!empty($listbasedir) ? $listbasedir."/" : "").$filename;
				$templateName = $templateName[0] == "/" ? substr($templateName, 1) : $templateName;
				$templateObj =& new Template($templateName);
				if ($color) {
					$tr_class = "list_row_even";
				} else {
					$tr_class = "list_row_odd";
				}
				$color = !$color;
				print "<script>default_class[" . $counter ."] = \"" . $tr_class . "\";</script>";
				if (camp_is_text_file($filename)) {
					if (empty($listbasedir) && ($filename == "home.tpl")) {
						print "<TR id=\"row_" . $counter . "\" style=\"background-color:#7dcd82;\" onmouseover=\"setPointer(this, " . $counter . ", 'over');\" onmouseout=\"setPointer(this, " . $counter . ", 'out');\" >";
					} else {
						print "<TR id=\"row_" . $counter . "\" class=\"" . $tr_class . "\" onmouseover=\"setPointer(this, " . $counter . ", 'over');\" onmouseout=\"setPointer(this, " . $counter . ", 'out');\">";
					}
					print "<TD><INPUT TYPE=\"checkbox\" VALUE=\"";
					if ($templateObj->exists()) { p($templateObj->getTemplateId()); } else { putGS("N/A"); }
					print "\" NAME=\"f_template_code[]\" ID=\"checkbox_" . $counter . "\" CLASS=\"input_checkbox\" onclick=\"checkboxClick(this, " . $counter . ");\" /></TD>";
					print "<TD valign=\"center\"><IMG SRC='".$Campsite["ADMIN_IMAGE_BASE_URL"]."/generic.gif' BORDER='0'>&nbsp;<A HREF='/$ADMIN/templates/edit_template.php?f_path=" .urlencode($listbasedir)."&f_name=".urlencode($filename)."'>$filename</A></TD>";
				} elseif (camp_is_image_file($filename)) {
					print "<TR $tr_class><TD><IMG SRC='".$Campsite["ADMIN_IMAGE_BASE_URL"]."/image.png' BORDER='0'> <A HREF='/$ADMIN/templates/edit_template.php?f_path=" .urlencode($listbasedir)."&f_name=".urlencode($filename)."'>$filename</a></TD>";
				} else {
					print "<TR $tr_class><TD><IMG SRC='".$Campsite["ADMIN_IMAGE_BASE_URL"]."/generic.gif' BORDER='0'> $filename</TD>";
				}
				?>

				<td align="center"><?php if ($templateObj->exists()) { echo $templateObj->getTemplateId(); } else { putGS("N/A"); } ?></td>

				<?php
				if ($g_user->hasPermission("ManageTempl")){
					print '<TD ALIGN="CENTER"><A HREF="/'.$ADMIN.'/templates/dup.php?Path='.urlencode($listbasedir).'&Name='.urlencode($filename).'"><IMG SRC="'.$Campsite["ADMIN_IMAGE_BASE_URL"].'/duplicate.png" BORDER="0" ALT="'.getGS('Duplicate file').'" TITLE="'.getGS('Duplicate file').'"></A></TD>';
					print '<TD ALIGN="CENTER"><A HREF="/'.$ADMIN.'/templates/rename.php?Path='.urlencode($listbasedir).'&Name='.urlencode($filename).'"><IMG SRC="'.$Campsite["ADMIN_IMAGE_BASE_URL"].'/rename.png" BORDER="0" ALT="'.getGS('Rename file').'" TITLE="'.getGS('Rename file').'"></A?</TD>';
				}
				if ($g_user->hasPermission("DeleteTempl")) {
					print '<TD ALIGN="CENTER"><A HREF="/'.$ADMIN.'/templates/do_del.php?What=1&Path='.urlencode($listbasedir).'&Name='.urlencode($filename).'" onclick="return confirm(\''.getGS('Are you sure you want to delete the template $1 from folder $2?', htmlspecialchars($filename),htmlspecialchars($currentFolder)).'\');"><IMG SRC="'.$Campsite["ADMIN_IMAGE_BASE_URL"].'/delete.png" BORDER="0" ALT="'.getGS('Delete file').'" TITLE="'.getGS('Delete file').'"></A></TD></TR>';
				}
				$counter++;
			}
		}
		else{
			echo '<TR><TD COLSPAN="2">'.getGS('No templates.').'</TD></TR>' ;
		}
		?>
		</TABLE>
	</TD>
</TR>
</TABLE>
<INPUT TYPE="HIDDEN" NAME="f_current_folder" VALUE="<?php p($currentFolder); ?>">
</FORM>
