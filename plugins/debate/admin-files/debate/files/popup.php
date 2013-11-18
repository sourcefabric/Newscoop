<?PHP
$translator = \Zend_Registry::get('container')->getService('translator');
$preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

// Check permissions
if (!$g_user->hasPermission('plugin_debate_admin')) {
    camp_html_display_error($translator->trans('You do not have the right to manage debates.', array(), 'plugin_debate'));
    exit;
}
if (!$g_user->hasPermission('AddFile')) {
	camp_html_display_error($translator->trans('You do not have the right to add files.', array(), 'article_files'), null, true);
	exit;
}

$f_debate_nr = Input::Get('f_debate_nr', 'int', 0);
$f_debateanswer_nr = Input::Get('f_debateanswer_nr', 'int', 0);
$f_fk_language_id = Input::Get('f_fk_language_id', 'int', 0);


if (camp_convert_bytes($preferencesService->MaxUploadFileSize) == false) {
	camp_html_add_msg($translator->trans("The maximum file upload size was not configured in Newscoop.", array(), 'article_files'));
	camp_html_add_msg($translator->trans("Please make sure you upgraded the database correctly: run $1 in a shell.", array(
			'$1' => $Campsite['BIN_DIR'].'/campsite-create-instance --db_name '.$Campsite['DATABASE_NAME']), 'article_files'));
}

if (!is_writable($Campsite['FILE_DIRECTORY'])) {
	camp_html_add_msg($translator->trans("Unable to add attachment.", array(), 'article_files'));
	camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $Campsite['FILE_DIRECTORY']));
}

$DebateAnswer = new DebateAnswer($f_fk_language_id, $f_debate_nr, $f_debateanswer_nr);

camp_html_display_msgs();
?>
<html>
<head>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<title><?php echo $translator->trans("Attach File to Debate Answer"); ?></title>
	<?php include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php"); ?>
</head>
<body>

<br>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php" ENCTYPE="multipart/form-data" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  echo $translator->trans("Attach File to Debate Answer", array(), 'plugin_debate'); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("File"); ?>:</TD>
	<TD>
		<INPUT TYPE="HIDDEN" NAME="MAX_FILE_SIZE" value="<?php p(intval(camp_convert_bytes($preferencesService->MaxUploadFileSize))); ?>" />
		<INPUT TYPE="FILE" NAME="f_file" SIZE="32" class="input_file" /><BR />
		<?php echo $translator->trans("Maximum Upload Size", array(), 'article_files'); p(" = " . $preferencesService->MaxUploadFileSize); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Description"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_description" VALUE="" SIZE="32" class="input_text" alt="blank" emsg="<?php echo $translator->trans("Please enter a description for the file.", array(), 'article_files'); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="left" colspan="2" style="padding-left: 15px;"><?php  echo $translator->trans("Should this file only be available for this translation, or for all translations?", array(), 'article_files'); ?></TD>
</TR>
<TR>
	<TD colspan="2" class="indent"  style="padding-left: 30px;">
	<INPUT type="radio" name="f_language_specific" value="yes"><?php echo $translator->trans("Only this translation", array(), 'article_files'); ?><br>
	<INPUT type="radio" name="f_language_specific" value="no" checked><?php echo $translator->trans("All translations", array(), 'article_files'); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="left" colspan="2"  style="padding-left: 15px;"><?php  echo $translator->trans("Do you want this file to open in the users browser, or to automatically download?", array(), 'article_files'); ?></TD>
</TR>
<TR>
	<TD colspan="2" style="padding-left: 30px;">
	<INPUT type="radio" name="f_content_disposition" value=""><?php echo $translator->trans("Open in the browser", array(), 'article_files'); ?><br>
	<INPUT type="radio" name="f_content_disposition" value="attachment" checked><?php echo $translator->trans("Automatically download", array(), 'article_files'); ?>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
    <INPUT TYPE="HIDDEN" NAME="f_debate_nr" VALUE="<?php  p($f_debate_nr); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_debateanswer_nr" VALUE="<?php  p($f_debateanswer_nr); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_fk_language_id" VALUE="<?php  p($f_fk_language_id); ?>">
    <INPUT TYPE="HIDDEN" NAME="BackLink" VALUE="<?php  p($_SERVER['REQUEST_URI']); ?>">
<?php if (is_writable($Campsite['FILE_DIRECTORY'])) { ?>
	<INPUT TYPE="submit" NAME="Save" VALUE="<?php  echo $translator->trans('Save'); ?>" class="button">
	&nbsp;&nbsp;
<?php } ?>
	<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  echo $translator->trans('Cancel'); ?>" class="button" onclick="window.close();">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<?php
include ('edit_files_box.php');
?>
</body>
</html>
