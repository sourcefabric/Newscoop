<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
$translator = \Zend_Registry::get('container')->getService('translator');

$f_language_id = Input::Get('f_language_id', 'int', 0, true);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_comment_id = Input::Get('f_comment_id');

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $BackLink);
	exit;
}

if ($g_user->hasPermission('EditorSpellcheckerEnabled')) {
    $spellcheck = 'spellcheck="true"';
} else {
    $spellcheck = 'spellcheck="false"';
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
camp_html_content_top($translator->trans("Reply to comment", array(), 'article_comments'), $topArray);
?>
<table cellpadding="1" cellspacing="0" class="action_buttons" style="padding-top: 10px;">
<tr>
  <td><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" border="0" /></td>
  <td><a href="<?php echo camp_html_article_url($articleObj, $f_language_id, "edit.php"); ?>"><b><?php echo $translator->trans('Back to Edit Article'); ?></b></a></td>
</tr>
</table>
<p>
<table id="comment-reply-to" cellspacing="0" cellpadding="0" border="0" class="box_table" style="display:none;">
<tr>
  <td colspan="2" style="padding-top: 5px; padding-bottom: 5px; border-bottom: 1px solid black;"">
    &nbsp;<b><?php echo $translator->trans('Comment'); ?></b>
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
  <td align="right" valign="top" nowrap><?php echo $translator->trans('From', array(), 'article_comments'); ?>:</td>
  <td>${name} &lt;${email}&gt; (${ip})</td>
</tr>
<tr>
  <td align="right" valign="top" nowrap><?php echo $translator->trans('Date'); ?>:</td>
  <td>${time_created}</td>
</tr>
<tr>
  <td align="right" valign="top" nowrap><?php echo $translator->trans('Subject'); ?>:</td>
  <td>${subject}</td>
</tr>
<tr>
  <td align="right" valign="top" nowrap><?php echo $translator->trans('Comment'); ?>:</td>
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
    <b><?php echo $translator->trans('Reply to comment', array(), 'article_comments'); ?></b>
    <hr noshade size="1" color="black" />
  </td>
</tr>
<tr>
  <td valign="middle" align="right">
    <?php echo $translator->trans('Subject'); ?>:
  </td>
  <td>
    <input id="comment_subject" type="text" name="f_comment_subject" value="" class="input_text" size="41" <?php print $spellcheck ?> >
  </td>
</tr>
<tr>
  <td valign="top" align="right" style="padding-top: 5px;">
    <?php echo $translator->trans('Comment'); ?>:
  </td>
  <td>
    <textarea id="comment_message" name="f_comment_body" class="input_text_area" rows="10" cols="60" <?php print $spellcheck ?>></textarea>
  </td>
</tr>
<tr>
  <td colspan="2" align="center">
    <input type="submit" value="<?php echo $translator->trans('Submit'); ?>" class="button">
  </td>
</tr>
</table>
</form>
<script>
function replyComment() {
	$('#comment-reply').submit(function(){

		var call_data = {
			"article": "<?php echo $f_article_number; ?>",
			"language": "<?php echo $f_language_selected; ?>",
			"parent": "<?php echo $f_comment_id; ?>",
			"subject": $('#comment_subject').val(),
			"message": $('#comment_message').val(),
                        "<?php echo SecurityToken::KeyParameter();?>": "<?php echo SecurityToken::ValueParameter();?>"
		};

	    var call_url = '../../comment/reply/format/json';

		var res_handle = function(data) {
			window.location.href = "<?php echo camp_html_article_url($articleObj, $f_language_id, "edit.php"); ?>";
		};

		callServer(call_url, call_data, res_handle, true);

        return false;

	});
};
function loadComment() {

	var call_data = {
		"comment": "<?php echo $f_comment_id; ?>"
	};

    var call_url = '../../comment/list/format/json';

	var res_handle = function(data) {
		template = $('#comment-reply-to').html();
		comment = data.result[0];
		if(comment)
			for(key in comment) {
				template = template.replace(new RegExp("\\$({|%7B)"+key+"(}|%7D)","g"),comment[key]);
			}
		$('#comment-reply-to').html(template).show();
	};

	callServer(call_url, call_data, res_handle, true);
};
</script>
<script>
$(function() {
    loadComment();
    replyComment();
});
</script>
<?php } // if comment enabled ?>

<?php camp_html_copyright_notice(); ?>