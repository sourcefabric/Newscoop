<?php
camp_load_translation_strings("article_files");
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
	exit;
}

if (camp_convert_bytes((SystemPref::Get('MaxUploadFileSize'))) == false) {
	camp_html_add_msg(getGS("The maximum file upload size was not configured in Campsite."));
	camp_html_add_msg(getGS("Please make sure you upgraded the database correctly: run $1 in a shell.",
			$Campsite['BIN_DIR'].'/campsite-create-instance --db_name '.$Campsite['DATABASE_NAME']));
}

if (!is_writable($Campsite['FILE_DIRECTORY'])) {
	camp_html_add_msg(getGS("Unable to add attachment."));
	camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $Campsite['FILE_DIRECTORY']));
}

$articleObj = new Article($f_language_selected, $f_article_number);

camp_html_display_msgs();
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Expires" content="now" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />
  <title><?php putGS("Attach File to Article"); ?></title>
  <?php include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php"); ?>
</head>
<body>

<br/ >
<form name="dialog" method="POST" action="/<?php echo $ADMIN; ?>/articles/files/do_add.php" enctype="multipart/form-data" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<table border="0" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td colspan="2">
    <b><?php  putGS("Attach File to Article"); ?></b>
    <hr noshade size="1" color="black" />
  </td>
</tr>
<tr>
  <td align="right"><?php putGS("File"); ?>:</td>
  <td>
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php p(intval(camp_convert_bytes(SystemPref::Get('MaxUploadFileSize')))); ?>" />
    <input type="file" name="f_file" size="32" class="input_file" /><br />
    <?php putGS("Maximum Upload Size"); p(" = " . SystemPref::Get('MaxUploadFileSize')); ?>
  </td>
</tr>
<tr>
  <td align="right"><?php putGS("Description"); ?>:</td>
  <td>
    <input type="text" name="f_description" value="" size="32" class="input_text" alt="blank" emsg="<?php putGS("Please enter a description for the file."); ?>" />
  </td>
</tr>
<tr>
  <td align="left" colspan="2" style="padding-left: 15px;"><?php putGS("Should this file only be available for this translation of the article, or for all translations?"); ?></td>
</tr>
<tr>
  <td colspan="2" class="indent"  style="padding-left: 30px;">
    <input type="radio" name="f_language_specific" value="yes"><?php putGS("Only this translation"); ?><br />
    <input type="radio" name="f_language_specific" value="no" checked /><?php putGS("All translations"); ?>
  </td>
</tr>
<tr>
  <td align="left" colspan="2" style="padding-left: 15px;"><?php putGS("Do you want this file to open in the user's browser, or to automatically download?"); ?></td>
</tr>
<tr>
  <td colspan="2" style="padding-left: 30px;">
    <input type="radio" name="f_content_disposition" value=""><?php putGS("Open in the browser"); ?><br />
    <input type="radio" name="f_content_disposition" value="attachment" checked /><?php putGS("Automatically download"); ?>
  </td>
</tr>
<tr>
  <td colspan="2">
    <div align="center">
      <input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>" />
      <input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>" />
      <input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>" />
      <input type="hidden" name="BackLink" value="<?php  p($_SERVER['REQUEST_URI']); ?>" />
      <?php if (is_writable($Campsite['FILE_DIRECTORY'])) { ?>
      <input type="submit" name="Save" value="<?php  putGS('Save'); ?>" class="button" />
      &nbsp;&nbsp;
      <?php } ?>
      <input type="button" name="Cancel" value="<?php putGS('Cancel'); ?>" class="button" onclick="window.close();" />
    </div>
  </td>
</tr>
</table>
</form>
<p>
</body>
</html>