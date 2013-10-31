<?php

$translator = \Zend_Registry::get('container')->getService('translator');
// check permissions
if (!$g_user->hasPermission('CommentEnable')) {
    return;
}
?>

<form id="comment-add" action="../comment/add-to-article/format/json" method="POST">
    <?php echo SecurityToken::FormParameter(); ?>
    <input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
    <input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
    <input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
    <h3><?php echo $translator->trans('Post a Comment', array(), 'article_comments'); ?></h3>
    <fieldset class="plain">
    <ul>
      <li>
        <label><?php echo $translator->trans('Author'); ?></label>
        <input id="commenter_name" type="text" name="f_comment_nickname" value="<?php p($g_user->getRealName()); ?>" class="input_text" size="41" />
      </li>
      <li>
        <label><?php echo $translator->trans('Subject'); ?></label>
        <input id="comment_subject" type="text" name="f_comment_subject" value="" class="input_text" size="41" spellcheck="false" <?php print $spellcheck ?> />
      </li>
      <li>
        <label><?php echo $translator->trans('Comment'); ?></label>
        <textarea id="comment_message" name="f_comment_body" class="input_text_area" rows="8" cols="" <?php print $spellcheck ?> style="width:99.5%;"></textarea>
      </li>
      <li><input type="submit" value="<?php echo $translator->trans('Submit'); ?>" class="default-button" /></li>
    </ul>
    </fieldset>
</form>
<script>
function addComment() {
    $('#comment-add').submit(function(){

		var call_data = {
			"article": "<?php echo $f_article_number; ?>",
			"language": "<?php echo $f_language_selected; ?>",
			"name": $('#commenter_name').val(),
			"subject": $('#comment_subject').val(),
			"message" :$('#comment_message').val()
		};

	    var call_url = '../comment/add-to-article/format/json';

		var res_handle = function(data) {

			$('#comment-add').each(function(){
				this.reset();
			});
			loadComments();
			flashMessage('<?php echo $translator->trans('Comment saved.', array(), 'article_comments'); ?>');

		};

		callServer(call_url, call_data, res_handle, true);

        return false;
    });
};
</script>
<script>
$(function() {
    addComment();
});
</script>