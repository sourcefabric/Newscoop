<?php
camp_load_translation_strings("plugin_interview");

echo camp_html_breadcrumbs(array(
    array(getGS('Plugins'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
    array(getGS('Interviews'), $Campsite['WEBSITE_URL'] . '/admin/interview/admin/index.php'),
    array(getGS('List Items'), ''),
));
?>

<script type="text/javascript">
function ajax_action(action)
{
    $('#f_action').val(action);

    // save & reload
    var myAjax = $.get(
        '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/<?php p(dirname($GLOBALS['call_script'])) ?>/ajax_action.php',
        $('#items_list').serialize(),
        function() {
            window.location.reload();
        });
}
</script>
<?php

// User role depend on path to this file. Tricky: moderator and guest folders are just symlink to admin files!
if (strpos($call_script, '/interview/admin/') !== false && $g_user->hasPermission('plugin_interview_admin')) {
    $is_admin = true;   
}
if (strpos($call_script, '/interview/moderator/') !== false && $g_user->hasPermission('plugin_interview_moderator')) { $is_moderator = true;   
}
if (strpos($call_script, '/interview/guest/') !== false && $g_user->hasPermission('plugin_interview_guest')) {
    $is_guest = true;   
}

// Check permissions
if (!$is_admin && !$is_moderator && !$is_guest) {
    camp_html_display_error(getGS('You do not have the right to manage interviews.'));
    exit;
}

$f_interview_id = Input::Get('f_interview_id', 'int');

if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
    exit;
}

$f_length = Input::Get('f_length', 'int', 20);
$f_start = Input::Get('f_start', 'int', 0);
$f_order = Input::Get('f_order', 'string', 'byorder');

if ($f_status = Input::Get('f_status', 'string')) {
    $constraints .= "status is $f_status ";    
} elseif ($is_guest) {
    $constraints .= "status not draft ";
}

$parameters = array(
    'length' => $f_length,
    'order' => "$f_order ASC",
    'constraints' => $constraints
);

define('ADMIN_INTERVIEW_ID', $f_interview_id);  # we need this constant in InterviewItemsList::createList()
$Interview = new Interview($f_interview_id);
$InterviewItemsList = new InterviewItemsList($f_start, $parameters);

$count = $InterviewItemsList->count;

$TotalItems = new InterviewItemsList();
$total = $TotalItems->count;

$pager = new SimplePager($count, $f_length, "f_start", "index.php?f_order=$f_order&amp;f_interview_id=$f_interview_id&amp;", false);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/campsite-checkbox.js"></script>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
    <TD><B><?php  putGS('Interview: $1', $Interview->getTitle()); ?></B></A></TD>
</tr>
</TABLE>
<p>

<FORM name="selector" method="get">
<input type="hidden" name="f_interview_id" id="f_interview_id" value="<?php p($f_interview_id) ?>" />
<TABLE CELLSPACING="0" CELLPADDING="0" class="table_actions">
<TR>
    <TD>
        <TABLE cellpadding="0" cellspacing="0">
        <TR>
            <TD ALIGN="left">
                <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" >
                <TR>
                    <TD><?php  putGS('Status'); ?>:</TD>
                    <TD valign="middle">
                        <SELECT NAME="f_status" class="input_select" onchange="this.form.submit()">
                        <option value="0"><?php putGS("All"); ?></option>
                        <?php
                        if ($is_admin || $is_moderator) {
                            $displ_status = array('draft', 'pending', 'published', 'rejected');   
                        } else {
                            $displ_status = array('pending', 'published', 'rejected');    
                        }
                        foreach ($displ_status as $item) {
                            echo '<OPTION value="'.$item.'"' ;
                            if ($item == $f_status) {
                                echo " selected";
                            }
                            echo '>'.getGS($item).'</option>';
                        } ?>
                        </SELECT>
                    </TD>
                </TR>
                </TABLE>
            </TD>
            
            <?php if ($is_admin || $is_moderator) { ?>
              <TD style="padding-left: 20px;">
                <script>
                function action_selected(dropdownElement)
                {
                    // Verify that at least one checkbox has been selected.
                    checkboxes = document.forms.items_list["f_items[]"];
                    if (checkboxes) {
                        isValid = false;
                        numCheckboxesChecked = 0;
                        // Special case for single checkbox
                        // (when there is only one article in the section).
                        if (!checkboxes.length) {
                            isValid = checkboxes.checked;
                            numCheckboxesChecked = isValid ? 1 : 0;
                        }
                        else {
                            // Multiple checkboxes
                            for (var index = 0; index < checkboxes.length; index++) {
                                if (checkboxes[index].checked) {
                                    isValid = true;
                                    numCheckboxesChecked++;
                                }
                            }
                        }
                        if (!isValid) {
                            alert("<?php putGS("You must select at least one item to perform an action."); ?>");
                            dropdownElement.options[0].selected = true;
                            return;
                        }
                    }
                    else {
                        dropdownElement.options[0].selected = true;
                        return;
                    }

                    // Get the index of the "delete" option.
                    deleteOptionIndex = -1;

                    for (var index = 0; index < dropdownElement.options.length; index++) {
                        if (dropdownElement.options[index].value == "items_delete") {
                            deleteOptionIndex = index;
                        }
                    }

                    // if the user has selected the "delete" option
                    if (dropdownElement.selectedIndex == deleteOptionIndex) {
                        ok = confirm("<?php putGS("Are you sure you want to delete the selected item(s)?"); ?>");
                        if (!ok) {
                            dropdownElement.options[0].selected = true;
                            return;
                        }
                    }

                    // do the action if it isnt the first or second option
                    if ( (dropdownElement.selectedIndex != 0)) {
                        ajax_action(document.forms.selector.f_action.value);
                    }
                }
                </script>
                <SELECT name="f_action" class="input_select" onchange="action_selected(this);">
                    <OPTION value=""><?php putGS("Actions"); ?>...</OPTION>
                    <OPTION value="items_delete"><?php putGS("Delete"); ?></OPTION>
                    <OPTION value="items_setdraft"><?php putGS("Status: Draft"); ?></OPTION>
                    <OPTION value="items_setpending"><?php putGS("Status: Pending"); ?></OPTION>
                    <OPTION value="items_setpublished"><?php putGS("Status: Published"); ?></OPTION>
                    <OPTION value="items_setrejected"><?php putGS("Status: Offline"); ?></OPTION>
                </SELECT>
              </TD>
        
              <TD style="padding-left: 5px; font-weight: bold;">
                <input type="button" class="button" value="<?php putGS("Select All"); ?>" onclick="checkAll(<?php p($count); ?>);">
                <input type="button" class="button" value="<?php putGS("Select None"); ?>" onclick="uncheckAll(<?php p($count); ?>);">
                <input type="button" class="button" value="Add new" onclick="window.open('edit_item.php?f_item_id=0&f_interview_id=<?php p($f_interview_id) ?>&questioneer=<?php echo $g_user->getUserId(); ?>', 'edit_item', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=700, height=450, top=200, left=100');" />
              </TD>
            <?php } ?>
        </TR>
        </TABLE>
    </TD>

</TR>
</TABLE>
</FORM>

<?php
$counter = 0;
$color= 0;

if ($InterviewItemsList->getLength()) {
    ?>
    <FORM name="items_list" id="items_list" action="items_action.php" method="POST">
    <input type="hidden" name="f_interview_id" id="f_interview_id" value="<?php p($f_interview_id) ?>" />
    <input type="hidden" name="f_action" id="f_action">
    <input type="hidden" name="f_new_pos" id="f_new_pos" />
    <?php echo SecurityToken::FormParameter(); ?>
    <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list" style="padding-top: 5px;" width="95%">
        <TR class="table_list_header">
        
            <?php if ($is_admin || $is_moderator) { ?>
                <TD width="10">&nbsp;</TD>
            <?php } ?>
            
            <TD ALIGN="LEFT" VALIGN="TOP" width="500">
                <A href="list_items.php?f_start=<?php echo $f_start ?>&amp;f_interview_id=<?php p($f_interview_id) ?>&amp;f_order=byquestion"><?php  putGS("Question"); ?></a>
                &nbsp;<SMALL>
                <?php if ($is_admin || $is_moderator) putGS('Click to edit'); ?>
                <?php if ($is_guest) putGS('Click to answer'); ?>
                </SMALL>
            </TD>            
            <TD VALIGN="TOP" width="500">
                <A href="list_items.php?f_start=<?php echo $f_start ?>&amp;f_interview_id=<?php p($f_interview_id) ?>&amp;f_order=byanswer"><?php  putGS("Answer"); ?></a>
                &nbsp;<SMALL>
                <?php if ($is_admin || $is_guest) putGS('Click to edit'); ?>
                </SMALL>
            </TD>
            
            <?php if ($is_admin || $is_moderator) { ?> 
                <TD align="center" valign="top"><?php putGS("Order"); ?></TD>
            <?php } ?> 
              
            <TD ALIGN="center" VALIGN="TOP" width="30">
                <A href="list_items.php?f_start=<?php echo $f_start ?>&amp;f_interview_id=<?php p($f_interview_id) ?>&amp;f_order=bystatus"><?php  putGS("Status"); ?></a>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="30">
                <A href="list_items.php?f_start=<?php echo $f_start ?>&amp;f_interview_id=<?php p($f_interview_id) ?>&amp;f_order=byquestioneer"><?php  putGS("Questioneer"); ?></a>
            </TD>
            
            <?php if ($is_admin || $is_moderator) { ?>
                <TD ALIGN="center" VALIGN="TOP" width="30">
                    <A href="list_items.php?f_start=<?php echo $f_start ?>&amp;f_interview_id=<?php p($f_interview_id) ?>&amp;f_order=byinterview_end"><?php  putGS("Delete"); ?></a>
                </TD>
            <?php } ?>
        </TR>
        <?php
    
        $used = array();
     
        while ($MetaInterviewItem = $InterviewItemsList->current) {
            $InterviewItemsList->defaultIterator()->next();
            
            if ($color) {
                $rowClass = "list_row_even";
            } else {
                $rowClass = "list_row_odd";
            }
            $color = !$color;
            ?>
            <script>default_class[<?php p($counter); ?>] = "<?php p($rowClass); ?>";</script>
            <TR id="row_<?php p($counter); ?>" class="<?php p($rowClass); ?>" onmouseover="setPointer(this, <?php p($counter); ?>, 'over');" onmouseout="setPointer(this, <?php p($counter); ?>, 'out');">
                
                <?php if ($is_admin || $is_moderator) { ?>
                    <TD>
                        <input type="checkbox" value="<?php p((int)$MetaInterviewItem->identifier); ?>" name="f_items[]" id="checkbox_<?php p($counter); ?>" class="input_checkbox" onclick="checkboxClick(this, <?php p($counter); ?>);">
                    </TD>
                <?php } ?>
                
                <td>
                    <a href="javascript: void(0);" onclick="window.open('edit_item.php?f_item_id=<?php p($MetaInterviewItem->identifier) ?>', 'edit_item', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=700, height=450, top=200, left=100');">
                        <?php p($MetaInterviewItem->question); ?>
                    </a>
                </td>
               
                <td>
                    <a href="javascript: void(0);" onclick="window.open('edit_item.php?f_item_id=<?php p($MetaInterviewItem->identifier) ?>', 'edit_item', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=700, height=450, top=200, left=100');">
                        <?php p($MetaInterviewItem->answer); ?>
                    </a>
                </td>
                
                <?php if ($is_admin || $is_moderator) { ?> 
                  <TD ALIGN="right" valign="middle" NOWRAP style="padding: 1px;">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td width="18px">
							<?php if ($count) { ?>
							<a href="javascript: 
                            uncheckAll(<?php p($count); ?>);
                            document.getElementById('checkbox_<?php p($counter); ?>').checked = true;
                            ajax_action('item_move_up_rel') 
                            "><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/up-16x16.png" width="16" height="16" border="0"></A>
							<?php } ?>
						</td>
						<td width="20px">
							<?php if ($count) { ?>
							<a href="javascript: 
                            uncheckAll(<?php p($count); ?>);
                            document.getElementById('checkbox_<?php p($counter); ?>').checked = true;
                            ajax_action('item_move_down_rel') 
                            "><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/down-16x16.png" width="16" height="16" border="0"></A>
							<?php } ?>
						</td>

						<td>
							<select name="f_position_<?php p($counter);?>" 
                                onChange="document.forms.items_list.f_new_pos.value = document.forms.items_list.f_position_<?php p($counter); ?>.value;
                                          document.getElementById('checkbox_<?php p($counter); ?>').checked = true;
                                          ajax_action('item_move_abs')"
                                class="input_select" style="font-size: smaller;">
							<?php
							for ($j = 1; $j <= $total; $j++) {
								if (($MetaInterviewItem->position) == $j) {
									echo "<option value=\"$j\" selected>$j</option>\n";
								} else {
									echo "<option value=\"$j\">$j</option>\n";
								}
							}
							?>
							</select>
						</td>

					</tr>
					</table>
				  </TD>
				<?php } ?>
                
                <td align="center"><?php p($MetaInterviewItem->status); ?></td>
                
                <td align="center"><?php p($MetaInterviewItem->questioneer->name); ?></td>
                
                <?php if ($is_admin || $is_moderator) { ?>             
                    <td align='center'>
                        <a href="javascript: if (confirm('<?php putGS('Are you sure you want to delete the selected item(s)?') ?>')) {
                            uncheckAll(<?php p($count); ?>);
                            document.getElementById('checkbox_<?php p($counter); ?>').checked = true;
                            ajax_action('items_delete') 
                            }">
                            <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0">
                        </a>
                    </td>
                <?php } ?>
            </tr>
            <?php
            $counter++;
        }
      ?>
    </table>
</FORM>
<?php
} else {?>
    <BLOCKQUOTE>
    <LI><?php  putGS('No interview items.'); ?></LI>
    </BLOCKQUOTE>  
    <?php 
}
?>
