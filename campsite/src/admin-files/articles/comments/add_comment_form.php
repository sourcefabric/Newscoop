<?php
// Show the "Add comment" form
if (!$articleObj->commentsLocked() && $inEditMode) {
?>
  <a name="add_comment"></a>
  <form action="/<?php p($ADMIN); ?>/articles/comments/do_add_comment.php" method="GET">
  <?php echo SecurityToken::FormParameter(); ?>
  <input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
  <input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
  <input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
  <h3><?php putGS('Post a Comment'); ?></h3>
  <fieldset class="plain">
    <ul>
      <li>
        <label><?php putGS('Author'); ?></label>
        <input type="text" name="f_comment_nickname" value="<?php p($g_user->getRealName()); ?>" class="input_text" size="41" />
      </li>
      <li>
        <label><?php putGS('Subject'); ?></label>
        <input type="text" name="f_comment_subject" value="" class="input_text" size="41" spellcheck="false" <?php print $spellcheck ?> />
      </li>
      <li>
        <label><?php putGS('Comment'); ?></label>
        <textarea name="f_comment_body" class="input_text_area" rows="8" cols="" <?php print $spellcheck ?> style="width:99.5%;"></textarea>
      </li>
      <li><input type="submit" value="<?php putGS('Submit'); ?>" class="default-button" /></li>
    </ul>
  </fieldset>
  </form>
<?php } ?>