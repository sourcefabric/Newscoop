<?php
camp_load_translation_strings("plugin_interview");

// DO NOT DELETE!!! Needed for localizer
// getGS("Interviews");
// getGS("Administer");
// getGS("Moderate");
// getGS("Interview Guest");
// getGS("draft");
// getGS("pending");
// getGS("published");
// getGS("rejected");
// getGS("Guest Login");
// getGS("Guest Email");
// getGS("Image Description");
// getGS("Delete Image");
// getGS("Short Description");
// getGS("Questions Limit");

echo camp_html_breadcrumbs(array(
    array(getGS('Plugins'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
    array(getGS('Interviews'), ''),
));
?>

<script type="text/javascript">
function ajax_action(action)
{
    $('#f_action').val(action);

    // save & reload
    var myAjax = $.get(
        '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/<?php p(dirname($GLOBALS['call_script'])) ?>/ajax_action.php',
        $('#interviews_list').serialize(),
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
if (strpos($call_script, '/interview/moderator/') !== false && $g_user->hasPermission('plugin_interview_moderator')) {
    $is_moderator = true;
    $constraints .= "moderator_user_id is {$g_user->getUserId()} ";
}
if (strpos($call_script, '/interview/guest/') !== false && $g_user->hasPermission('plugin_interview_guest')) {
    $is_guest = true;
    $constraints .= "guest_user_id is {$g_user->getUserId()} ";
}

// Check permissions
if (!$is_admin && !$is_moderator && !$is_guest) {
    camp_html_display_error(getGS('You do not have the right to manage interviews.'));
    exit;
}

$f_length = Input::Get('f_length', 'int', 20);
$f_start = Input::Get('f_start', 'int', 0);
$f_order = Input::Get('f_order', 'string', 'byorder');

if ($f_language_id = Input::Get('f_language_id', 'int')) {
    $constraints .= "language_id is $f_language_id ";
}

if ($f_status = mysql_escape_string(Input::Get('f_status', 'string'))) {
    $constraints .= "status is $f_status ";
}

$parameters = array(
    'constraints' => $constraints,
    'length' => $f_length,
    'order' => "$f_order ASC"
);

define('PLUGIN_INTERVIEW_ADMIN_MODE', true);

$InterviewsList = new InterviewsList($f_start, $parameters);
$total = $InterviewsList->getTotalCount();
$count = $InterviewsList->getLength();
$pager = new SimplePager($total, $f_length, "f_start", "index.php?f_order=$f_order&amp;", false);

$TotalList = new InterviewsList();
$total = $TotalList->count;

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/campsite-checkbox.js"></script>

<?php if ($is_admin) { ?>
    <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
    <TR>
        <TD><A HREF="javascript: void(0);" onclick="window.open('edit.php', 'edit_interview', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=700, height=700, top=100, left=100');" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
        <TD><A HREF="javascript: void(0);" onclick="window.open('edit.php', 'edit_interview', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=700, height=700, top=100, left=100');" ><B><?php  putGS("Add new Interview"); ?></B></A></TD>
    </tr>
    </TABLE>
<?php } ?>

<p>

<FORM name="selector" method="get">
<TABLE CELLSPACING="0" CELLPADDING="0" class="table_actions">
<TR>
    <TD>
        <TABLE cellpadding="0" cellspacing="0">
        <TR>
            <TD ALIGN="left">
                <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" >
                <TR>
                    <TD><?php  putGS('Language'); ?>:</TD>
                    <TD valign="middle">
                        <SELECT NAME="f_language_id" class="input_select" onchange="this.form.submit()">
                        <option value="0"><?php putGS("All"); ?></option>
                        <?php
                        foreach (Interview::GetCampLanguagesList() as $k => $v) {
                            echo '<OPTION value="'.$k.'"' ;
                            if ($k == $f_language_id) {
                                echo " selected";
                            }
                            echo '>'.htmlspecialchars($v).'</option>';
                        } ?>
                        </SELECT>
                    </TD>
                </TR>
                </TABLE>
            </TD>
            <TD ALIGN="left">
                <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" >
                <TR>
                    <TD><?php  putGS('Status'); ?>:</TD>
                    <TD valign="middle">
                        <SELECT NAME="f_status" class="input_select" onchange="this.form.submit()">
                        <option value="0"><?php putGS("All"); ?></option>
                        <?php
                        foreach (array('draft', 'pending', 'published', 'rejected') as $item) {
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

            <?php if ($is_admin) { ?>
              <TD style="padding-left: 20px;">
                <script>
                function action_selected(dropdownElement)
                {
                    // Verify that at least one checkbox has been selected.
                    checkboxes = document.forms.interviews_list["f_interviews[]"];
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
                        if (dropdownElement.options[index].value == "interviews_delete") {
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
                    <OPTION value="interviews_delete"><?php putGS("Delete"); ?></OPTION>
                    <OPTION value="interviews_setdraft"><?php putGS("Status: Draft"); ?></OPTION>
                    <OPTION value="interviews_setpending"><?php putGS("Status: Pending"); ?></OPTION>
                    <OPTION value="interviews_setpublished"><?php putGS("Status: Published"); ?></OPTION>
                    <OPTION value="interviews_setrejected"><?php putGS("Status: Offline"); ?></OPTION>
                </SELECT>
              </TD>

              <TD style="padding-left: 5px; font-weight: bold;">
                <input type="button" class="button" value="<?php putGS("Select All"); ?>" onclick="checkAll(<?php p($count); ?>);">
                <input type="button" class="button" value="<?php putGS("Select None"); ?>" onclick="uncheckAll(<?php p($count); ?>);">
              </TD>
            <?php } ?>
        </TR>
        </TABLE>
    </TD>

</TR>
</TABLE>
</form>

<?php
$counter = 0;
$color= 0;

if ($InterviewsList->getLength()) {
    ?>
    <FORM name="interviews_list" id="interviews_list" action="action.php" method="POST">
	<?php echo SecurityToken::FormParameter(); ?>
    <input type="hidden" name="f_action" id="f_action" />
    <input type="hidden" name="f_new_pos" id="f_new_pos" />

    <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list" style="padding-top: 5px;" width="95%">
        <TR class="table_list_header">

            <?php if($is_admin) { ?>
                <TD width="10">&nbsp;</TD>
            <?php } ?>

            <TD ALIGN="LEFT" VALIGN="TOP" width="500">
                <A href="index.php?f_start=<?php echo $f_start ?>&amp;f_order=byname"><?php  putGS("Name"); ?></a>
                &nbsp;<SMALL>
                <?php if ($is_admin) putGS('Click to edit'); ?>
                </SMALL>
            </TD>

            <?php if ($is_admin) { ?>
                <TD align="center" valign="top"><?php putGS("Order"); ?></TD>
            <?php } ?>

            <TD ALIGN="center" VALIGN="TOP" width="30">
                <A href="index.php?f_start=<?php echo $f_start ?>&amp;f_order=bystatus"><?php  putGS("Status"); ?></a>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="30">
                <A href="index.php?f_start=<?php echo $f_start ?>&amp;f_order=bymoderator"><?php  putGS("Moderator"); ?></a>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="30">
                <A href="index.php?f_start=<?php echo $f_start ?>&amp;f_order=byguest"><?php  putGS("Guest"); ?></a>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="30">
                <A href="index.php?f_start=<?php echo $f_start ?>&amp;f_order=byquestions_begin"><?php  putGS("Questions Begin"); ?></a>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="30">
                <A href="index.php?f_start=<?php echo $f_start ?>&amp;f_order=byquestions_end"><?php  putGS("Questions End"); ?></a>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="30">
                <A href="index.php?f_start=<?php echo $f_start ?>&amp;f_order=byinterview_begin"><?php  putGS("Interview Begin"); ?></a>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="30">
                <A href="index.php?f_start=<?php echo $f_start ?>&amp;f_order=byinterview_end"><?php  putGS("Interview End"); ?></a>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="20"><?php putGS('List Items') ?></TD>

            <?php if ($is_admin) { ?>
                <TD ALIGN="center" VALIGN="TOP" width="20"><?php putGS('Invite') ?></TD>
                <TD align="center" valign="top" width="20">&nbsp;</TD>
            <?php } ?>
        </TR>
        <?php

        while ($MetaInterview = $InterviewsList->current) {
            $InterviewsList->defaultIterator()->next();

            if ($color) {
                $rowClass = "list_row_even";
            } else {
                $rowClass = "list_row_odd";
            }
            $color = !$color;
            ?>
            <script>default_class[<?php p($counter); ?>] = "<?php p($rowClass); ?>";</script>
            <TR id="row_<?php p($counter); ?>" class="<?php p($rowClass); ?>" onmouseover="setPointer(this, <?php p($counter); ?>, 'over');" onmouseout="setPointer(this, <?php p($counter); ?>, 'out');">

                <?php if($is_admin) { ?>
                    <TD>
                        <input type="checkbox" value="<?php p((int)$MetaInterview->identifier); ?>" name="f_interviews[]" id="checkbox_<?php p($counter); ?>" class="input_checkbox" onclick="checkboxClick(this, <?php p($counter); ?>);">
                    </TD>
                <?php } ?>

                <td>
                    <?php
                    p($MetaInterview->identifier.'.');

                    if ($is_admin) {
                        ?><a href="javascript: void(0);" onclick="window.open('edit.php?f_interview_id=<?php p($MetaInterview->identifier); ?>', 'edit_interview', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=700, height=700, top=100, left=100');"><?php p($MetaInterview->title); ?></a><?php
                    } else {
                        p($MetaInterview->title);
                    }
                    ?>
                    (<?php p($MetaInterview->language->name); ?>)
                </td>

                <?php if ($is_admin) { ?>
                  <TD ALIGN="right" valign="middle" NOWRAP style="padding: 1px;">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td width="18px">
							<?php if ($count) { ?>
							<a href="javascript:
                            uncheckAll(<?php p($count); ?>);
                            document.getElementById('checkbox_<?php p($counter); ?>').checked = true;
                            ajax_action('interview_move_up_rel')
                            "><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/up-16x16.png" width="16" height="16" border="0"></A>
							<?php } ?>
						</td>
						<td width="20px">
							<?php if ($count) { ?>
							<a href="javascript:
                            uncheckAll(<?php p($count); ?>);
                            document.getElementById('checkbox_<?php p($counter); ?>').checked = true;
                            ajax_action('interview_move_down_rel')
                            "><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/down-16x16.png" width="16" height="16" border="0"></A>
							<?php } ?>
						</td>

						<td>
							<select name="f_position_<?php p($counter);?>"
                                onChange="document.forms.interviews_list.f_new_pos.value = document.forms.interviews_list.f_position_<?php p($counter); ?>.value;
                                          document.getElementById('checkbox_<?php p($counter); ?>').checked = true;
                                          ajax_action('interview_move_abs')"
                                class="input_select" style="font-size: smaller;">
							<?php
							for ($j = 1; $j <= $total; $j++) {
								if (($MetaInterview->position) == $j) {
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

                <td align="center"><?php putGS($MetaInterview->status); ?></td>
                <td align="center"><?php p($MetaInterview->moderator->name); ?></td>
                <td align="center"><?php p($MetaInterview->guest->name); ?></td>
                <td align="center"><?php p(substr($MetaInterview->questions_begin, 0, 16)); ?></td>
                <td align="center"><?php p(substr($MetaInterview->questions_end, 0, 16)); ?></td>
                <td align="center"><?php p(substr($MetaInterview->interview_begin, 0, 16)); ?></td>
                <td align="center"><?php p(substr($MetaInterview->interview_end, 0, 16)); ?></td>

                <td align='center'>
                    <a href='list_items.php?f_interview_id=<?php p($MetaInterview->identifier); ?>'>
                        <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/preview.png" BORDER="0">
                    </a>
                </td>

                <?php if($is_admin) { ?>
                    <td align='center'>
                        <a href="javascript: void(0);" onclick="window.open('invitation.php?f_interview_id=<?php p($MetaInterview->identifier); ?>', 'edit_interview', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=720, height=700, top=200, left=100');"><IMG SRC="<?php echo '/'.CS_PLUGINS_DIR.'/interview/css/', ($MetaInterview->guest_invitation_sent || $MetaInterview->questioneer_invitation_sent) ? 'email_red.png' : 'email_green.png' ?>" BORDER="0" alt="Invite"></a>
                    </td>
                    <td align='center'>
                        <a href="javascript: if (confirm('<?php putGS('Are you sure you want to delete the selected item(s)?') ?>')) {
                            uncheckAll(<?php p($count); ?>);
                            document.getElementById('checkbox_<?php p($counter); ?>').checked = true;
                            ajax_action('interviews_delete')
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
    <LI><?php  putGS('No interviews.'); ?></LI>
    </BLOCKQUOTE>
    <?php
}
?>
