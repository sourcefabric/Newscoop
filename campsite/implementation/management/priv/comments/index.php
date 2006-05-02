<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("comments");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/phorum_load.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_forum.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_message.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_user.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleComment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/SimplePager.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

// This can be 'inbox' or 'archive'
$f_comment_screen = camp_session_get('f_comment_screen', 'inbox');
$f_comment_start_inbox = camp_session_get('f_comment_start_inbox', 0);
$f_comment_start_archive = camp_session_get('f_comment_start_archive', 0);
$f_comment_per_page = camp_session_get('f_comment_per_page', 10);
$f_comment_search = camp_session_get('f_comment_search', '');
//if ($f_comment_per_page < 4) {
//    $f_comment_per_page = 4;
//}

$numInbox = ArticleComment::GetComments('unapproved', true);
$numArchive = ArticleComment::GetComments('approved', true);

if ($f_comment_screen == 'inbox') {
    $pager =& new SimplePager($numInbox, $f_comment_per_page, 'f_comment_start_inbox', "/$ADMIN/comments/index.php?");

} elseif ($f_comment_screen == 'archive') {
    $pager =& new SimplePager($numArchive, $f_comment_per_page, 'f_comment_start_archive', "/$ADMIN/comments/index.php?");
}

// This is here again on purpose because sometimes the pager
// must correct this value.
$f_comment_start_inbox = camp_session_get('f_comment_start_inbox', 0);
$f_comment_start_archive = camp_session_get('f_comment_start_archive', 0);

if ($f_comment_screen == 'inbox') {
    $comments = ArticleComment::GetComments('unapproved', false,
            array('LIMIT' => array('START'=> $f_comment_start_inbox,
                                   'MAX_ROWS' => $f_comment_per_page)));
} elseif ($f_comment_screen == 'archive') {
    $comments = ArticleComment::GetComments('approved', false,
            array('LIMIT' => array('START'=> $f_comment_start_archive,
                                   'MAX_ROWS' => $f_comment_per_page)));
}

$crumbs = array();
$crumbs[] = array(getGS("Content"), "");
$crumbs[] = array(getGS("Comments"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite.js"></script>
<table cellpadding="6" cellspacing="0" style="padding-top: 5px; padding-left: 10px;" border="0">
<tr>
    <td style="<?php if ($f_comment_screen == "inbox") { ?>background-color: lightgrey;<?php } ?>">
        <a href="?f_comment_screen=inbox"><b><?php putGS("Inbox"); ?> (<?php p($numInbox); ?>)</b></a>
    </td>

    <td style="<?php if ($f_comment_screen == "archive") { ?>background-color: lightgrey;<?php } ?>">
        <a href="?f_comment_screen=archive"><b><?php putGS("Archive"); ?> (<?php p($numArchive); ?>)</b></a>
    </td>
</tr>
</table>

<?php
$pagerStr = $pager->render();
?>
<table width="100%" style="border-top: 1px solid #777;">
<tr>
    <td style="padding-left: 13px;">
        <table cellpadding="0" cellspacing="0">
        <form method="GET">
        <tr>
            <td <?php if (!empty($pagerStr)) { ?>style="padding-right: 15px;"<?php } ?>>
                <?php echo $pagerStr; ?>
            </td>

            <td <?php if (!empty($pagerStr)) { ?>style="padding-left: 15px; border-left: 1px solid #777;"<?php } ?>>
                <?php putGS("Items per page"); ?>:
            </td>

            <td>
                <input type="text" name="f_comment_per_page" value="<?php p($f_comment_per_page);?>" size="2" maxlength="4" class="input_text">
            </td>

            <td style="padding-left: 15px;">
                <?php putGS("Search"); ?>: <input type="text" name="f_comment_search" value="<?php p($f_comment_search); ?>" size="10"  class="input_text" disabled>
            </td>

            <td style="padding-left: 15px;">
                <input type="submit" value="<?php putGS("Submit"); ?>" class="button">
            </td>

        </tr>
        </form>
        </table>
    </td>
</tr>
</table>

<?php
if (count($comments) == 0) {
    ?>
    <table style="padding-left: 20px; padding-top: 5px; border-top: 1px solid #777;" width="100%">
    <tr><td align="left" valign="top">
    <?php putGS("No comments"); ?>
    </td></tr></table>
    <?php
} else {
?>
<script>
var comment_ids = new Array;

function onCommentAction(p_type, p_commentId)
{
    document.getElementById(p_type+'_'+p_commentId).checked=true;
    document.getElementById('subject_'+p_commentId).className = 'comment_pending_'+p_type;
}
</script>

<!-- main table with date&subject on the left, comment on right -->
<table cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid #777;">
<tr>
    <td width="30%" valign="top" align="left">
        <!-- The column with date&subject -->
        <table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="100%">
            <?php
            foreach ($comments as $commentPack) {
            $comment = $commentPack["comment"];

            switch ($comment->getStatus()) {
                case PHORUM_STATUS_APPROVED:
                    $css = "comment_approved";
                    break;
                case PHORUM_STATUS_HIDDEN:
                    $css = "comment_inbox";
                    break;
                case PHORUM_STATUS_HOLD:
                    $css = "comment_hidden";
                    break;
            }
            ?>
    		<script>
    		comment_ids.push("comment_<?php p($comment->getMessageId()); ?>");
    		</script>

            <tr class="<?php echo $css; ?>" id="subject_<?php p($comment->getMessageId()); ?>">
            <td valign="top" align="left" width="1%" nowrap onclick="HideAll(comment_ids); ShowElement('comment_<?php p($comment->getMessageId()); ?>');"><a href="javascript: void(0);" onclick="HideAll(comment_ids); ShowElement('comment_<?php p($comment->getMessageId()); ?>');"><?php echo date("Y-m-d H:i:s", $comment->getLastModified()); ?></a></td>
            <td valign="top" align="left" onclick="HideAll(comment_ids); ShowElement('comment_<?php p($comment->getMessageId()); ?>');"><a href="javascript: void(0);" onclick="HideAll(comment_ids); ShowElement('comment_<?php p($comment->getMessageId()); ?>');"><?php echo $comment->getSubject(); ?></a></td>
            </tr>
            <?php } ?>
        </table>
    </td>

    <td width="70%" style="border-left: 1px solid #777;">
        <!-- The column where you can edit the comments -->
        <table class="table_input" style="margin-top: 5px; margin-left: 5px;">
        <form action="do_edit.php" method="GET">
        <?php
        $count = 1;
        foreach ($comments as $commentPack) {
            $comment = $commentPack["comment"];
            $article = $commentPack["article"];
        ?>
        <!-- BEGIN table containing comment controls+content -->
        <tr <?php if ($count++ != 1) { ?>style="display: none;"<?php } ?> id="comment_<?php p($comment->getMessageId()); ?>">
        <td>
            <!-- table for the action controls -->
            <table style="border-bottom: 1px solid #8EAED7;" width="100%">
            <tr>

            <td><input type="image" src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" name="save" value="save"></td>

            <td style="padding-left: 10px;">
                <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="inbox" class="input_radio" id="inbox_<?php echo $comment->getMessageId(); ?>" <?php if ($comment->getStatus() == PHORUM_STATUS_HIDDEN) { ?>checked<?php } ?> onchange="onCommentAction('inbox', <?php p($comment->getMessageId()); ?>);">
            </td>

            <td><a href="javascript: void(0);" onclick="onCommentAction('inbox', <?php p($comment->getMessageId()); ?>);"><b><?php putGS("Inbox"); ?></b></a>
            </td>

            <td  style="padding-left: 10px;">
                <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="approve" class="input_radio" id="approved_<?php echo $comment->getMessageId(); ?>" <?php if ($comment->getStatus() ==  PHORUM_STATUS_APPROVED) { ?>checked<?php } ?> onchange="onCommentAction('approved', <?php p($comment->getMessageId()); ?>);">
            </td>

            <td><a href="javascript: void(0);" onclick="onCommentAction('approved', <?php p($comment->getMessageId()); ?>);"><b><?php putGS("Approve"); ?></b></a>
            </td>

            <td style="padding-left: 10px;">
                <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="delete" class="input_radio" id="delete_<?php echo $comment->getMessageId(); ?>" onchange="onCommentAction('delete', <?php p($comment->getMessageId()); ?>);">
            </td>

            <td>
                <a href="javascript: void(0);" onclick="onCommentAction('delete', <?php p($comment->getMessageId()); ?>);"><b><?php putGS("Delete"); ?></b></a>
            </td>

            <td style="padding-left: 10px;">
                <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="hide" class="input_radio" id="hidden_<?php echo $comment->getMessageId(); ?>" <?php if ($comment->getStatus() == PHORUM_STATUS_HOLD) { ?>checked<?php } ?> onchange="onCommentAction('hidden', <?php p($comment->getMessageId()); ?>);">
            </td>

            <td>
                <a href="javascript: void(0);" onclick="onCommentAction('hidden', <?php p($comment->getMessageId()); ?>);"><b><?php putGS("Hide"); ?></b></a>
            </td>
            </tr>
            </table>
            <!-- END table for the action controls -->

            <!-- BEGIN table with comment content -->
            <table BORDER="0" CELLSPACING="1" CELLPADDING="3">
            <TR>
                <td><b><?php putGS("Date"); ?>:</b></td>
                <td><?php p(date("Y-m-d H:i:s", $comment->getLastModified())); ?></td>
            </tr>

            <tr>
                <td><b><?php putGS("Author:"); ?></b></td>
                <td><?php p($comment->getAuthor()); ?></td>
            </tr>

            <tr>
                <td><b><?php putGS("Subject:"); ?></b></td>
                <td><input type="text" name="f_subject_<?php p($comment->getMessageId()); ?>" value="<?php p(htmlspecialchars($comment->getSubject())); ?>" size="30" class="input_text"></td>
            </tr>

            <tr>
                <td><b><?php putGS("Comment:"); ?></b></td>
            </tr>

            <tr>
                <td colspan="2"><textarea name="f_comment_<?php p($comment->getMessageId()); ?>" class="input_text" rows="15" cols="60"><?php echo $comment->getBody(); ?></textarea></td>
            </tr>
            </table>
        </td>
        </tr>
            <!-- END table with comment content -->
            <?php } ?>
        </table>
        <!-- END table containing comment controls+content -->

    </td>
    </tr>
    </table>
    <!-- END table contain left & right pane for editing comments -->
    <?php

} // if there are comments
?>