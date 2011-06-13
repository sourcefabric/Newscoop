<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
camp_load_translation_strings("globals");
camp_load_translation_strings("articles");

$f_language_id = Input::Get('f_language_id', 'int', 0, true);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_comment_id = Input::Get('f_comment_id');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;
}

// Check that the article exists.
$articleObj = new Article($f_language_id, $f_article_number);
if (!$articleObj->exists()) {
	exit;
}
if (!$articleObj->commentsEnabled() || $articleObj->commentsLocked())  {
	camp_html_goto_page(camp_html_article_url($articleObj, $f_language_selected, "edit.php"));
}

$publicationObj = new Publication($articleObj->getPublicationId());
$issueObj = new Issue($articleObj->getPublicationId(), $f_language_id, $articleObj->getIssueNumber());
$sectionObj = new Section($articleObj->getPublicationId(), $articleObj->getIssueNumber(), $f_language_id, $articleObj->getSectionNumber());
$languageObj = new Language($articleObj->getLanguageId());

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj,
				  'Section' => $sectionObj, 'Article'=>$articleObj);
camp_html_content_top(getGS("Reply to comment"), $topArray);
?>
<table cellpadding="1" cellspacing="0" class="action_buttons" style="padding-top: 10px;">
<tr>
  <td><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" border="0" /></td>
  <td><a href="<?php echo camp_html_article_url($articleObj, $f_language_id, "edit.php"); ?>"><b><?php putGS('Back to Edit Article'); ?></b></a></td>
</tr>
</table>
<p>
<table id="comment-reply-to" cellspacing="0" cellpadding="0" border="0" class="box_table" style="display:none;">
<tr>
  <td colspan="2" style="padding-top: 5px; padding-bottom: 5px; border-bottom: 1px solid black;"">
    &nbsp;<b><?php putGS('Comment'); ?></b>
  </td>
<tr>
<?php
if (isset($connectedToOnlineServer)
    && $connectedToOnlineServer == false) {
?>
<tr>
  <td><?php camp_html_display_msgs('0.25em', '0.25em'); ?></td>
</tr>
</table>
<?php
} else {
?>
<tr>
  <td align="right" valign="top" nowrap><?php putGS('From'); ?>:</td>
  <td>${name} &lt;${email}&gt; (${ip})</td>
</tr>
<tr>
  <td align="right" valign="top" nowrap><?php putGS('Date'); ?>:</td>
  <td>${time_created}</td>
</tr>
<tr>
  <td align="right" valign="top" nowrap><?php putGS('Subject'); ?>:</td>
  <td>${subject}</td>
</tr>
<tr>
  <td align="right" valign="top" nowrap><?php putGS('Comment'); ?>:</td>
  <td>${message}</td>
</tr>
</table>
<p>
<form id="comment-reply" action="../../comment/reply/format/json" method="POST">
<?php echo SecurityToken::FormParameter(); ?>
<input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
<input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
<input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
<input type="hidden" name="f_comment_id" value="<?php p($f_comment_id); ?>">
<table border="0" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td colspan="2" style="padding-left: 5px;">
    <b><?php putGS('Reply to comment'); ?></b>
    <hr noshade size="1" color="black" />
  </td>
</tr>
<tr>
  <td valign="middle" align="right">
    <?php putGS('Subject'); ?>:
  </td>
  <td>
    <input id="comment_subject" type="text" name="f_comment_subject" value="" class="input_text" size="41" <?php print $spellcheck ?> >
  </td>
</tr>
<tr>
  <td valign="top" align="right" style="padding-top: 5px;">
    <?php putGS('Comment'); ?>:
  </td>
  <td>
    <textarea id="comment_message" name="f_comment_body" class="input_text_area" rows="10" cols="60" <?php print $spellcheck ?>></textarea>
  </td>
</tr>
<tr>
  <td colspan="2" align="center">
    <input type="submit" value="<?php putGS('Submit'); ?>" class="button">
  </td>
</tr>
</table>
</form>
<script>
function replyComment() {
	$('#comment-reply').submit(function(){
        $.ajax({
            type: 'POST',
            url: '../../comment/reply/format/json',
            data: {
                "article": "<?php echo $f_article_number; ?>",
                "language": "<?php echo $f_language_selected; ?>",
                "parent": "<?php echo $f_comment_id; ?>",
                "subject": $('#comment_subject').val(),
                "message": $('#comment_message').val(),
                <?php echo SecurityToken::JsParameter();?>,
            },
            success: function(data) {
                if(data.status != 200) {
                    flashMessage(data.message);
                    return;
                }
            	window.location.href = "<?php echo camp_html_article_url($articleObj, $f_language_id, "edit.php"); ?>";
            },
            error: function (rq, status, error) {
                if (status == 0 || status == -1) {
                    flashMessage('<?php putGS('Unable to reach Campsite. Please check your internet connection.'); ?>', 'error');
                }
            }
        });
        return false;

	});
}
function loadComment() {
    $.ajax({
        type: 'POST',
        url: '../../comment/list/format/json',
        data: {
            "comment": "<?php echo $f_comment_id; ?>",
        },
        success: function(data) {
        	template = $('#comment-reply-to').html();
            comment = data.result[0];
            if(comment)
                for(key in comment) {
                    template = template.replace(new RegExp("\\$({|%7B)"+key+"(}|%7D)","g"),comment[key]);
                }
            $('#comment-reply-to').html(template).show();
        }
    });
}
</script>
<script>
$(function() {
    loadComment();
    replyComment();
});
</script>
<?php } // if comment enabled ?>

<?php camp_html_copyright_notice(); ?>