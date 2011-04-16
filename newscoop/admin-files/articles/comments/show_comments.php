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
    'f_language_id',
    'f_article_number',
    'f_language_selected',
);
foreach ($hiddens as $name) {
    echo '<input type="hidden" name="', $name;
    echo '" value="', $$name, '" />', "\n";
}
?>
<p style="display:none"><?php putGS('No comments posted.'); ?></p>
<fieldset id="comment-prototype" class="plain comments-prototype" style="display:none">
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
        <input type="radio" name="comment_action_${id}" value="deleted" class="input_radio" id="delete_${id}"/>
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
            <a href="<?php echo camp_html_article_url($articleObj, $f_language_selected, 'comments/reply.php', '', '&f_comment_id='); ?>" class="ui-state-default text-button clear-margin"><?php putGS('Reply to comment'); ?></a>
        </dd>
        <?php endif; //inEditMode?>
      </dl>
    </div>
</fieldset>
<form id="comments-moderate" action="comment/do_moderate.php" method="POST">
</form>
<script>
function loadComments() {
	$('#comments-moderate').empty();
    $.ajax({
        type: 'POST',
        url: '../comment/list/format/json',
        data: {
            "article": "<?php echo $articleObj->getArticleNumber(); ?>",
            "language": "<?php echo $f_language_selected; ?>"
        },
        success: function(data) {
            hasComents = false;
            for(i in data.result) {
                hasComment = true;
                comment = data.result[i];
                if(typeof(comment) == "function")
                    continue;
                template = $('#comment-prototype').html();
                for(key in comment) {
                    if(key == 'status') {
                    	template = template.replace(new RegExp("\\${"+comment[key]+"_checked}","g"),'checked="true"');
                    	template = template.replace(new RegExp("\\${[^_]*_checked}","g"),'');
                    }
                	template = template.replace(new RegExp("\\${"+key+"}","g"),comment[key]);
                }
            	$('#comments-moderate').append(template);
            }
            if(!hasComment)
                $('#no-comments').show();

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
		   "status": el.val()
		},
		success: function(data) {
		}
	});
});
$(function() {
	loadComments();
});
</script>