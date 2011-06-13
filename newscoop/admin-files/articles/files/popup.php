<?php
camp_load_translation_strings("article_files");
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once LIBS_DIR . '/MediaList/MediaList.php';

$inArchive = !empty($_REQUEST['archive']);

if (!$inArchive) {
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

    $articleObj = new Article($f_language_selected, $f_article_number);
}

if (camp_convert_bytes((SystemPref::Get('MaxUploadFileSize'))) == false) {
	camp_html_add_msg(getGS("The maximum file upload size was not configured in Newscoop."));
	camp_html_add_msg(getGS("Please make sure you upgraded the database correctly: run $1 in a shell.",
			$Campsite['BIN_DIR'].'/campsite-create-instance --db_name '.$Campsite['DATABASE_NAME']));
}

if (!is_writable($Campsite['FILE_DIRECTORY'])) {
	camp_html_add_msg(getGS("Unable to add attachment."));
	camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $Campsite['FILE_DIRECTORY']));
}

camp_html_display_msgs();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="expires" content="now" />
    <title><?php putGS("Attach File to Article"); ?></title>

    <?php include dirname(__FILE__) . '/../../html_head.php'; ?>
    <?php include dirname(__FILE__) . '/../../javascript_common.php'; ?>

    <style>
        body, #tabs { background-color: #f5f5f5; }
        #tabs { border: none; }
    </style>

    <script type="text/javascript"><!--
        $(function() {
            $('#tabs').tabs();
        });
    //--></script>
</head>
<body>

<?php if (!$inArchive) { ?>
<div id="tabs">
    <ul>
        <li><a href="#new-file"><?php putGS('Attach new file'); ?></a></li>
        <li><a href="#existing-file"><?php putGS('Attach existing file'); ?></a></li>
    </ul>

    <div id="new-file">
<?php } ?>

<p></p>
<form name="dialog" method="POST" action="/<?php echo $ADMIN; ?>/articles/files/do_add.php?archive=<?php echo (int) $inArchive; ?>" enctype="multipart/form-data" onsubmit="return <?php camp_html_fvalidate(); ?>;">
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

<?php if (!$inArchive) { ?>
</div>
<div id="existing-file">
<form action="/<?php echo $ADMIN; ?>/articles/files/do_add_existing.php" method="POST">

<?php

// add hiddens
echo SecurityToken::formParameter();
foreach (array('f_language_selected', 'f_article_number') as $name) {
    echo '<input type="hidden" name="', $name;
    echo '" value="', $$name, '" />', "\n";
}

// render list
$list = new MediaList;
$list->setColVis(FALSE)
    ->setHidden('content_disposition')
    ->setHidden('inUse')
    ->setClickable(FALSE)
    ->render();

?>

    <div style="margin: 8px 0; text-align:center">
        <input type="submit" class="button" value="<?php putGS('Attach'); ?>" />
    </div>

    </form>
</div>

</div><!-- /#tabs -->
<?php } ?>

</body>
</html>
