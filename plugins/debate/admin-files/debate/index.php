<?php
$translator = \Zend_Registry::get('container')->getService('translator');

// Check permissions
if (!$g_user->hasPermission('plugin_debate_admin')) {
    camp_html_display_error($translator->trans('You do not have the right to manage debates.', array(), 'plugin_debate'));
    exit;
}

$f_language_selected = Input::Get('f_language_selected', 'int');
$f_debate_limit = Input::Get('f_debate_limit', 'int', 20);
$f_debate_offset = Input::Get('f_debate_offset', 'int', 0);
$f_debate_order = Input::Get('f_debate_order', 'string', 'bynumber');

$parents = Debate::getDebates(array('language_id' => $f_language_selected, 'parent_debate_nr' => 0), null, $f_debate_offset, $f_debate_limit, $f_debate_order);
$debates = array();
// add the copys
foreach ($parents as $debate) {
    $debates[] = $debate;
    $copys = Debate::getDebates(array('language_id' => $debate->getLanguageId(), 'parent_debate_nr' => $debate->getNumber()));
    foreach ($copys as $debate) {
        $debates[] = $debate;
    }
}


$pager = new SimplePager(Debate::countDebates(), $f_debate_limit, "f_debate_offset", "index.php?f_debate_order=$f_debate_order&amp;", false);
$allLanguages = Language::GetLanguages();

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

echo camp_html_breadcrumbs(array(
    array($translator->trans('Plugins', array(), 'plugin_debate'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
    array($translator->trans('Debates', array(), 'plugin_debate'), ''),
));

// DO NOT DELETE!!! Needed for localizer
// $translator->trans("Debates");
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/campsite-checkbox.js"></script>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
    <TD><A HREF="edit.php" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
    <TD><A HREF="edit.php" ><B><?php  echo $translator->trans("Add new Debate", array(), 'plugin_debate'); ?></B></A></TD>
</tr>
</TABLE>
<p>

<FORM name="debate_list" action="action.php" method="POST">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE CELLSPACING="0" CELLPADDING="0" class="table_actions">
<TR>
    <TD>
        <TABLE cellpadding="0" cellspacing="0">
        <TR>
            <TD ALIGN="left">
                <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" >
                <TR>
                    <TD><?php  echo $translator->trans('Language'); ?>:</TD>
                    <TD valign="middle">
                        <SELECT NAME="f_language_selected" id="f_language_selected" class="input_select" onchange="location.href='index.php?f_language_selected='+document.getElementById('f_language_selected').options[document.getElementById('f_language_selected').selectedIndex].value;">
                        <option value="0"><?php echo $translator->trans("All"); ?></option>
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
                    checkboxes = document.forms.debate_list["f_debate_code[]"];
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
                            alert("<?php echo $translator->trans("You must select at least one debate to perform an action.", array(), 'plugin_debate'); ?>");
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
                        ok = confirm("<?php echo $translator->trans("Are you sure you want to delete the selected debatess?", array(), 'plugin_debate'); ?>");
                        if (!ok) {
                            dropdownElement.options[0].selected = true;
                            return;
                        }
                    }

                    // if the user has selected the "reset" option
                    if (dropdownElement.selectedIndex == resetOptionIndex) {
                        ok = confirm("<?php echo $translator->trans("Are you sure you want to reset counters on the selected debatess?", array(), 'plugin_debate'); ?>");
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
                <SELECT name="f_debate_list_action" class="input_select" onchange="action_selected(this);">
                    <OPTION value=""><?php echo $translator->trans("Actions"); ?>...</OPTION>
                    <OPTION value="delete"><?php echo $translator->trans("Delete"); ?></OPTION>
                    <OPTION value="reset"><?php echo $translator->trans("Reset"); ?></OPTION>
                </SELECT>
            </TD>

            <TD style="padding-left: 5px; font-weight: bold;">
                <input type="button" class="button" value="<?php echo $translator->trans("Select All", array(), 'plugin_debate'); ?>" onclick="checkAll(<?php p(count($debates)); ?>);">
                <input type="button" class="button" value="<?php echo $translator->trans("Select None", array(), 'plugin_debate'); ?>" onclick="uncheckAll(<?php p(count($debates)); ?>);">
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

if (count($debates)) {
    ?>
    <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list" style="padding-top: 5px;" width="95%">
        <TR class="table_list_header">
            <TD width="10">&nbsp;</TD>
            <TD ALIGN="LEFT" VALIGN="TOP" width="800">
                <A href="index.php?f_debate_offset=<?php echo $f_debate_offset ?>&amp;f_debate_order=byname"><?php  echo $translator->trans("Name"); ?></a>
                &nbsp;<SMALL>(<?php echo $translator->trans('click to edit', array(), 'plugin_debate'); ?></SMALL>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="30">
                <A href="index.php?f_debate_offset=<?php echo $f_debate_offset ?>&amp;f_debate_order=bybegin"><?php  echo $translator->trans("Begin", array(), 'plugin_debate'); ?></a>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="30">
                <A href="index.php?f_debate_offset=<?php echo $f_debate_offset ?>&amp;f_debate_order=byend"><?php  echo $translator->trans("End", array(), 'plugin_debate'); ?></a>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="20">&nbsp;</TD>
            <TD ALIGN="center" VALIGN="TOP" width="20">&nbsp;</TD>
            <TD align="center" valign="top" width="20">&nbsp;</TD>
            <TD align="center" valign="top" width="20">&nbsp;</TD>
        </TR>
        <?php

        $used = array();

        foreach ($debates as $debate) {
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
                    <input type="checkbox" value="<?php p((int)$debate->getNumber().'_'.(int)$debate->getLanguageId()); ?>" name="f_debate_code[]" id="checkbox_<?php p($counter); ?>" class="input_checkbox" onclick="checkboxClick(this, <?php p($counter); ?>);">
                </TD>

                <td>
                    <?php
                    if (!array_key_exists($debate->getNumber(), $used) && $debate->getProperty('parent_debate_nr') == 0) {
                        p($debate->getNumber().'.');
                        $used[$debate->getNumber()] = true;
                    } elseif ($debate->getProperty('parent_debate_nr')) {
                        p('\'-> '.$debate->getNumber().'.');
                    } else {
                        p('&nbsp;&nbsp;');
                    }
                    ?>
                    <a href="edit.php?f_debate_nr=<?php p($debate->getNumber()); ?>&amp;f_fk_language_id=<?php p($debate->getLanguageId()); ?>">
                        <?php echo htmlspecialchars($debate->getProperty('title')); ?>
                    </a>
                    &nbsp; (<?php echo htmlspecialchars($debate->getLanguageName()); ?>)
                </td>

                <td align="center"><?php p($debate->getProperty('date_begin')); ?></td>
                <td align="center"><?php p($debate->getProperty('date_end')); ?></td>

                <td align='center'>
                <?php if (!$debate->getProperty('parent_debate_nr')) { ?>
                    <a href="translate.php?f_debate_nr=<?php p($debate->getNumber()); ?>&f_fk_language_id=<?php p($debate->getLanguageId()) ?>" title="<?php echo $translator->trans('Translate') ?>">
                        <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/translate.png" BORDER="0">
                    </a>
                <?php } ?>
                </td>

                <td align='center'>
                <?php if ($debate->isExtended()) { ?>
                    <a href="copy.php?f_debate_nr=<?php p($debate->getNumber()); ?>&f_fk_language_id=<?php p($debate->getLanguageId()) ?>" title="<?php echo $translator->trans('Copy', array(), 'plugin_debate') ?>">
                        <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/duplicate.png" BORDER="0">
                    </a>
                <?php } ?>
                </td>

                <td align='center'>
                    <a href="result.php?f_debate_nr=<?php p($debate->getNumber()); ?>&f_fk_language_id=<?php p($debate->getLanguageId()); ?>" title="<?php echo $translator->trans('Result', array(), 'plugin_debate') ?>">
                        <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/preview.png" BORDER="0">
                    </a>
                </td>

                <td align='center'>
                    <a href="javascript: if (confirm('<?php echo camp_javascriptspecialchars($translator->trans('Are you sure you want to delete the debate $1?', array('$1' => $debate->getProperty('title')), 'plugin_debate')); ?>')) location.href='do_delete.php?f_debate_nr=<?php p($debate->getNumber()); ?>&amp;f_fk_language_id=<?php p($debate->getLanguageId()); ?>&amp;<?php echo SecurityToken::URLParameter(); ?>'">
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
    <LI><?php  echo $translator->trans('No debates.', array(), 'plugin_debate'); ?></LI>
    </BLOCKQUOTE>
    <?php
}
?>
