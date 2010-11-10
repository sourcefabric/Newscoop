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

    params = [];
    if (action == 'delete') {
        if (!confirm('<?php putGS('Are you sure you want to delete selected articles?'); ?>')) {
            return;
        }
    } else if (action == 'move' || action == 'duplicate_interactive') {
        params = {
            'f_publication_id': <?php echo (int) $this->publication; ?>,
            'f_issue_number': <?php echo (int) $this->issue; ?>,
            'f_section_number': <?php echo (int) $this->section; ?>,
            'f_language_id': <?php echo (int) $this->language; ?>,
        }
    }

    callServer(['ArticleList', 'doAction'], [
        action,
        items,
        params,
        ], function(data) {
            if (action == 'duplicate_interactive' || action == 'move') {
                window.location = data; // redirect
            }
            flashMessage('<?php putGS('Articles updated.'); ?>');
            var smartlistId = smartlist.attr('id').split('-')[1];
            tables[smartlistId].fnDraw(true);
        }
    );
});

});

</script>
<?php } ?>
