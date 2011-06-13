<?php
// check permissions
if (!$g_user->hasPermission('CommentModerate')) {
    return;
}
?>

<?php
// add token
echo SecurityToken::FormParameter();

// add hidden inputs
$hiddens = array(
    'f_language_id' => 'language_id',
    'f_article_number' => 'article_id',
    'f_language_selected' => 'language_selected_id',
);
foreach ($hiddens as $name) {
    echo '<input type="hidden" name="', $name;
    echo '" value="', $$name, '" />', "\n";
}
?>
<fieldset id="comment-prototype" class="plain comments-block" style="display:none">
    <?php if ($inEditMode): ?>
    <ul class="action-list clearfix">
      <li>
        <a class="ui-state-default icon-button right-floated" href="javascript:;"><span class="ui-icon ui-icon-disk"></span><?php putGS('Save'); ?></a>
      </li>
      <li>
        <input type="radio" name="comment_action_${id}" value="hidden" class="input_radio" id="hidden_${id}" ${hidden_checked}/>
        <label class="inline-style left-floated" for="hidden_${id}"><?php putGS('Hidden'); ?></label>
      </li>

      <li>
        <input type="radio" name="comment_action_${id}" value="deleted" class="input_radio" id="delete_${id}" ${deleted_checked}/>
        <label class="inline-style left-floated" for="deleted_${id}"><?php putGS('Delete'); ?></label>
      </li>

      <li>
      <input type="radio" name="comment_action_${id}" value="approved" class="input_radio" id="approved_${id}" ${approved_checked}/>
        <label class="inline-style left-floated" for="approved_${id}"><?php putGS('Approved'); ?></label>
      </li>

      <li>
      <input type="radio" name="comment_action_${id}" value="pending" class="input_radio" id="inbox_${id}" ${pending_checked}/>
        <label class="inline-style left-floated" for="inbox_${id}"><?php putGS('New'); ?></label>
      </li>
    </ul>
    <?php endif; //inEditMode?>
    <div class="frame clearfix">
      <dl class="inline-list">
        <dt><?php putGS('From'); ?></dt>
        <dd><a href="mailto:${email}">"${name}" &lt;${email}&gt;</a> (${ip})</dd>
        <dt><?php putGS('Date'); ?></dt>
        <dd>${time_created}</dd>
        <dt><?php putGS('Subject'); ?></dt>
        <dd>${subject}</dd>
        <dt><?php putGS('Comment'); ?></dt>
        <dd>${message}</dd>
        <?php if ($inEditMode): ?>
        <dt>&nbsp;</dt>
        <dd class="buttons">
            <a href="<?php echo camp_html_article_url($articleObj, $f_language_selected, 'comments/reply.php', '', '&f_comment_id=${id}'); ?>" class="ui-state-default text-button clear-margin"><?php putGS('Reply to comment'); ?></a>
        </dd>
        <?php endif; //inEditMode?>
      </dl>
    </div>
</fieldset>
<p style="display:none"><?php putGS('No comments posted.'); ?></p>
<form id="comment-moderate" action="../comment/set-status/format/json" method="POST"></form>
<script>
function toggleCommentStatus() {
    $('#comment-moderate .comments-block').each(function() {
    	var statusClassMap = { 'hidden': 'hide', 'approved': 'approve', 'pending': 'inbox'};
    	var block = $(this);
        var status = $('input:radio:checked', block).val();
        var cclass = 'comment_'+statusClassMap[status];
        var button = $('dd.buttons', block);

        // set class
        $('.frame', block).removeClass('comment_inbox comment_hide comment_approve')
            .addClass(cclass);

        // show/hide button
        button.hide();
        if (status == 'approve') {
            button.show();
        }
    });
    //detach deleted
    $('input[value=deleted]:checked', $('#comment-moderate')).each(function() {
        $(this).closest('fieldset').slideUp(function() {
            $(this).detach();
        });
    });
}
function loadComments() {
    $.ajax({
        type: 'POST',
        url: '../comment/list/format/json',
        data: {
            "article": "<?php echo $articleObj->getArticleNumber(); ?>",
            "language": "<?php echo $f_language_selected; ?>"
        },
        success: function(data) {
            $('#comment-moderate').empty();
        	hasComents = false;
            for(i in data.result) {
                hasComment = true;
                comment = data.result[i];
                if(typeof(comment) == "function")
                    continue;
                template = $('#comment-prototype').html();
                for(key in comment) {
                    if(key == 'status') {
                    	template = template.replace(new RegExp("\\$({|%7B)"+comment[key]+"_checked(}|%7D)","g"),'checked="true"');
                    	template = template.replace(new RegExp("\\${[^_]*_checked}","g"),'');
                    }
                	template = template.replace(new RegExp("\\$({|%7B)"+key+"(}|%7D)","g"),comment[key]);
                }
            	$('#comment-moderate').append('<fieldset class="plain comments-block">'+template+'</fieldset>');
            }
            if(!hasComment)
                $('#no-comments').show();
            toggleCommentStatus();
        }
    });
}
$('.action-list a').live('click',function(){
	var el = $(this).parents('ul').find('input:checked').first();
	$.ajax({
        type: 'POST',
        url: '../comment/set-status/format/json',
        data: {
		   "comment": el.attr('id').match(/\d+/)[0],
		   "status": el.val(),
		   <?php echo SecurityToken::JsParameter();?>,
		},
		success: function(data) {
		    if(data.status != 200) {
		    	flashMessage(data.message);
		    	return;
		    }
            flashMessage('<?php putGS('Comments updated.'); ?>');
            toggleCommentStatus();
		},
        error: function (rq, status, error) {
            if (status == 0 || status == -1) {
                flashMessage('<?php putGS('Unable to reach Campsite. Please check your internet connection.'); ?>', 'error');
            }
        }
	});
});
</script>
<script>
$(function() {
	loadComments();
});
</script>
