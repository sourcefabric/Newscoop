<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */
?>
<div class="smartlist actions">

<fieldset class="actions">
    <legend><?php putGS('Select action'); ?></legend>
    <select name="action">
        <option value="">---</option>
        <option value="workflow_publish"><?php putGS('Status: Publish'); ?></option>
        <option value="workflow_submit"><?php putGS('Status: Submit'); ?></option>
        <option value="workflow_new"><?php putGS('Status: Set New'); ?></option>
        <option value="switch_onfrontpage"><?php putGS("Toggle: 'On Front Page'"); ?></option>
        <option value="switch_onsectionpage"><?php putGS("Toggle: 'On Section Page'"); ?></option>
        <option value="switch_comments"><?php putGS("Toggle: 'Comments'"); ?></option>
        <option value="unlock"><?php putGS('Unlock'); ?></option>
        <option value="delete"><?php putGS('Delete'); ?></option>
        <option value="duplicate"><?php putGS('Duplicate'); ?></option>
        <option value="duplicate_interactive"><?php putGS('Duplicate to another section'); ?></option>
        <option value="move"><?php putGS('Move'); ?></option>
    </select>
</fieldset>

</div><!-- /.smartlist-actions -->

<script type="text/javascript">
$(document).ready(function() {

// datepicker for dates
$('input.date').datepicker({
    dateFormat: 'yy-mm-dd',
});

// actions handle
$('.actions select').change(function() {
    var action = $(this).val();
    $(this).val('');

    var items = [];
    $('table.datatable td input:checkbox:checked').each(function() {
        items.push($(this).attr('name'));
    });

    if (items.length == 0) {
        flashMessage('<?php putGS('Select some article first.'); ?>', 'error');
        return;
    }

    $.getJSON('/<?php echo $this->admin; ?>/smartlist/do_action.php', {
        'action': action,
        'items': items,
        '<?php echo SecurityToken::SECURITY_TOKEN; ?>': '<?php echo SecurityToken::GetToken(); ?>'
    }, function(data, textStatus) {
        if (!data.success) {
            flashMessage('<?php putGS('Error'); ?>: ' + data.message, 'error');
        } else {
            if (items.length > 1) {
                flashMessage('<?php putGS('Articles updated.'); ?>');
            } else {
                flashMessage('<?php putGS('Article updated.'); ?>');
            }
        }
        table.fnDraw(true);
    });
});

}); // document.ready

/**
 * Create alert via JQueryUI
 *
 * @param string message
 * @param bool error
 *
 * @return void
 */
function dialog_alert(message, error)
{
    if (error) {
        title = '<?php putGS('Error'); ?>';
    } else {
        title = '<?php putGS('Info'); ?>';
    }

    $('<div title="' + title + '"><p>' + message + '</p></div>')
        .appendTo('body')
        .dialog({
            modal: true,
            buttons: {
                Ok: function() {
                    $(this).dialog('close');
                }
            }
        });
}

</script>
