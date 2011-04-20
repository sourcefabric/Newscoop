<?php
camp_load_translation_strings("plugin_poll");

// Check permissions
if (!$g_user->hasPermission('plugin_poll')) {
    camp_html_display_error(getGS('You do not have the right to manage polls.'));
    exit;
}

$f_language_selected = Input::Get('f_language_selected', 'int');
$f_poll_limit = Input::Get('f_poll_limit', 'int', 20);
$f_poll_offset = Input::Get('f_poll_offset', 'int', 0);
$f_poll_order = Input::Get('f_poll_order', 'string', 'bynumber');

$parents = Poll::getPolls(array('language_id' => $f_language_selected, 'parent_poll_nr' => 0), null, $f_poll_offset, $f_poll_limit, $f_poll_order);

// add the copys
foreach ($parents as $poll) {
    $polls[] = $poll;
    $copys = Poll::getPolls(array('language_id' => $poll->getLanguageId(), 'parent_poll_nr' => $poll->getNumber()));
    foreach ($copys as $poll) {
        $polls[] = $poll;
    }
}


$pager = new SimplePager(Poll::countPolls(), $f_poll_limit, "f_poll_offset", "index.php?f_poll_order=$f_poll_order&amp;", false);
$allLanguages = Language::GetLanguages();

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

echo camp_html_breadcrumbs(array(
    array(getGS('Plugins'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
    array(getGS('Polls'), ''),
));

// DO NOT DELETE!!! Needed for localizer
// getGS("Polls");
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/campsite-checkbox.js"></script>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
    <TD><A HREF="edit.php" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
    <TD><A HREF="edit.php" ><B><?php  putGS("Add new Poll"); ?></B></A></TD>
</tr>
</TABLE>
<p>

<FORM name="poll_list" action="action.php" method="POST">
<?php echo SecurityToken::FormParameter(); ?>
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
                        <SELECT NAME="f_language_selected" id="f_language_selected" class="input_select" onchange="location.href='index.php?f_language_selected='+document.getElementById('f_language_selected').options[document.getElementById('f_language_selected').selectedIndex].value;">
                        <option value="0"><?php putGS("All"); ?></option>
                        <?php
                        foreach ($allLanguages as $languageItem) {
                            echo '<OPTION value="'.$languageItem->getLanguageId().'"' ;
                            if ($languageItem->getLanguageId() == $f_language_selected) {
                                echo " selected";
                            }
                            echo '>'.htmlspecialchars($languageItem->getNativeName()).'</option>';
                        } ?>
                        </SELECT>
                    </TD>
                </TR>
                </TABLE>
            </TD>
            <TD style="padding-left: 20px;">
                <script>
                function action_selected(dropdownElement)
                {
                    // Verify that at least one checkbox has been selected.
                    checkboxes = document.forms.poll_list["f_poll_code[]"];
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
                            alert("<?php putGS("You must select at least one poll to perform an action."); ?>");
                            dropdownElement.options[0].selected = true;
                            return;
                        }
                    }
                    else {
                        dropdownElement.options[0].selected = true;
                        return;
                    }

                    // Get the index of the "delete" and "reset" option.
                    deleteOptionIndex = -1;
                    translateOptionIndex = -1;

                    for (var index = 0; index < dropdownElement.options.length; index++) {
                        if (dropdownElement.options[index].value == "delete") {
                            deleteOptionIndex = index;
                        }
                        if (dropdownElement.options[index].value == "reset") {
                            resetOptionIndex = index;
                        }
                    }

                    // if the user has selected the "delete" option
                    if (dropdownElement.selectedIndex == deleteOptionIndex) {
                        ok = confirm("<?php putGS("Are you sure you want to delete the selected polls?"); ?>");
                        if (!ok) {
                            dropdownElement.options[0].selected = true;
                            return;
                        }
                    }

                    // if the user has selected the "reset" option
                    if (dropdownElement.selectedIndex == resetOptionIndex) {
                        ok = confirm("<?php putGS("Are you sure you want to reset counters on the selected polls?"); ?>");
                        if (!ok) {
                            dropdownElement.options[0].selected = true;
                            return;
                        }
                    }

                    // do the action if it isnt the first or second option
                    if ( (dropdownElement.selectedIndex != 0)) {
                        dropdownElement.form.submit();
                    }
                }
                </script>
                <SELECT name="f_poll_list_action" class="input_select" onchange="action_selected(this);">
                    <OPTION value=""><?php putGS("Actions"); ?>...</OPTION>
                    <OPTION value="delete"><?php putGS("Delete"); ?></OPTION>
                    <OPTION value="reset"><?php putGS("Reset"); ?></OPTION>
                </SELECT>
            </TD>

            <TD style="padding-left: 5px; font-weight: bold;">
                <input type="button" class="button" value="<?php putGS("Select All"); ?>" onclick="checkAll(<?php p(count($polls)); ?>);">
                <input type="button" class="button" value="<?php putGS("Select None"); ?>" onclick="uncheckAll(<?php p(count($polls)); ?>);">
            </TD>
        </TR>
        </TABLE>
    </TD>

</TR>
</TABLE>
<p>

<table class="indent">
    <tr>
        <td>
            <?php echo $pager->render(); ?>
        </td>
    </tr>
</table>

<?php
$counter = 0;
$color= 0;

if (count($polls)) {
    ?>
    <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list" style="padding-top: 5px;" width="95%">
        <TR class="table_list_header">
            <TD width="10">&nbsp;</TD>
            <TD ALIGN="LEFT" VALIGN="TOP" width="800">
                <A href="index.php?f_poll_offset=<?php echo $f_poll_offset ?>&amp;f_poll_order=byname"><?php  putGS("Name"); ?></a>
                &nbsp;<SMALL>(<?php putGS('click to edit'); ?></SMALL>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="30">
                <A href="index.php?f_poll_offset=<?php echo $f_poll_offset ?>&amp;f_poll_order=bybegin"><?php  putGS("Begin"); ?></a>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="30">
                <A href="index.php?f_poll_offset=<?php echo $f_poll_offset ?>&amp;f_poll_order=byend"><?php  putGS("End"); ?></a>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="20">&nbsp;</TD>
            <TD ALIGN="center" VALIGN="TOP" width="20">&nbsp;</TD>
            <TD align="center" valign="top" width="20">&nbsp;</TD>
            <TD align="center" valign="top" width="20">&nbsp;</TD>
        </TR>
        <?php

        $used = array();

        foreach ($polls as $poll) {
            if ($color) {
                $rowClass = "list_row_even";
            } else {
                $rowClass = "list_row_odd";
            }
            $color = !$color;
            ?>
            <script>default_class[<?php p($counter); ?>] = "<?php p($rowClass); ?>";</script>
            <TR id="row_<?php p($counter); ?>" class="<?php p($rowClass); ?>" onmouseover="setPointer(this, <?php p($counter); ?>, 'over');" onmouseout="setPointer(this, <?php p($counter); ?>, 'out');">
                <TD>
                    <input type="checkbox" value="<?php p((int)$poll->getNumber().'_'.(int)$poll->getLanguageId()); ?>" name="f_poll_code[]" id="checkbox_<?php p($counter); ?>" class="input_checkbox" onclick="checkboxClick(this, <?php p($counter); ?>);">
                </TD>

                <td>
                    <?php
                    if (!array_key_exists($poll->getNumber(), $used) && $poll->getProperty('parent_poll_nr') == 0) {
                        p($poll->getNumber().'.');
                        $used[$poll->getNumber()] = true;
                    } elseif ($poll->getProperty('parent_poll_nr')) {
                        p('\'-> '.$poll->getNumber().'.');
                    } else {
                        p('&nbsp;&nbsp;');
                    }
                    ?>
                    <a href="edit.php?f_poll_nr=<?php p($poll->getNumber()); ?>&amp;f_fk_language_id=<?php p($poll->getLanguageId()); ?>">
                        <?php p($poll->getProperty('title')); ?>
                    </a>
                    &nbsp; (<?php p($poll->getLanguageName()); ?>)
                </td>

                <td align="center"><?php p($poll->getProperty('date_begin')); ?></td>
                <td align="center"><?php p($poll->getProperty('date_end')); ?></td>

                <td align='center'>
                <?php if (!$poll->getProperty('parent_poll_nr')) { ?>
                    <a href="translate.php?f_poll_nr=<?php p($poll->getNumber()); ?>&f_fk_language_id=<?php p($poll->getLanguageId()) ?>" title="<?php putGS('Translate') ?>">
                        <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/translate.png" BORDER="0">
                    </a>
                <?php } ?>
                </td>

                <td align='center'>
                <?php if ($poll->isExtended()) { ?>
                    <a href="copy.php?f_poll_nr=<?php p($poll->getNumber()); ?>&f_fk_language_id=<?php p($poll->getLanguageId()) ?>" title="<?php putGS('Copy') ?>">
                        <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/duplicate.png" BORDER="0">
                    </a>
                <?php } ?>
                </td>

                <td align='center'>
                    <a href="result.php?f_poll_nr=<?php p($poll->getNumber()); ?>&f_fk_language_id=<?php p($poll->getLanguageId()); ?>" title="<?php putGS('Result') ?>">
                        <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/preview.png" BORDER="0">
                    </a>
                </td>

                <td align='center'>
                    <a href="javascript: if (confirm('<?php echo camp_javascriptspecialchars(getGS('Are you sure you want to delete the poll "$1"?', $poll->getProperty('title'))); ?>')) location.href='do_delete.php?f_poll_nr=<?php p($poll->getNumber()); ?>&amp;f_fk_language_id=<?php p($poll->getLanguageId()); ?>&amp;<?php echo SecurityToken::URLParameter(); ?>'">
                        <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0">
                    </a>
                </td>

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
    <LI><?php  putGS('No polls.'); ?></LI>
    </BLOCKQUOTE>
    <?php
}
?>
