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
    $translator = \Zend_Registry::get('container')->getService('translator');
?>
<fieldset class="actions">
    <legend><?php echo $translator->trans('Select action', array(), 'library'); ?></legend>
    <select name="action">
        <option value="">---</option>
        <?php if ($g_user->hasPermission('Publish')) { ?>
        <option value="workflow_publish"><?php echo $translator->trans('Status: Publish', array(), 'library'); ?></option>
        <?php } ?>
        <option value="workflow_submit"><?php echo $translator->trans('Status: Submit', array(), 'library'); ?></option>
        <option value="workflow_new"><?php echo $translator->trans('Status: Set New', array(), 'library'); ?></option>
        <option value="switch_onfrontpage"><?php echo $translator->trans("Toggle: On Front Page", array(), 'library'); ?></option>
        <option value="switch_onsectionpage"><?php echo $translator->trans("Toggle: On Section Page", array(), 'library'); ?></option>
        <option value="switch_comments"><?php echo $translator->trans("Toggle: Comments", array(), 'library'); ?></option>
        <?php if ($this->publication > 0 && $this->issue > 0 && $this->section > 0) { ?>
        <option value="publish_schedule"><?php echo $translator->trans('Publish Schedule', array(), 'library'); ?></option>
        <?php } ?>
        <option value="unlock"><?php echo $translator->trans('Unlock'); ?></option>
        <?php if ($g_user->hasPermission('DeleteArticle')) { ?>
        <option value="delete"><?php echo $translator->trans('Delete'); ?></option>
        <?php } ?>
        <option value="duplicate"><?php echo $translator->trans('Duplicate'); ?></option>
        <option value="duplicate_interactive"><?php echo $translator->trans('Duplicate to another section', array(), 'library'); ?></option>
        <option value="move"><?php echo $translator->trans('Move'); ?></option>
    </select>
</fieldset>
</div><!-- /.smartlist-actions -->

<?php if (!self::$renderActions) { ?>
<script type="text/javascript">
$(document).ready(function() {

// check all/none
$('.smartlist thead input:checkbox').change(function() {
    var smartlist = $(this).closest('.smartlist');
    var checked = (typeof $(this).attr("checked") === 'undefined') ? false : true;
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
        flashMessage('<?php echo $translator->trans('Select some article first.', array(), 'library'); ?>', 'error');
        return;
    }

    params = [];
    if (action == 'delete') {
        if (!confirm('<?php echo $translator->trans('Are you sure you want to delete selected articles?', array(), 'library'); ?>')) {
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

            $('.datatable th input:checkbox').attr('checked', false);
        }
    );
});

});

</script>
<?php } ?>
