<p>

<script>
function onCommentAction(p_type, p_commentId)
{
    document.getElementById(p_type+'_'+p_commentId).checked=true;
    document.getElementById('comment_'+p_commentId).className = 'comment_'+p_type;
}
</script>

<form method="GET" action="/<?php p($ADMIN); ?>/articles/comments/do_moderate.php">
<input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
<input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
<input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
<a name="comments"></a>
<table class="table_input" width="900px" style="padding-left: 5px;">
<tr>
    <td style="padding-top: 5px; padding-bottom: 5px; border-bottom: 1px solid #8EAED7;"">
        &nbsp;<b><?php putGS("Comments"); ?></b>
        <?php
        if (SystemPref::Get("UseDBReplication") == 'Y') {
            if ($connectedToOnlineServer) {
        ?>
        &nbsp;[ <span class="success_message">
        <?php
                putGS("You are connected to the Online Server");
            } elseif (isset($connectedToOnlineServer)
                      &&$connectedToOnlineServer == false) {
        ?>
        &nbsp;[ <span class="failure_message">
        <?php
                putGS("Unable to connect to the Online Server");
            }
        ?>
        </span> ]
        <?php
        }
        ?>
   	</td>
<tr>

<?php
if (!is_array($comments) || count($comments) <= 0) {
    ?>
    <tr><td style="padding-left: 15px;"><?php putGS("No comments posted."); ?></td></tr>
    <?php
} else {
    foreach ($comments as $comment) {
        switch ($comment->getStatus()) {
            case PHORUM_STATUS_APPROVED:
                $css = "comment_approved";
                break;
            case PHORUM_STATUS_HIDDEN;
                $css = "comment_inbox";
                break;
            case PHORUM_STATUS_HOLD:
                $css = "comment_hidden";
                break;
        }
        ?>

        <?php if ($g_user->hasPermission("CommentModerate")  && ($f_edit_mode == "edit") )  { ?>
        <tr>
            <td>

            <!-- table for the action controls -->
            <table>
            <tr>

            <td><input type="image" src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" name="save" value="save"></td>

            <?php if ($publicationObj->commentsPublicModerated() || $publicationObj->commentsSubscribersModerated()) {?>
            <td style="padding-left: 10px;">
                <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="inbox" class="input_radio" id="inbox_<?php echo $comment->getMessageId(); ?>" <?php if ($comment->getStatus() == PHORUM_STATUS_HIDDEN) { ?>checked<?php } ?> onchange="onCommentAction('inbox', <?php p($comment->getMessageId()); ?>);">
            </td>

            <td><a href="javascript: void(0);" onclick="onCommentAction('inbox', <?php p($comment->getMessageId()); ?>);"><b><?php putGS("New"); ?></b></a>
            </td>
            <?php } ?>

            <td style="padding-left: 10px;">
                <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="approve" class="input_radio" id="approved_<?php echo $comment->getMessageId(); ?>" <?php if ($comment->getStatus() ==  PHORUM_STATUS_APPROVED) { ?>checked<?php } ?> onchange="onCommentAction('approved', <?php p($comment->getMessageId()); ?>);">
            </td>

            <td><a href="javascript: void(0);" onclick="onCommentAction('approved', <?php p($comment->getMessageId()); ?>);"><b><?php putGS("Approved"); ?></b></a>
            </td>

            <?php if ($comment->getMessageId() != $comment->getThreadId()) { ?>
            <td style="padding-left: 10px;">
                <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="delete" class="input_radio" id="delete_<?php echo $comment->getMessageId(); ?>" onchange="onCommentAction('delete', <?php p($comment->getMessageId()); ?>);">
            </td>

            <td>
                <a href="javascript: void(0);" onclick="onCommentAction('delete', <?php p($comment->getMessageId()); ?>);"><b><?php putGS("Delete"); ?></b></a>
            </td>
 			<?php } ?>

            <td style="padding-left: 10px;">
                <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="hide" class="input_radio" id="hidden_<?php echo $comment->getMessageId(); ?>" <?php if ($comment->getStatus() == PHORUM_STATUS_HOLD) { ?>checked<?php } ?> onchange="onCommentAction('hidden', <?php p($comment->getMessageId()); ?>);">
            </td>

            <td>
                <a href="javascript: void(0);" onclick="onCommentAction('hidden', <?php p($comment->getMessageId()); ?>);"><b><?php putGS("Hidden"); ?></b></a>
            </td>
            </tr>
            </table>
            <!-- END table for the action controls -->

            </td>
        </tr>
        <?php } // if $g_user->hasPermission("CommentModerate") ?>

        <tr>
            <td class="<?php p($css); ?>" style="padding-left: 15px; padding-right: 20px; padding-bottom: 5px; border-bottom: 1px solid #8EAED7;" id="comment_<?php p($comment->getMessageId()); ?>">
                <table cellspacing="0" cellpadding="3" border="0">
                <tr>
                    <td align="right" valign="top" nowrap>
 						<?php putGS("From:"); ?>
                    </td>
                    <td><?php p(htmlspecialchars($comment->getAuthor())); ?> &lt;<?php p(htmlspecialchars($comment->getEmail())); ?>&gt; (<?php p($comment->getIpAddress()); ?>)</td>
                </tr>

                <tr>
                    <td align="right" valign="top" nowrap><?php putGS("Date:"); ?></td>
                    <td><?php p(date("Y-m-d H:i:s", $comment->getCreationDate())); ?></td>
                </tr>

                <tr>
                    <td align="right" valign="top" nowrap><?php putGS("Subject"); ?>:</td>
                    <td><?php p(htmlspecialchars($comment->getSubject())); ?></td>
                </tr>

                <tr>
                    <td align="right" valign="top" nowrap><?php putGS("Comment"); ?>:</td>
                    <td><?php p(htmlspecialchars($comment->getBody())); ?></td>
                </tr>

                <?php if ($comment->getStatus() == PHORUM_STATUS_APPROVED) { ?>
                <tr>
                    <td colspan="2" align="left" valign="top" nowrap><a href="<?php echo camp_html_article_url($articleObj, $f_language_selected, "comments/reply.php", "", "&f_comment_id=".$comment->getMessageId()); ?>"><?php putGS("Reply to comment"); ?></a></td>
                </tr>
                <?php } ?>

                </table>
            </td>
        </tr>
        <?php
    }
}
?>
</table>
</form>
<p>
<?php
// show the "add comment" form
if (!$articleObj->commentsLocked() && ($f_edit_mode == "edit") ) {
?>
<a name="add_comment"></a>
<form action="/<?php p($ADMIN); ?>/articles/comments/do_add_comment.php" method="GET">
<input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
<input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
<input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
<table class="table_input">
<tr>
    <td colspan="2" style="padding-left: 5px;">
        <b><?php putGS("Post a comment"); ?></b>
   		<HR NOSHADE SIZE="1" COLOR="BLACK">
    </td>
</tr>

<tr>
    <td valign="middle" align="right">
        <?php putGS("Author"); ?>:
    </td>

    <td>
        <input type="text" name="f_comment_nickname" value="<?php p($g_user->getRealName()); ?>" class="input_text" size="41">
    </td>
</tr>

<tr>
    <td valign="middle" align="right">
        <?php putGS("Subject"); ?>:
    </td>

    <td>
        <input type="text" name="f_comment_subject" value="" class="input_text" size="41">
    </td>
</tr>

<tr>
    <td valign="top" align="right" style="padding-top: 5px;">
        <?php putGS("Comment"); ?>:
    </td>

    <td>
        <textarea name="f_comment_body" class="input_text" rows="7" cols="60"></textarea>
    </td>
</tr>

<tr>
    <td colspan="2" align="center">
        <input type="submit" value="<?php putGS("Submit"); ?>" class="button">
    </td>
</tr>
</table>
</form>
<?php } ?>