<form id="comment-add" action="../comment/add-to-article/format/json" method="POST">
    <?php echo SecurityToken::FormParameter(); ?>
    <input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
    <input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
    <input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
    <h3><?php putGS('Post a Comment'); ?></h3>
    <fieldset class="plain">
    <ul>
      <li>
        <label><?php putGS('Author'); ?></label>
        <input id="commenter_name" type="text" name="f_comment_nickname" value="<?php p($g_user->getRealName()); ?>" class="input_text" size="41" />
      </li>
      <li>
        <label><?php putGS('Subject'); ?></label>
        <input id="comment_subject" type="text" name="f_comment_subject" value="" class="input_text" size="41" spellcheck="false" <?php print $spellcheck ?> />
      </li>
      <li>
        <label><?php putGS('Comment'); ?></label>
        <textarea id="comment_message" name="f_comment_body" class="input_text_area" rows="8" cols="" <?php print $spellcheck ?> style="width:99.5%;"></textarea>
      </li>
      <li><input type="submit" value="<?php putGS('Submit'); ?>" class="default-button" /></li>
    </ul>
    </fieldset>
</form>
<script>
function addComment() {
    $('#comment-add').submit(function(){
        $.ajax({
            type: 'POST',
            url: '../comment/add-to-article/format/json',
            data: {
                "article": "<?php echo $f_article_number; ?>",
                "language": "<?php echo $f_language_selected; ?>",
                "name": $('#commenter_name').val(),
                "subject": $('#comment_subject').val(),
                "message" :$('#comment_message').val(),
                <?php echo SecurityToken::JsParameter();?>,
            },
            success: function(data) {
                $('#comment-add').each(function(){
                    this.reset();
                });
                loadComments();
            }
        });
        return false;
    });
}
</script>
<script>
$(function() {
    addComment();
});
</script>