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
<?php
	global $g_user;
?>
<fieldset class="actions">
    <legend><?php putGS('Select action'); ?></legend>
    <select name="action">
        <option value="">---</option>
        <?php if ($g_user->hasPermission('Publish')) { ?>
        <option value="workflow_publish"><?php putGS('Status: Publish'); ?></option>
        <?php } ?>
        <option value="workflow_submit"><?php putGS('Status: Submit'); ?></option>
        <option value="workflow_new"><?php putGS('Status: Set New'); ?></option>
        <option value="switch_onfrontpage"><?php putGS("Toggle: 'On Front Page'"); ?></option>
        <option value="switch_onsectionpage"><?php putGS("Toggle: 'On Section Page'"); ?></option>
        <option value="switch_comments"><?php putGS("Toggle: 'Comments'"); ?></option>
        <?php if ($this->publication > 0 && $this->issue > 0 && $this->section > 0) { ?>
        <option value="publish_schedule"><?php putGS('Publish Schedule'); ?></option>
        <?php } ?>
        <option value="unlock"><?php putGS('Unlock'); ?></option>
        <?php if ($g_user->hasPermission('DeleteArticle')) { ?>
        <option value="delete"><?php putGS('Delete'); ?></option>
        <?php } ?>
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
    } else if (action == 'move'
        || action == 'duplicate_interactive'
        || action == 'publish_schedule') {
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
    		var dataJson = eval('(' + data + ')');
            if (action == 'duplicate_interactive'
                || action == 'move'
                || action == 'publish_schedule') {
                window.location = dataJson.hiperlink; // redirect
            }
			var messages = dataJson.messages;

			var sentFlash = false;
			for(var i=0; i< messages.length; i++) {
				var message = messages[i];
				if(message.no > 0) {
					var flashType = '';
					var textMessage = message.textMessage;
					if(message.status == 'notAffected') {
						flashType = 'error';
					}
					if(sentFlash == true) {
						setTimeout(function() {
							flashMessage(message.textMessage, flashType);
						},3000);
					} else {
						flashMessage(textMessage, flashType);
					}
					sentFlash = true;
				}
			}

            var smartlistId = smartlist.attr('id').split('-')[1];
            tables[smartlistId].fnDraw(true);
        }
    );
});

});

</script>
<?php } ?>
