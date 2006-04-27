<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("comments");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/phorum_load.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_forum.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_message.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_user.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleComment.php');

// Get all pending messages

$inbox = ArticleComment::GetUnapprovedComments();

$crumbs = array();
$crumbs[] = array(getGS("Content"), "");
$crumbs[] = array(getGS("Comments"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<div style="overflow: auto; width: 100%; height: 200px; border:1px solid black;" >
<form action="do_moderate.php" method="GET">
<table BORDER="0" CELLSPACING="1" CELLPADDING="3" style="border-bottom: 3px solid #8EAED7;">
<TR class="table_list_header">
    <td><?php putGS("Date"); ?></td>
    <td><?php putGS("Subject"); ?></td>
    <td><?php putGS("Comment"); ?></td>
    <td><?php putGS("Article"); ?></td>
</tr>

<?php
$color = 0;
foreach ($inbox as $commentPack) {
    $comment = $commentPack["comment"];
    $article = $commentPack["article"];
    if ($color) {
        $cssClass = "list_row_even";
    } else {
        $cssClass = "list_row_odd";
    }
    $color = !$color;
    ?>
    <tr class="<?php echo $cssClass; ?>">
    <td valign="top" align="left"><?php echo date("Y-m-d H:i:s", $comment->getLastModified()); ?></td>
    <td valign="top" align="left"><?php echo $comment->getSubject(); ?></td>
    <td valign="top" align="left"><?php echo $comment->getBody(); ?></td>
    <td><?php echo $article->getName(); ?></td>
    </tr>

    <tr class="<?php echo $cssClass; ?>">

    <td colspan="4" style="/*border-left: 2px solid #8EAED7; border-bottom: 3px solid #8EAED7; border-right: 2px solid #8EAED7;*/ ">
        <table>
        <tr>

        <td><input type="image" src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" name="save" value="save"></td>

        <td style="padding-left: 10px;">
            <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="Inbox" class="input_radio" id="inbox_<?php echo $comment->getMessageId(); ?>" checked>
        </td>

        <td><a href="javascript: void(0);" onclick="document.getElementById('inbox_<?php echo $comment->getMessageId(); ?>').checked=true;"><b><?php putGS("Inbox"); ?></b></a>
        </td>

        <td  style="padding-left: 10px;">
            <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="approve" class="input_radio" id="approve_<?php echo $comment->getMessageId(); ?>" >
        </td>

        <td><a href="javascript: void(0);" onclick="document.getElementById('approve_<?php echo $comment->getMessageId(); ?>').checked=true;"><b><?php putGS("Approve"); ?></b></a>
        </td>

        <td style="padding-left: 10px;">
            <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="delete" class="input_radio" id="delete_<?php echo $comment->getMessageId(); ?>" >
        </td>

        <td>
            <a href="javascript: void(0);" onclick="document.getElementById('delete_<?php echo $comment->getMessageId(); ?>').checked=true;"><b><?php putGS("Delete"); ?></b></a>
        </td>

        <td style="padding-left: 10px;">
            <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="hide" class="input_radio" id="hide_<?php echo $comment->getMessageId(); ?>" >
        </td>

        <td>
            <a href="javascript: void(0);" onclick="document.getElementById('hide_<?php echo $comment->getMessageId(); ?>').checked=true;"><b><?php putGS("Hide"); ?></b></a>
        </td>
    </tr>
    </table>
    </td>
    </tr>
    <?php
}
?>
</table>
</div>
