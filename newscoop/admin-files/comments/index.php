<?php
camp_load_translation_strings("comments");
require_once($GLOBALS['g_campsiteDir']."/include/phorum_load.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/DbReplication.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_forum.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_message.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_user.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_ban_item.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleComment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SimplePager.php');

if (!$g_user->hasPermission('CommentModerate')) {
	camp_html_display_error(getGS("You do not have the right to moderate comments." ));
	exit;
}

// This can be 'inbox' or 'archive'
$f_comment_screen = camp_session_get('f_comment_screen', 'inbox');
$f_comment_start_inbox = camp_session_get('f_comment_start_inbox', 0);
$f_comment_start_archive = camp_session_get('f_comment_start_archive', 0);
$f_comment_per_page = camp_session_get('f_comment_per_page', 20);
$f_comment_search = trim(camp_session_get('f_comment_search', ''));
$f_comment_order_by = camp_session_get('f_comment_order_by', 'datestamp');
$f_comment_order_direction = camp_session_get('f_comment_order_direction', 'ASC');
if ($f_comment_per_page < 4) {
    $f_comment_per_page = 4;
}

// Build the links for ordering search results
if ($f_comment_order_direction == 'DESC') {
	$ReverseOrderDirection = "ASC";
	$OrderSign = "<img src=\"".$Campsite["ADMIN_IMAGE_BASE_URL"]."/descending.png\" border=\"0\">";
} else {
	$ReverseOrderDirection = "DESC";
	$OrderSign = "<img src=\"".$Campsite["ADMIN_IMAGE_BASE_URL"]."/ascending.png\" border=\"0\">";
}
$orderDirectionUrl = "/$ADMIN/comments/index.php?f_comment_order_direction=$ReverseOrderDirection";

// We check if a Campsite Online server is being used
if (SystemPref::Get("UseDBReplication") == 'Y') {
    $dbReplicationObj = new DbReplication();
    $connectedToOnlineServer = $dbReplicationObj->connect();
}

$numInbox = 0;
$numArchive = 0;
if (!isset($connectedToOnlineServer)
        || $connectedToOnlineServer == true) {
    $numInbox = ArticleComment::GetComments('unapproved', true, $f_comment_search);
    $numArchive = ArticleComment::GetComments('approved', true, $f_comment_search);

    if ($f_comment_screen == 'inbox') {
        $pager = new SimplePager($numInbox, $f_comment_per_page, 'f_comment_start_inbox', "/$ADMIN/comments/index.php?");
    } elseif ($f_comment_screen == 'archive') {
        $pager = new SimplePager($numArchive, $f_comment_per_page, 'f_comment_start_archive', "/$ADMIN/comments/index.php?");
    }

    // This is here again on purpose because sometimes the pager
    // must correct this value.
    $f_comment_start_inbox = camp_session_get('f_comment_start_inbox', 0);
    $f_comment_start_archive = camp_session_get('f_comment_start_archive', 0);

    if ($f_comment_screen == 'inbox') {
        $comments = ArticleComment::GetComments('unapproved', false,
                                                $f_comment_search,
                                                array('ORDER BY' => array($f_comment_order_by => $f_comment_order_direction),
                                                      'LIMIT' => array('START'=> $f_comment_start_inbox,
                                                                       'MAX_ROWS' => $f_comment_per_page)));
    } elseif ($f_comment_screen == 'archive') {
        $comments = ArticleComment::GetComments('approved', false,
                                                $f_comment_search,
                                                array('ORDER BY' => array($f_comment_order_by => $f_comment_order_direction),
                                                      'LIMIT' => array('START'=> $f_comment_start_archive,
                                                                       'MAX_ROWS' => $f_comment_per_page)));
    }
}

$crumbs = array();
$crumbs[] = array(getGS("Content"), "");
$crumbs[] = array(getGS("Comments"), "");
echo camp_html_content_top(getGS('Comments'), null);

?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/campsite.js"></script>

<?php camp_html_display_msgs("0.25em", "0.25em"); ?>

<table cellpadding="6" cellspacing="0" style="padding-top: 5px;" border="0" width="100%">
<tr>
    <td style="border-bottom: 1px solid #777;">&nbsp;</td>
    <td width="1%" nowrap class="<?php if ($f_comment_screen != "inbox") { ?>tab_inactive<?php } else { ?>tab_active<?php } ?>">
        <a href="?f_comment_screen=inbox" <?php if ($f_comment_screen != "inbox") { ?>style="color: #555;"<?php } ?>><b><?php putGS("New"); ?> (<?php p($numInbox); ?>)</b></a>
    </td>

    <td width="1%" nowrap class="<?php if ($f_comment_screen != "archive") { ?>tab_inactive<?php } else { ?>tab_active<?php } ?>">
        <a href="?f_comment_screen=archive" <?php if ($f_comment_screen != "archive") { ?>style="color: #555;"<?php } ?>><b><?php putGS("Published"); ?> (<?php p($numArchive); ?>)</b></a>
    </td>

    <td width="98%" style="border-bottom: 1px solid #777;">
    <?php
    if (SystemPref::Get("UseDBReplication") == 'Y') {
        if ($connectedToOnlineServer) {
    ?>
        <span class="success_message">
    <?php
            putGS("You are connected to the Online server");
        } elseif (isset($connectedToOnlineServer)
                  &&$connectedToOnlineServer == false) {
    ?>
        <span class="failure_message">
    <?php
            putGS("Unable to connect to the Online Server");
        }
    ?>
    </span>
    <?php
    }
    ?>
        &nbsp;
    </td>
</tr>
</table>

<?php
if (is_object($pager)) {
    $pagerStr = $pager->render();
}
?>
<table width="100%" style="padding-top: 2px;">
<tr>
    <td style="padding-left: 13px;">
    <form action="/<?php echo $ADMIN; ?>/comments/" method="POST">
        <table cellpadding="0" cellspacing="0">
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
                <?php putGS("Search"); ?>: <input type="text" name="f_comment_search" value="<?php p($f_comment_search); ?>" size="15"  class="input_text">
            </td>

            <td style="padding-left: 15px;">
                <select name="f_comment_order_by" class="input_select">
                <?php
                camp_html_select_option("datestamp", $f_comment_order_by, getGS("Date posted"));
                camp_html_select_option("Name", $f_comment_order_by, getGS("Article name"));
                camp_html_select_option("author", $f_comment_order_by, getGS("Author"));
                camp_html_select_option("thread", $f_comment_order_by, getGS("Thread"));
                ?>
                </select>
            </td>

            <td>
               <a href="<?php p($orderDirectionUrl); ?>"><?php p($OrderSign); ?></a>
            </td>
            <td style="padding-left: 15px;">
                <input type="submit" value="<?php putGS("Search"); ?>" class="button">
            </td>

        </tr>
        </table>
        </form>
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
var arrow_ids = new Array;

function onCommentAction(p_type, p_commentId)
{
    document.getElementById(p_type+'_'+p_commentId).checked=true;
    document.getElementById('subject_'+p_commentId).className = 'comment_pending_'+p_type;
}

function onSummaryClick(p_messageId)
{
    HideAll(comment_ids);
    ShowElement('comment_'+p_messageId);
    HideAll(arrow_ids);
    ShowElement('arrow_'+p_messageId);
} // fn onSummaryClick
</script>

<!-- main table with date&subject on the left, comment on right -->
<table cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid #777;">
<tr>
    <td width="500px" valign="top" align="left">
        <!-- The column with date&subject -->
        <table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="100%">
        <tr class="table_list_header">
            <td>&nbsp;</td>
            <td><?php putGS("Date posted"); ?></td>
            <td><?php putGS("Subject"); ?></td>
            <td><?php putGS("Article"); ?></td>
            <td>&nbsp;</td>
        </tr>
            <?php
            $count = 1;
            foreach ($comments as $commentPack) {
            $comment = $commentPack["comment"];
            $article = $commentPack["article"];
            if ($comment->getStatus() == PHORUM_STATUS_HOLD) {
                $css = "comment_hidden";
            } else {
                if ($count%2 == 0) {
                    $css = "list_row_even";
                } else {
                    $css = "list_row_odd";
                }
            }
            ?>
    		<script>
    		comment_ids.push("comment_<?php p($comment->getMessageId()); ?>");
    		arrow_ids.push("arrow_<?php p($comment->getMessageId()); ?>");
    		</script>

            <tr class="<?php echo $css; ?>" id="subject_<?php p($comment->getMessageId()); ?>">
            <td valign="top" align="left" width="1%" nowrap><?php echo $count++; ?></td>
            <td valign="top" align="left" width="1%" nowrap onclick="onSummaryClick(<?php p($comment->getMessageId()); ?>);"><a href="javascript: void(0);" onclick="onSummaryClick(<?php p($comment->getMessageId()); ?>);"><?php echo date("Y-m-d H:i:s", $comment->getCreationDate()); ?></a></td>
            <td valign="top" align="left" onclick="onSummaryClick(<?php p($comment->getMessageId()); ?>);"><a href="javascript: void(0);" onclick="onSummaryClick(<?php p($comment->getMessageId()); ?>);"><?php echo htmlspecialchars($comment->getSubject()); ?></a></td>
            <td valign="top" align="left" onclick="onSummaryClick(<?php p($comment->getMessageId()); ?>);"><a href="javascript: void(0);" onclick="onSummaryClick(<?php p($comment->getMessageId()); ?>);"><?php p(htmlspecialchars($article->getName())); ?></a></td>
            <td width="1%"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/arrow_left.gif" id="arrow_<?php p($comment->getMessageId()); ?>" <?php if ($count != 2) { ?>style="display: none;"<?php } ?>></td>
            </tr>
            <?php } ?>
        </table>
    </td>

    <td style="border-left: 1px solid #777;" valign="top">
        <!-- The column where you can edit the comments -->
        <form action="/<?php echo $ADMIN; ?>/comments/do_edit.php" method="POST">
		<?php echo SecurityToken::FormParameter(); ?>
        <table border="0" cellpadding="0" cellspacing="0" class="box_table">
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
            <table>
            <tr>
            <td style="padding-left: 10px;">
                <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="inbox" class="input_radio" id="inbox_<?php echo $comment->getMessageId(); ?>" <?php if ($comment->getStatus() == PHORUM_STATUS_HOLD) { ?>checked<?php } ?> onchange="onCommentAction('inbox', <?php p($comment->getMessageId()); ?>);">
            </td>

            <td><a href="javascript: void(0);" onclick="onCommentAction('inbox', <?php p($comment->getMessageId()); ?>);"><b><?php putGS("New"); ?></b></a>
            </td>

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
                <input type="radio" name="comment_action_<?php echo $comment->getMessageId(); ?>" value="hide" class="input_radio" id="hidden_<?php echo $comment->getMessageId(); ?>" <?php if ($comment->getStatus() == PHORUM_STATUS_HIDDEN) { ?>checked<?php } ?> onchange="onCommentAction('hidden', <?php p($comment->getMessageId()); ?>);">
            </td>

            <td>
                <a href="javascript: void(0);" onclick="onCommentAction('hidden', <?php p($comment->getMessageId()); ?>);"><b><?php putGS("Hidden"); ?></b></a>
            </td>

            <td style="padding-left: 10px;">
            	<a href="javascript: void(0);" onclick="window.open('/<?php p($ADMIN); ?>/comments/ban.php?f_comment_id=<?php p($comment->getMessageId()); ?>', null, 'resizable=yes, menubar=no, toolbar=no, width=400, height=200, top=200, left=200'); return false;"><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/unlink.png" border="0"></a>
	        </td>

	        <td>
            	<a href="javascript: void(0);" onclick="window.open('/<?php p($ADMIN); ?>/comments/ban.php?f_comment_id=<?php p($comment->getMessageId()); ?>', null, 'resizable=yes, menubar=no, toolbar=no, width=400, height=200, top=200, left=200'); return false;"><b><?php putGS("Ban user"); ?></b></a>
	        </td>

	        <td style="padding-left: 10px;">
	        	<input type="submit" name="save" value="<?php putGS("Save"); ?>" class="button">
	        </td>

            </tr>
            </table>
            <!-- END table for the action controls -->

            <!-- BEGIN table with article content -->
            <table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="100%"  style="border-top: 1px solid #8EAED7;">
            <tr><td style="padding-top: 5px;">
                <b><?php putGS("Article"); ?>:</b>&nbsp; <a href="<?php echo camp_html_article_url($article, $article->getLanguageId(), "edit.php"); ?>"><?php p(htmlspecialchars($article->getName())); ?></a>
            </td></tr>
            <TR id="article_closed_<?php p($comment->getMessageId()); ?>">
                <td valign="middle">
                    <a href="javascript:void(0);" onclick="HideElement('article_closed_<?php p($comment->getMessageId()); ?>'); ShowElement('article_<?php p($comment->getMessageId()); ?>');"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/viewmagplus.png" border="0" align="absmiddle"><b><?php putGS("Show article"); ?></b></a>
                </td>
            </tr>
            <tr style="display: none;" id="article_<?php p($comment->getMessageId()); ?>">
                <td>
                    <a href="javascript:void(0);" onclick="HideElement('article_<?php p($comment->getMessageId()); ?>'); ShowElement('article_closed_<?php p($comment->getMessageId()); ?>');"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/viewmagminus.png" border="0" align="absmiddle"><b><?php putGS("Hide article"); ?></b></a><br>
                    <table bgcolor="#EFEFEF" style="margin: 10px; border: 1px solid #777;" width="100%" cellpadding="0" cellspacing="0">
                    <?php
                        $articleData = $article->getArticleData();
                        // Get article type fields.
                        $dbColumns = $articleData->getUserDefinedColumns(false, true);
                        foreach ($dbColumns as $dbColumn) {
                            ?>
                            <tr>
                                <td valign="top" align="left" style="padding: 5px;"><b><?php echo htmlspecialchars($dbColumn->getDisplayName(0)); ?>:</b> </td>
                            </tr>

                            <tr>
                                <td  style="border-bottom: 1px solid #777; padding: 10px;"><?php print $articleData->getProperty($dbColumn->getName()); ?>
                            </tr>
                            <?php
                        }
                    ?>
                    </table>
                 </td>
            </tr>
            </table>
            <!-- END table with article content -->

            <!-- BEGIN table with comment content -->
            <table BORDER="0" CELLSPACING="1" CELLPADDING="3">
            <TR>
                <td><b><?php putGS("Date"); ?>:</b></td>
                <td><?php p(date("Y-m-d H:i:s", $comment->getCreationDate())); ?></td>
            </tr>

            <tr>
                <td><b><?php putGS("Author"); ?>:</b></td>
                <td><?php p(htmlspecialchars($comment->getAuthor())); ?> &lt;<?php p(htmlspecialchars($comment->getEmail())); ?>&gt; (<?php p($comment->getIpAddress()); ?>)</td>
            </tr>

            <tr>
                <td><b><?php putGS("Subject"); ?>:</b></td>
                <td><input type="text" name="f_subject_<?php p($comment->getMessageId()); ?>" value="<?php p(htmlspecialchars($comment->getSubject())); ?>" size="30" class="input_text"></td>
            </tr>

            <tr>
                <td><b><?php putGS("Comment"); ?>:</b></td>
            </tr>

            <tr>
                <td colspan="2"><textarea name="f_comment_<?php p($comment->getMessageId()); ?>" class="input_textarea" rows="15" cols="70"><?php p(htmlspecialchars($comment->getBody())); ?></textarea></td>
            </tr>
            </table>
        </td>
        </tr>
            <!-- END table with comment content -->
            <?php } ?>
        </table>
        </form>
        <!-- END table containing comment controls+content -->
        <br>
    </td>
    </tr>
    </table>
    <!-- END table contain left & right pane for editing comments -->
<?php
} // if there are comments

camp_html_copyright_notice();
?>
