<?php
$translator = \Zend_Registry::get('container')->getService('translator');
if (!$g_user->hasPermission('plugin_debate_admin')) {
    camp_html_display_error($translator->trans('You do not have the right to manage debates.', array(), 'plugin_debate'));
    exit;
}
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/campsite-checkbox.js"></script>
<script language="javascript">
function debate_assign(debate_nr, action)
{
    $('#f_debate_nr').val(debate_nr);
    $('#f_action').val(action);

    $.get('<?php echo $Campsite['WEBSITE_URL']; ?>/admin/debate/ajax_do_assign.php',
        $('form#assignments').serialize(),
        function(response) {
            debate_switch(response);
        });
}

function debate_switch(response)
{
    var img;
    var funct;
    var debate_nr;

    eval(response);

    switch (action) {
        case 'assign':
            img = '<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" alt="" />';
            funct = 'debate_assign('+debate_nr+', \'unassign\')';
        break;

        case 'unassign':
            img = '<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" alt="" />';
            funct = 'debate_assign('+debate_nr+', \'assign\')';
        break;
    }

    $('#debate_'+debate_nr).html('<a href="javascript: '+funct+'; ">'+img+'</a>');

    // set fancybox to reload
    try {
        parent.$.fancybox.reload = true;
    } catch (e) {}
}

function debate_popup_close()
{
    var target = '<?php p($f_debate_item) ?>';

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
    <INPUT type="hidden" name="f_debate_nr" id='f_debate_nr'>
    <INPUT type="hidden" name="f_action" id='f_action'>
    <INPUT type="hidden" name="f_debate_item" value="<?php echo $f_debate_item; ?>">
    <INPUT TYPE="hidden" name="f_language_id" VALUE="<?php echo $f_language_id; ?>">
    <INPUT TYPE="hidden" name="f_publication_id" VALUE="<?php echo $f_publication_id; ?>">
    <INPUT TYPE="hidden" name="f_issue_nr" VALUE="<?php echo $f_issue_nr; ?>">
    <INPUT TYPE="hidden" name="f_section_nr" VALUE="<?php echo $f_section_nr; ?>">
    <INPUT TYPE="hidden" name="f_article_nr" VALUE="<?php echo $f_article_nr; ?>">
</FORM>

<?php
camp_html_display_msgs();

$assigned = array();

switch ($f_debate_item) {
    case 'publication':
        foreach (DebatePublication::GetAssignments(null, $f_publication_id) as $assignObj) {
            $assigned[$assignObj->getDebateNumber()] = true;
        }
    break;

    case 'issue':
        foreach (DebateIssue::GetAssignments(null, $f_language_id, $f_issue_nr, $f_publication_id) as $assignObj) {
            $assigned[$assignObj->getDebateNumber()] = true;
        }
    break;

    case 'section':
        foreach (DebateSection::GetAssignments(null, $f_language_id, $f_section_nr, $f_issue_nr, $f_publication_id) as $assignObj) {
            $assigned[$assignObj->getDebateNumber()] = true;
        }
    break;

    case 'article':
        foreach (DebateArticle::GetAssignments(null, $f_language_id, $f_article_nr) as $assignObj) {
            $assigned[$assignObj->getDebateNumber()] = true;
        }
    break;

    default:
	   camp_html_display_error($translator->trans('Invalid input'), 'javascript: window.close()');
	   exit;
    break;
}

?>
<table style="margin-top: 10px; margin-left: 15px; margin-right: 15px;" cellpadding="0" cellspacing="0">
    <TR>
    	<TD style="padding: 3px; background-color: #EEE; border-top: 1px solid #8baed1; border-left: 1px solid #8baed1; border-bottom: 1px solid #8baed1;">
    		<B><?php echo $translator->trans("Attach Debates", array(), 'plugin_debate'); ?></B>
    	</TD>
    	<TD style="padding: 3px; background-color: #EEE; border-top: 1px solid #8baed1; border-right: 1px solid #8baed1; border-bottom: 1px solid #8baed1;" align="right">
    	   <a href=""><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" border="0"></a>
    	   <a href="assign_popup.php?f_include=edit.php&amp;f_language_id=<?php p($f_language_id) ?>&amp;f_no_menu=1&amp;f_from=<?php p(urlencode($_SERVER['REQUEST_URI'])) ?>"><b><?php echo $translator->trans('Add new Debate');; ?></b></a>
        </TD>
    </TR>

    <?php
    $f_debate_limit = Input::Get('f_debate_limit', 'int', 15);
    $f_debate_offset = Input::Get('f_debate_offset', 'int', 0);
    $f_debate_order = Input::Get('f_debate_order', 'string');
    $f_debate_assigned = Input::Get('f_debate_assigned', 'string');

    $constraints = array(
        'language_id' => $f_language_id,
        'publication_id' => $f_publication_id,
        'issue_nr' => $f_issue_nr,
        'section_nr' => $f_section_nr,
        'article_nr' => $f_article_nr
    );

    $debates = Debate::getDebates($constraints, $f_debate_assigned, $f_debate_offset, $f_debate_limit, $f_debate_order);

    $param_string = http_build_query(array(
        'f_debate_item' => $f_debate_item,
        'f_issue_nr' => $f_issue_nr,
        'f_language_id' => $f_language_id,
        'f_publication_id' => $f_publication_id,
        'f_article_nr' => $f_article_nr,
    ), '', '&amp;');
    $pager_params = "?$param_string&amp;f_debate_order=$f_debate_order&amp;";
    $pager = new SimplePager(Debate::countDebates($f_language_id), $f_debate_limit, "f_debate_offset", $pager_params, false);

    Debate::countDebates($f_language_id)
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

        if (count($debates)) {
            ?>
            <TR class="table_list_header">
                <TD ALIGN="center" VALIGN="TOP" width="20">
                    <A href="?<?php p($param_string) ?>&amp;f_debate_offset=<?php echo $f_debate_offset ?>&amp;f_debate_assigned=<?php p($f_debate_item) ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0"></a>
                </TD>
                <TD ALIGN="LEFT" VALIGN="TOP" width="800">
                    <A href="?<?php p($param_string) ?>&amp;f_debate_offset=<?php echo $f_debate_offset ?>&amp;f_debate_order=byname"><?php  echo $translator->trans("Name", array(), 'plugin_debate'); ?></a>
                    &nbsp;<SMALL>(click to edit)</SMALL>
                </TD>
                <TD ALIGN="center" VALIGN="TOP" width="30">
                    <A href="?<?php p($param_string) ?>&amp;f_debate_offset=<?php echo $f_debate_offset ?>&amp;f_debate_order=bybegin"><?php  echo $translator->trans("Begin", array(), 'plugin_debate'); ?></a>
                </TD>
                <TD ALIGN="center" VALIGN="TOP" width="30">
                    <A href="?<?php p($param_string) ?>&amp;f_debate_offset=<?php echo $f_debate_offset ?>&amp;f_debate_order=byend"><?php  echo $translator->trans("End", array(), 'plugin_debate'); ?></a>
                </TD>
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

                    <td align='center'>
                        <?php
                        if (array_key_exists($debate->getNumber(), $assigned)) {
                            ?>
                            <div id='debate_<?php p($debate->getNumber()); ?>'>
                            <a href="javascript: debate_assign(<?php p($debate->getNumber()); ?>, 'unassign'); ">
                                <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0">
                            </a>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div id='debate_<?php p($debate->getNumber()); ?>'>
                            <a href="javascript: debate_assign(<?php p($debate->getNumber()); ?>, 'assign'); ">
                                <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0">
                            </a>
                            </div>
                            <?php
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        if (!array_key_exists($debate->getNumber(), $used)) {
                            p($debate->getNumber().'.');
                            $used[$debate->getNumber()] = true;
                        } else {
                            p('&nbsp;&nbsp;');
                        }
                        ?>
                        <a href="assign_popup.php?f_include=edit.php&amp;f_debate_nr=<?php p($debate->getNumber()); ?>&amp;f_fk_language_id=<?php p($debate->getLanguageId()); ?>&amp;f_from=<?php p(urlencode($_SERVER['REQUEST_URI'])) ?>">
                            <?php p($debate->getProperty('title')); ?>
                        </a>
                        &nbsp; (<?php p($debate->getLanguageName()); ?>)
                    </td>

                    <td align="center"><?php p($debate->getProperty('date_begin')); ?></td>
                    <td align="center"><?php p($debate->getProperty('date_end')); ?></td>

                </tr>
                <?php
                $counter++;
                }

            } else {?>
                <tr><TD><LI><?php  echo $translator->trans('No Debates available.', array(), 'plugin_debate'); ?></LI></TD></TR>
                <?php
        }
        ?>
        </table>
        </TD>
    </TR>
    <TR>
    	<TD style="padding: 3px; background-color: #EEE; border-bottom: 1px solid #8baed1; border-left: 1px solid #8baed1; border-right: 1px solid #8baed1;" align="center" colspan="2">
    	   <input type="button" class="button" value="<?php echo $translator->trans('Close') ?>" onClick="debate_popup_close()">
        </TD>
    </TR>
</TABLE>

</FORM>
</P>
