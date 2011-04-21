<?php

camp_load_translation_strings("plugin_blog");

// User role depend on path to this file. Tricky: moderator folder is just symlink to admin files!
if (strpos($call_script, '/blog/admin/') !== false && $g_user->hasPermission('plugin_blog_admin')) {
    $is_admin = true;
}
if (strpos($call_script, '/blog/moderator/') !== false && $g_user->hasPermission('plugin_blog_moderator')) {
    $is_moderator = true;
}

// Check permissions
if (!$is_admin && !$is_moderator) {
    camp_html_display_error(getGS('You do not have the right to manage blogs.'));
    exit;
}

$f_length = Input::Get('f_length', 'int', 20);
$f_start = Input::Get('f_start', 'int', 0);
$f_order = Input::Get('f_order', 'string', 'byidentifier');

if ($f_language_id = Input::Get('f_language_id', 'int')) {
    $constraints .= "language_id is $f_language_id ";
}

if ($f_entry_id = Input::Get('f_entry_id', 'int')) {
    $constraints .= "entry_id is $f_entry_id ";
}

if ($f_status = mysql_escape_string(Input::Get('f_status', 'string'))) {
    $constraints .= "status is $f_status ";
}

if ($f_admin_status = mysql_escape_string(Input::Get('f_admin_status', 'string'))) {
    $constraints .= "admin_status is $f_admin_status ";
}

$parameters = array(
    'constraints' => $constraints,
    'length' => $f_length,
    'order' => "$f_order ASC"
);

define('PLUGIN_BLOG_ADMIN_MODE', true);
$self = basename(__FILE__);
$self_params = $self.'?';
if ($f_entry_id) $self_params .= "f_entry_id=$f_entry_id&amp;";
if ($f_start)    $self_params .= "f_start=$f_start&amp;";

$BlogCommentsList = new BlogCommentsList($f_start, $parameters);
$total = $BlogCommentsList->getTotalCount();
$count = $BlogCommentsList->getLength();
$pager = new SimplePager($total, $f_length, "f_start", "index.php?f_order=$f_order&amp;", false);

$TotalList = new BlogCommentsList();
$total = $TotalList->count;

if ($f_entry_id) {
    $BlogEntry = new BlogEntry($f_entry_id);
    $Blog = $BlogEntry->getBlog();
    echo camp_html_breadcrumbs(array(
        array(getGS('Plugins'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
        array(getGS('Blogs'), $Campsite['WEBSITE_URL'] . '/admin/blog/admin/list_blogs.php'),
        array($Blog->getSubject(), 'list_entries.php?f_blog_id=' . $BlogEntry->getProperty('fk_blog_id')),
        array(getGS('List comments') . ': ' . $BlogEntry->getSubject(), ''),
    ));
} else {
    echo camp_html_breadcrumbs(array(
        array(getGS('Plugins'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
        array(getGS('Blogs'), $Campsite['WEBSITE_URL'] . '/admin/blog/admin/list_blogs.php'),
        array(getGS('List all comments'), ''),
    ));
}

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/campsite-checkbox.js"></script>
<script type="text/javascript">
function ajax_action(action)
{
    $('#f_action').val(action);

    // save & reload
    var myAjax = $.get(
        '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/<?php p(dirname($GLOBALS['call_script'])) ?>/ajax_action.php',
        $('#comments_list').serialize(),
        function() {
            window.location.reload();
        });
}
</script>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
  <TR>
    <?php if ($f_entry_id && $is_admin) { ?>
        <TD style="padding-left: 20px;"><A HREF="javascript: void(0);" onclick="window.open('comment_form.php?f_entry_id=<?php echo $f_entry_id ?>', 'edit_comment', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=600, height=420, top=100, left=100');" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
        <TD><A HREF="javascript: void(0);" onclick="window.open('comment_form.php?f_entry_id=<?php echo $f_entry_id ?>', 'edit_comment', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=600, height=420, top=100, left=100');" ><B><?php  putGS("Add new comment"); ?></B></A></TD>
    <?php } ?>
  </tr>
</TABLE>



<p>

<FORM name="selector" method="get">
<input type="hidden" name="f_entry_id" value="<?php p($f_entry_id) ?>">
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
                        foreach (Language::GetLanguages() as $Language) {
                            $languageList[$Language->getLanguageId()] = $Language->getNativeName();
                        }
                        asort($languageList);

                        foreach ($languageList as $k => $v) {
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
                        foreach (array('pending', 'online', 'offline') as $item) {
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
            <TD ALIGN="left">
                <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" >
                <TR>
                    <TD><?php  putGS('Admin Status'); ?>:</TD>
                    <TD valign="middle">
                        <SELECT NAME="f_admin_status" class="input_select" onchange="this.form.submit()">
                        <option value="0"><?php putGS("All"); ?></option>
                        <?php
                        foreach (array('pending', 'online', 'offline') as $item) {
                            echo '<OPTION value="'.$item.'"' ;
                            if ($item == $f_admin_status) {
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
                    checkboxes = document.forms.comments_list["f_comments[]"];
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
                        if (dropdownElement.options[index].value == "comments_delete") {
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
                    <OPTION value="comments_delete"><?php putGS("Delete"); ?></OPTION>
                    <OPTION value="comments_set_online"><?php putGS("Admin Status: Online"); ?></OPTION>
                    <OPTION value="comments_set_offline"><?php putGS("Admin Status: Offline"); ?></OPTION>
                    <OPTION value="comments_set_pending"><?php putGS("Admin Status: Pending"); ?></OPTION>
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

if ($BlogCommentsList->getLength()) {
    ?>

<div class="pager indent">
    <?php echo $pager->render(); ?>
</div>

    <FORM name="comments_list" id="comments_list" action="action.php" method="POST">
	<?php echo SecurityToken::FormParameter(); ?>
    <input type="hidden" name="f_action" id="f_action" />
    <input type="hidden" name="f_new_pos" id="f_new_pos" />

    <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list" style="padding-top: 5px;" width="95%">
        <TR class="table_list_header">

            <?php if($is_admin) { ?>
                <TD width="10">&nbsp;</TD>
            <?php } ?>

            <TD ALIGN="LEFT" VALIGN="TOP" width="500">
                <A href="<?php p($self_params) ?>f_order=byname"><?php  putGS("Comment"); ?></a>
                &nbsp;<SMALL>
                <?php if ($is_admin) putGS('Click to edit'); ?>
                </SMALL>
            </TD>

            <TD align="center" valign="top">
                <A href="<?php p($self_params) ?>f_order=byuser_id"><?php  putGS("User"); ?></a>
            </TD>

            <TD ALIGN="center" VALIGN="TOP" width="60">
                <A href="<?php p($self_params) ?>f_order=bystatus"><?php  putGS("Status"); ?></a>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="60">
                <A href="<?php p($self_params) ?>f_order=byadmin_status"><?php  putGS("Admin Status"); ?></a>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="60">
                <A href="<?php p($self_params) ?>f_order=bydate"><?php  putGS("Published"); ?></a>
            </TD>
            <TD ALIGN="center" VALIGN="TOP" width="60">
                <A href="<?php p($self_params) ?>f_order=byfeature"><?php  putGS("Feature"); ?></a>
            </TD>
            <?php if($is_admin) { ?>
                <TD width="10">&nbsp;</TD>
            <?php } ?>
        </TR>
        <?php

        while ($MetaBlogComment = $BlogCommentsList->current) {
            $BlogCommentsList->defaultIterator()->next();

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
                        <input type="checkbox" value="<?php p((int)$MetaBlogComment->identifier); ?>" name="f_comments[]" id="checkbox_<?php p($counter); ?>" class="input_checkbox" onclick="checkboxClick(this, <?php p($counter); ?>);">
                    </TD>
                <?php } ?>

                <td>
                    <?php
                    p($MetaBlogComment->identifier.'.');

                    if ($is_admin) {
                        ?><a href="javascript: void(0);" onclick="window.open('comment_form.php?f_comment_id=<?php p($MetaBlogComment->identifier); ?>', 'edit_comment', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=600, height=420, top=100, left=100');"><?php p(mb_substr(strip_tags($MetaBlogComment->content), 0, 70)); ?></a><?php
                    } else {
                        p($MetaBlogComment->title);
                    }
                    ?>
                    (<?php p($MetaBlogComment->language->name); ?>)
                </td>

                <td align="center"><?php p($MetaBlogComment->user->name); ?></td>
                <td align="center"><?php putGS($MetaBlogComment->status); ?></td>
                <td align="center"><?php putGS($MetaBlogComment->admin_status); ?></td>
                <td align="center"><?php p($MetaBlogComment->date); ?></td>
                <td align="center"><?php p($MetaBlogComment->feature); ?></td>

                <?php if($is_admin) { ?>
                    <td align='center'>
                        <a href="javascript: if (confirm('<?php putGS('Are you sure you want to delete the selected item(s)?') ?>')) {
                            uncheckAll(<?php p($count); ?>);
                            document.getElementById('checkbox_<?php p($counter); ?>').checked = true;
                            ajax_action('comments_delete')
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

<div class="pager indent">
    <?php echo $pager->render(); ?>
</div>

<?php
} else {?>
    <BLOCKQUOTE>
    <LI><?php  putGS('No comments.'); ?></LI>
    </BLOCKQUOTE>
    <?php
}
?>
