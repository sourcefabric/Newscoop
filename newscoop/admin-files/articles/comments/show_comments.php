<?php
// check for comments
$comments = (array) $comments;
if (empty($comments)) {
    echo '<p>', putGS('No comments posted.'), '</p>';
    return;
}

// check permissions
if (!$g_user->hasPermission('CommentModerate')) {
    return;
}
?>

    <form id="comments-moderate" action="/<?php echo $ADMIN; ?>/articles/comments/do_moderate.php" method="POST">
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

<?php foreach ($comments as $comment) { ?>
<fieldset class="plain comments-block">
    <?php if ($inEditMode) { ?>
    <ul class="action-list clearfix">
      <li>
        <a class="ui-state-default icon-button right-floated" href="#"><span class="ui-icon ui-icon-disk"></span><?php putGS('Save'); ?></a>
      </li>
      <li>
        <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="hide" class="input_radio" id="hidden_<?php echo $comment->getMessageId(); ?>" <?php if ($comment->getStatus() == PHORUM_STATUS_HIDDEN) { echo 'checked'; } ?> />
        <label class="inline-style left-floated" for="hidden_<?php echo $comment->getMessageId(); ?>"><?php putGS('Hidden'); ?></label>
      </li>
      <?php if ($comment->getMessageId() != $comment->getThreadId()) { ?>
      <li>
        <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="delete" class="input_radio" id="delete_<?php echo $comment->getMessageId(); ?>" />
        <label class="inline-style left-floated" for="delete_<?php echo $comment->getMessageId(); ?>"><?php putGS('Delete'); ?></label>
      </li>
      <?php } ?>
      <li>
      <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="approve" class="input_radio" id="approved_<?php echo $comment->getMessageId(); ?>" <?php if ($comment->getStatus() == PHORUM_STATUS_APPROVED) { echo 'checked'; } ?> />
        <label class="inline-style left-floated" for="approved_<?php echo $comment->getMessageId(); ?>"><?php putGS('Approved'); ?></label>
      </li>
      <?php if ($publicationObj->commentsPublicModerated() || $publicationObj->commentsSubscribersModerated()) {?>
      <li>
      <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="inbox" class="input_radio" id="inbox_<?php echo $comment->getMessageId(); ?>" <?php if ($comment->getStatus() == PHORUM_STATUS_HOLD) { echo 'checked'; } ?> />
        <label class="inline-style left-floated" for="inbox_<?php echo $comment->getMessageId(); ?>"><?php putGS('New'); ?></label>
      </li>
      <?php } ?>
    </ul>
    <?php } ?>

    <div class="frame clearfix">
      <dl class="inline-list">
        <dt><?php putGS('From'); ?></dt>
        <dd><?php p(htmlspecialchars($comment->getAuthor())); ?> &lt;<?php p(htmlspecialchars($comment->getEmail())); ?>&gt; (<?php p($comment->getIpAddress()); ?>)</dd>

        <dt><?php putGS('Date'); ?></dt>
        <dd><?php p(date('Y-m-d H:i:s', $comment->getCreationDate())); ?></dd>
        <dt><?php putGS('Subject'); ?></dt>
        <dd><?php p(htmlspecialchars($comment->getSubject())); ?></dd>
        <dt><?php putGS('Comment'); ?></dt>
        <dd><?php p(htmlspecialchars($comment->getBody())); ?></dd>

        <?php if ($inEditMode) { ?>
        <dt>&nbsp;</dt>
        <dd class="buttons"><a href="<?php echo camp_html_article_url($articleObj, $f_language_selected, 'comments/reply.php', '', '&f_comment_id='.$comment->getMessageId()); ?>" class="ui-state-default text-button clear-margin"><?php putGS('Reply to comment'); ?></a></dd>
        <?php } ?>
      </dl>
    </div>
</fieldset>
<?php } ?>

</form>

<script type="text/javascript">
$(function() {
    /**
     * Toggles comment status
     */
    var toggleCommentStatus = function() {
        $('#comments-moderate .comments-block').each(function() {
            var block = $(this);
            var status = $('input:radio:checked', block).val();
            var cclass = 'comment_'+status;
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
    };

    // init
    toggleCommentStatus();

    // save via ajax
    $('form#comments-moderate').submit(function() {
        var form = $(this);
        callServer('ping', [], function(json) {
            $.ajax({
                type: 'POST',
                url: form.attr('action')+'?isAjax=1',
                data: form.serialize(),
                success: function(data, status, p) {
                    flashMessage('<?php putGS('Comments updated.'); ?>');

                    toggleCommentStatus();

                    // detach deleted
                    $('input[value=delete]:checked', form).each(function() {
                        $(this).closest('fieldset').slideUp(function() {
                            $(this).detach();
                        });
                    });
                },
                error: function (rq, status, error) {
                    if (status == 0 || status == -1) {
                        flashMessage('<?php putGS('Unable to reach Campsite. Please check your internet connection.'); ?>', 'error');
                    }
                }
            });

        });
        return false;
    });

    // call form save on save button click
    $('.comments-block .action-list a.icon-button').click(function() {
        $(this).closest('form').submit();
        return false;
    });
});
</script>
