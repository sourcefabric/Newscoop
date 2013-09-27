<?php
$translator = \Zend_Registry::get('container')->getService('translator');
$DebateAnswerAttachments = DebateAnswerAttachment::getDebateAnswerAttachments($f_debate_nr, $f_debateanswer_nr);
?>
<center>
<TABLE width="95%" style="border: 1px solid #EEEEEE;">
<TR>
	<TD>
		<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
		<TR>
			<TD align="left">
			<STRONG><?php echo $translator->trans("Files"); ?></STRONG>
			</TD>
			<?php if ( isset($f_edit_mode) && ($f_edit_mode == "edit") && $g_user->hasPermission('AddFile')) {  ?>
			<TD align="right">
				<IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" border="0">
				<A href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "files/popup.php"); ?>', 'attach_file', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=500, height=400, top=200, left=100');"><?php echo $translator->trans("Attach", array(), 'article_files'); ?></A>
			</TD>
			<?php } ?>
		</TR>
		</TABLE>
	</TD>
</TR>
<?php
foreach ($DebateAnswerAttachments as $DebateAnswerAttachment) {
    $file = $DebateAnswerAttachment->getAttachment();

	$fileEditUrl = "edit.php?f_publication_id="
	            . (isset($f_publication_id)?$f_publication_id:'')
	            . "&f_issue_number=".(isset($f_issue_number)?$f_issue_number:'')
	            . "&f_section_number=".(isset($f_section_number)?$f_section_number:'')
	            . "&f_article_number=".(isset($f_article_number)?$f_article_number:'')
	            . "&f_attachment_id=".$file->getAttachmentId()
	            . "&f_language_id=".(isset($f_language_id)?$f_language_id:'')
	            . "&f_language_selected=".(isset($f_language_selected)?$f_language_selected:'');

	$deleteUrl = "do_del.php?f_debate_nr=$f_debate_nr&amp;f_debateanswer_nr=$f_debateanswer_nr&amp;f_fk_language_id=$f_fk_language_id&amp;f_attachment_id=".$file->getAttachmentId().'&amp;'.SecurityToken::URLParameter();
	$downloadUrl = "/attachment/".basename($file->getStorageLocation())."?g_download=1";
	if (strstr($file->getMimeType(), "image/") && (strstr($_SERVER['HTTP_ACCEPT'], $file->getMimeType()) ||
							(strstr($_SERVER['HTTP_ACCEPT'], "*/*")))) {
	$previewUrl = "/attachment/".basename($file->getStorageLocation())."?g_show_in_browser=1";
	}
?>
<TR>
	<TD align="center" width="100%">
		<TABLE>
		<TR>
			<TD align="center" valign="top">
				<?php if (isset($f_edit_mode) && $f_edit_mode == "edit") { ?><a href="<?php p($fileEditUrl); ?>"><?php } p(wordwrap($file->getFileName(), "25", "<br>", true)); ?>
				<?php if (isset($f_edit_mode) && $f_edit_mode == "edit") { ?></a><?php } ?><br>
				<?php if (isset($f_language_selected)) p(htmlspecialchars($file->getDescription($f_language_selected))); ?>
			</TD>
			<?php if ($g_user->hasPermission('DeleteFile')) { ?>
			<TD>
				<A title="<?php echo $translator->trans("Delete"); ?>" href="<?php p($deleteUrl); ?>" onclick="return confirm('<?php echo $translator->trans("Are you sure you want to remove the file $1 from the debate answer?", array('$1' => camp_javascriptspecialchars($file->getFileName()))); ?>');"><IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/unlink.png" border="0" /></A><BR />
				<?php if (!empty($previewUrl)) { ?>
				<A title="<?php echo $translator->trans("Preview"); ?>" href="javascript: void(0);" onclick="window.open('<?php echo $previewUrl; ?>', 'attach_file', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=500, height=400, top=200, left=100');"><IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/preview-16x16.png" border="0" /></A>
				<?php } ?>
			</TD>
			<?php } ?>
		</TR>
		<TR>
			<TD align="center"><?php p(camp_format_bytes($file->getSizeInBytes())); ?> <A title="<?php echo $translator->trans("Download", array(), 'article_files'); ?>" href="<?php p($downloadUrl); ?>"><IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/download.png" border="0" /></A></TD>
			<TD></TD>
		</TR>
		</TABLE>
	</TD>
</TR>
<?php } ?>
</TABLE>
</center>