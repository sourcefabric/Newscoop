<script type="text/javascript">
function onCommentAction(p_type, p_commentId)
{
    document.getElementById(p_type+'_'+p_commentId).checked=true;
    document.getElementById('comment_'+p_commentId).className = 'comment_'+p_type;
}
</script>
  <h3><?php putGS('Comments'); ?></h3>
    <?php
    if (!is_array($comments) || count($comments) <= 0) {
    ?>
    <?php putGS('No comments posted.'); ?>
    <?php
    } else {
        foreach ($comments as $comment) {
            switch ($comment->getStatus()) {
            case PHORUM_STATUS_APPROVED:
                $css = 'comment_approved';
                break;
            case PHORUM_STATUS_HIDDEN;
                $css = 'comment_inbox';
                break;
            case PHORUM_STATUS_HOLD:
                $css = 'comment_hidden';
                break;
            }

            if ($g_user->hasPermission('CommentModerate') && $inEditMode)  {
    ?>
  <fieldset class="plain comments-block">
    <ul class="action-list clearfix">
      <li>
        <a class="ui-state-default icon-button right-floated" href="#"><span class="ui-icon ui-icon-disk"></span><?php putGS('Save'); ?></a>
      </li>
      <li>
        <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="hide" class="input_radio" id="hidden_<?php echo $comment->getMessageId(); ?>" <?php if ($comment->getStatus() == PHORUM_STATUS_HOLD) { ?>checked<?php } ?> onchange="onCommentAction('hidden', <?php p($comment->getMessageId()); ?>);" />
        <label class="inline-style left-floated" for="hidden_<?php echo $comment->getMessageId(); ?>"><?php putGS('Hidden'); ?></label>
      </li>
      <?php if ($comment->getMessageId() != $comment->getThreadId()) { ?>
      <li>
        <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="delete" class="input_radio" id="delete_<?php echo $comment->getMessageId(); ?>" onchange="onCommentAction('delete', <?php echo $comment->getMessageId(); ?>);">
        <label class="inline-style left-floated" for="delete_<?php echo $comment->getMessageId(); ?>"><?php putGS('Delete'); ?></label>
      </li>
      <?php } ?>
      <li>
        <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="approve" class="input_radio" id="approved_<?php echo $comment->getMessageId(); ?>" onchange="onCommentAction('approved', <?php echo $comment->getMessageId(); ?>);">
        <label class="inline-style left-floated" for="approved_<?php echo $comment->getMessageId(); ?>"><?php putGS('Approved'); ?></label>
      </li>
      <?php if ($publicationObj->commentsPublicModerated() || $publicationObj->commentsSubscribersModerated()) {?>
      <li>
        <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="inbox" class="input_radio" id="inbox_<?php echo $comment->getMessageId(); ?>" checked onchange="onCommentAction('inbox', <?php echo $comment->getMessageId(); ?>);">
        <label class="inline-style left-floated" for="inbox_<?php echo $comment->getMessageId(); ?>"><?php putGS('New'); ?></label>
      </li>
      <?php } ?>
    </ul>
    <?php
            }
    ?>
    <div class="frame clearfix comment_inbox">
      <dl class="inline-list">
        <dt><?php putGS('From'); ?></dt>
        <dd><?php p(htmlspecialchars($comment->getAuthor())); ?> &lt;<?php p(htmlspecialchars($comment->getEmail())); ?>&gt; (<?php p($comment->getIpAddress()); ?>)</dd>

        <dt><?php putGS('Date'); ?></dt>
        <dd><?php p(date('Y-m-d H:i:s', $comment->getCreationDate())); ?></dd>
        <dt><?php putGS('Subject'); ?></dt>
        <dd><?php p(htmlspecialchars($comment->getSubject())); ?></dd>
        <dt><?php putGS('Comment'); ?></dt>
        <dd><?php p(htmlspecialchars($comment->getBody())); ?></dd>

        <dt>&nbsp;</dt>
        <?php if ($comment->getStatus() == PHORUM_STATUS_APPROVED) { ?>
        <dd class="buttons"><a href="<?php echo camp_html_article_url($articleObj, $f_language_selected, 'comments/reply.php', '', '&f_comment_id='.$comment->getMessageId()); ?>" class="ui-state-default text-button clear-margin"><?php putGS('Reply to comment'); ?></a></dd>
        <?php } ?>
      </dl>
    </div>
  </fieldset>
    <?php
        }
    }
    ?>

