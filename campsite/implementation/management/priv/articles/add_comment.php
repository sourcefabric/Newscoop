<p>

<?php
// show all the comments attached to this article
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleComment.php');

$comments = ArticleComment::GetArticleComments($f_article_number, $f_language_id);
if (count($comments) <= 0) {
    echo "<p><b>No comments posted.</b><p>";
}
else {
    ?>
    <table class="table_input" width="900px">
    <tr>
        <td style="padding-top: 5px; padding-bottom: 5px; border-bottom: 1px solid black;/* 2px solid #8EAED7;*/">
            &nbsp;<b><?php putGS("Comments"); ?></b>
       	</td>
    <tr>

    <?php
    foreach ($comments as $comment) {
        ?>
        <tr>
            <td style="padding-left: 15px; padding-right: 20px; padding-bottom: 5px; border-bottom: 2px solid #8EAED7;">
                <table cellspacing="0" cellpadding="3" border="0">
                <tr>
                    <td align="right" valign="top" nowrap><?php putGS("From:"); ?></td>
                    <td><?php p($comment->getAuthor()); ?> &lt;<?php p($comment->getEmail()); ?>&gt; (<?php p($comment->getIpAddress()); ?>)</td>
                </tr>

                <tr>
                    <td align="right" valign="top" nowrap><?php putGS("Date:"); ?></td>
                    <td><?php p(date("Y-m-d H:i:s", $comment->getLastModified())); ?></td>
                </tr>

                <tr>
                    <td align="right" valign="top" nowrap><?php putGS("Subject:"); ?></td>
                    <td><?php p($comment->getSubject()); ?></td>
                </tr>

                <tr>
                    <td align="right" valign="top" nowrap><?php putGS("Comment:"); ?></td>
                    <td><?php p($comment->getBody()); ?></td>
                </tr>
                </table>
            </td>
        </tr>
        <?php
    }
    ?>
    </table>
    <?php
}
// show the "add comment" form
?>
<form action="do_add_comment.php" method="GET">
<input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
<input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
<input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
<table class="table_input">
<tr>
    <td colspan="2">
        <b><?php putGS("Add comment:"); ?></b>
   		<HR NOSHADE SIZE="1" COLOR="BLACK">
    </td>
</tr>

<tr>
    <td valign="middle" align="right">
        <?php putGS("Subject:"); ?>
    </td>

    <td>
        <input type="text" name="f_comment_subject" value="" class="input_text" size="40">
    </td>
</tr>

<tr>
    <td valign="top" align="right" style="padding-top: 5px;">
        <?php putGS("Comment:"); ?>
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
