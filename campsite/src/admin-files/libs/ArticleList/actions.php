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
<div class="actions">
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

<?php if (!self::$renderActions) { ?>
<script type="text/javascript">
$(document).ready(function() {

// check all/none
$('.smartlist thead input:checkbox').change(function() {
    var smartlist = $(this).closest('.smartlist');
    var checked = $(this).attr('checked');
    $('tbody input:checkbox', smartlist).each(function() {
        $(this).attr('checked', checked);
        if (checked) {
            $(this).parents('tr').addClass('selected');
        } else {
            $(this).parents('tr').removeClass('selected');
        }
    });
});

// actions handle
$('.smartlist .actions select').change(function() {
    var smartlist = $(this).closest('.smartlist');
    var action = $(this).val();
    $(this).val('');

    var items = [];
    $('tbody input:checkbox:checked', smartlist).each(function() {
        items.push($(this).attr('name'));
    });

    if (items.length == 0) {
        flashMessage('<?php putGS('Select some article first.'); ?>', 'error');
        return;
    }

    $.getJSON('<?php echo $this->path; ?>/do_action.php', {
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
        var smartlistId = smartlist.attr('id').split('-')[1];
        tables[smartlistId].fnDraw(true);
    });
});

});

</script>
<?php } ?>
