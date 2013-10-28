<?php
$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('plugin_debate_admin')) {
    camp_html_display_error($translator->trans('You do not have the right to manage debates.', array(), 'plugin_debate'));
    exit;
}
if (!$g_user->hasPermission('DeleteFile')) {
	camp_html_display_error($translator->trans('You do not have the right to delete files.' , array(), 'article_files'), null, true);
	exit;
}
$f_debate_nr = Input::Get('f_debate_nr', 'int', 0);
$f_debateanswer_nr = Input::Get('f_debateanswer_nr', 'int', 0);
$f_fk_language_id = Input::Get('f_fk_language_id', 'int', 0);
$f_attachment_id = Input::Get('f_attachment_id', 'int', 0);

$attachmentObj = new Attachment($f_attachment_id);
if (!$attachmentObj->exists()) {
	camp_html_display_error($translator->trans('Attachment does not exist.', array(), 'article_files'), null, true);
	exit;
}
$filePath = dirname($attachmentObj->getStorageLocation()) . '/' . $attachmentObj->getFileName();
if (!is_writable(dirname($filePath))) {
	camp_html_add_msg(camp_get_error_message(CAMP_ERROR_DELETE_FILE, $filePath,
			basename($attachmentObj->getStorageLocation())));
	//camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'edit.php'));
	exit;
}

$DebateAnswerAttachment = new DebateAnswerAttachment($f_debate_nr, $f_debateanswer_nr, $f_attachment_id);
$DebateAnswerAttachment->delete();

// Go back to upload screen.
camp_html_add_msg($translator->trans("File $1 deleted.", array('$1' => $attachmentObj->getFileName()), 'plugin_debate'), "ok");

$attachmentObj->delete();
?>
<script>
location.href="popup.php?f_debate_nr=<?php p($f_debate_nr) ?>&f_debateanswer_nr=<?php p($f_debateanswer_nr) ?>&f_fk_language_id=<?php p($f_fk_language_id) ?>";
</script>