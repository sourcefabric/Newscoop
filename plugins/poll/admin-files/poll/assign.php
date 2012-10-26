<?php
camp_load_translation_strings("plugin_poll");
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/campsite-checkbox.js"></script>
<script language="javascript">
function poll_assign(poll_nr, action)
{
    $('#f_poll_nr').val(poll_nr);
    $('#f_action').val(action);

    $.get('<?php echo $Campsite['WEBSITE_URL']; ?>/admin/poll/ajax_do_assign.php',
        $('form#assignments').serialize(),
        function(response) {
            poll_switch(response);
        });
}

function poll_switch(response)
{
    var img;
    var funct;
    var poll_nr;

    eval(response);

    switch (action) {
        case 'assign':
            img = '<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" alt="" />';
            funct = 'poll_assign('+poll_nr+', \'unassign\')';
        break;

        case 'unassign':
            img = '<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" alt="" />';
            funct = 'poll_assign('+poll_nr+', \'assign\')';
        break;
    }

    $('#poll_'+poll_nr).html('<a href="javascript: '+funct+'; ">'+img+'</a>');

    // set fancybox to reload
    try {
        parent.$.fancybox.reload = true;
    } catch (e) {}
}

function poll_popup_close()
{
    var target = '<?php p($f_poll_item) ?>';

    switch (target) {
        case 'publication':
            window.onunload = new Function('fx',
                "try {window.opener.document.forms[0].onsubmit(); window.opener.document.forms[0].submit();} catch (e) {}");
        break;

        case 'issue':
            window.onunload = new Function('fx',
                "try {window.opener.document.forms['issue_edit'].onsubmit(); window.opener.document.forms['issue_edit'].submit();} catch (e) {}");
        break;
            window.onunload = new Function('fx',
                "try {window.opener.document.forms['issue_edit'].onsubmit(); window.opener.document.forms['issue_edit'].submit();} catch (e) {}");
        case 'section':
            window.onunload = new Function('fx',
                "try {window.opener.document.forms['section_edit'].onsubmit(); window.opener.document.forms['section_edit'].submit();} catch (e) {}");
        break;

        case 'article':
            parent.$.fancybox.close();
        break;
    }

    window.close();
}
</SCRIPT>

<FORM id="assignments">
	<?php echo SecurityToken::FormParameter(); ?>
    <INPUT type="hidden" name="f_poll_nr" id='f_poll_nr'>
    <INPUT type="hidden" name="f_action" id='f_action'>
    <INPUT type="hidden" name="f_poll_item" value="<?php echo $f_poll_item; ?>">
    <INPUT TYPE="hidden" name="f_language_id" VALUE="<?php echo $f_language_id; ?>">
    <INPUT TYPE="hidden" name="f_publication_id" VALUE="<?php echo $f_publication_id; ?>">
    <INPUT TYPE="hidden" name="f_issue_nr" VALUE="<?php echo $f_issue_nr; ?>">
    <INPUT TYPE="hidden" name="f_section_nr" VALUE="<?php echo $f_section_nr; ?>">
    <INPUT TYPE="hidden" name="f_article_nr" VALUE="<?php echo $f_article_nr; ?>">
</FORM>

<?php
camp_html_display_msgs();

$assigned = array();

switch ($f_poll_item) {
    case 'publication':
        foreach (PollPublication::GetAssignments(null, $f_publication_id) as $assignObj) {
            $assigned[$assignObj->getPollNumber()] = true;
        }
    break;

    case 'issue':
        foreach (PollIssue::GetAssignments(null, $f_language_id, $f_issue_nr, $f_publication_id) as $assignObj) {
            $assigned[$assignObj->getPollNumber()] = true;
        }
    break;

    case 'section':
        foreach (PollSection::GetAssignments(null, $f_language_id, $f_section_nr, $f_issue_nr, $f_publication_id) as $assignObj) {
            $assigned[$assignObj->getPollNumber()] = true;
        }
    break;

    case 'article':
        foreach (PollArticle::GetAssignments(null, $f_language_id, $f_article_nr) as $assignObj) {
            $assigned[$assignObj->getPollNumber()] = true;
        }
    break;

    default:
	   camp_html_display_error(getGS('Invalid input'), 'javascript: window.close()');
	   exit;
    break;
}

?>
<table style="margin-top: 10px; margin-left: 15px; margin-right: 15px;" cellpadding="0" cellspacing="0">
    <TR>
    	<TD style="padding: 3px; background-color: #EEE; border-top: 1px solid #8baed1; border-left: 1px solid #8baed1; border-bottom: 1px solid #8baed1;">
    		<B><?php putGS("Attach Polls"); ?></B>
    	</TD>
    	<TD style="padding: 3px; background-color: #EEE; border-top: 1px solid #8baed1; border-right: 1px solid #8baed1; border-bottom: 1px solid #8baed1;" align="right">
    	   <a href=""><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" border="0"></a>
    	   <a href="assign_popup.php?f_include=edit.php&amp;f_language_id=<?php p($f_language_id) ?>&amp;f_no_menu=1&amp;f_from=<?php p(urlencode($_SERVER['REQUEST_URI'])) ?>"><b><?php putGS('Add new Poll');; ?></b></a>
        </TD>
    </TR>

    <?php
    $f_poll_limit = Input::Get('f_poll_limit', 'int', 15);
    $f_poll_offset = Input::Get('f_poll_offset', 'int', 0);
    $f_poll_order = Input::Get('f_poll_order', 'string');
    $f_poll_assigned = Input::Get('f_poll_assigned', 'string');

    $constraints = array(
        'language_id' => $f_language_id,
        'publication_id' => $f_publication_id,
        'issue_nr' => $f_issue_nr,
        'section_nr' => $f_section_nr,
        'article_nr' => $f_article_nr
    );

    $polls = Poll::getPolls($constraints, $f_poll_assigned, $f_poll_offset, $f_poll_limit, $f_poll_order);

    $param_string = "f_poll_item=$f_poll_item&amp;f_issue_nr=$f_issue_nr&amp;f_language_id=$f_language_id&amp;f_publication_id=$f_publication_id";
    $pager_params = "?$param_string&amp;f_poll_order=$f_poll_order&amp;";
    $pager = new SimplePager(Poll::countPolls($f_language_id), $f_poll_limit, "f_poll_offset", $pager_params, false);

    Poll::countPolls($f_language_id)
    ?>

    <tr>
        <td colspan="2" style="padding: 3px; background-color: #EEE; border-left: 1px solid #8baed1; border-right: 1px solid #8baed1;">
           &nbsp;&nbsp;&nbsp; <?php echo $pager->render(); ?>
        </td>
    </tr>

    <tr>
        <td colspan="2" style="padding: 3px; background-color: #EEE; border-left: 1px solid #8baed1; border-right: 1px solid #8baed1;">
        <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list" style="padding-top: 5px;" width="95%">
        <?php
        $counter = 0;
        $color= 0;

        if (count($polls)) {
            ?>
            <TR class="table_list_header">
                <TD ALIGN="center" VALIGN="TOP" width="20">
                    <A href="?<?php p($param_string) ?>&amp;f_poll_offset=<?php echo $f_poll_offset ?>&amp;f_poll_assigned=<?php p($f_poll_item) ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0"></a>
                </TD>
                <TD ALIGN="LEFT" VALIGN="TOP" width="800">
                    <A href="?<?php p($param_string) ?>&amp;f_poll_offset=<?php echo $f_poll_offset ?>&amp;f_poll_order=byname"><?php  putGS("Name"); ?></a>
                    &nbsp;<SMALL>(click to edit)</SMALL>
                </TD>
                <TD ALIGN="center" VALIGN="TOP" width="30">
                    <A href="?<?php p($param_string) ?>&amp;f_poll_offset=<?php echo $f_poll_offset ?>&amp;f_poll_order=bybegin"><?php  putGS("Begin"); ?></a>
                </TD>
                <TD ALIGN="center" VALIGN="TOP" width="30">
                    <A href="?<?php p($param_string) ?>&amp;f_poll_offset=<?php echo $f_poll_offset ?>&amp;f_poll_order=byend"><?php  putGS("End"); ?></a>
                </TD>
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

                    <td align='center'>
                        <?php
                        if (array_key_exists($poll->getNumber(), $assigned)) {
                            ?>
                            <div id='poll_<?php p($poll->getNumber()); ?>'>
                            <a href="javascript: poll_assign(<?php p($poll->getNumber()); ?>, 'unassign'); ">
                                <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0">
                            </a>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div id='poll_<?php p($poll->getNumber()); ?>'>
                            <a href="javascript: poll_assign(<?php p($poll->getNumber()); ?>, 'assign'); ">
                                <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0">
                            </a>
                            </div>
                            <?php
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        if (!array_key_exists($poll->getNumber(), $used)) {
                            p($poll->getNumber().'.');
                            $used[$poll->getNumber()] = true;
                        } else {
                            p('&nbsp;&nbsp;');
                        }
                        ?>
                        <a href="assign_popup.php?f_include=edit.php&amp;f_poll_nr=<?php p($poll->getNumber()); ?>&amp;f_fk_language_id=<?php p($poll->getLanguageId()); ?>&amp;f_from=<?php p(urlencode($_SERVER['REQUEST_URI'])) ?>">
                            <?php p($poll->getProperty('title')); ?>
                        </a>
                        &nbsp; (<?php p($poll->getLanguageName()); ?>)
                    </td>

                    <td align="center"><?php p($poll->getProperty('date_begin')); ?></td>
                    <td align="center"><?php p($poll->getProperty('date_end')); ?></td>

                </tr>
                <?php
                $counter++;
                }

            } else {?>
                <tr><TD><LI><?php  putGS('No Polls available.'); ?></LI></TD></TR>
                <?php
        }
        ?>
        </table>
        </TD>
    </TR>
    <TR>
    	<TD style="padding: 3px; background-color: #EEE; border-bottom: 1px solid #8baed1; border-left: 1px solid #8baed1; border-right: 1px solid #8baed1;" align="center" colspan="2">
    	   <input type="button" class="button" value="<?php putGS('Close') ?>" onClick="poll_popup_close()">
        </TD>
    </TR>
</TABLE>

</FORM>
</P>
