<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once LIBS_DIR . '/MediaList/MediaList.php';

$translator = \Zend_Registry::get('container')->getService('translator');
$preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

$inArchive = !empty($_REQUEST['archive']);

if (!$inArchive) {
    $f_publication_id = Input::Get('f_publication_id', 'int', 0);
    $f_issue_number = Input::Get('f_issue_number', 'int', 0);
    $f_section_number = Input::Get('f_section_number', 'int', 0);
    $f_language_id = Input::Get('f_language_id', 'int', 0);
    $f_language_selected = Input::Get('f_language_selected', 'int', 0);
    $f_article_number = Input::Get('f_article_number', 'int', 0);

    if (!Input::IsValid()) {
	    camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $_SERVER['REQUEST_URI'], true);
	    exit;
    }

    $articleObj = new Article($f_language_selected, $f_article_number);
}

if (camp_convert_bytes(($preferencesService->MaxUploadFileSize)) == false) {
	camp_html_add_msg($translator->trans("The maximum file upload size was not configured in Newscoop.", array(), 'article_files'));
	camp_html_add_msg($translator->trans("Please make sure you upgraded the database correctly: run $1 in a shell.", array(
			'$1' => $Campsite['BIN_DIR'].'/campsite-create-instance --db_name '.$Campsite['DATABASE_NAME']), 'article_files'));
}

if (!is_writable($Campsite['FILE_DIRECTORY'])) {
	camp_html_add_msg($translator->trans("Unable to add attachment.", array(), 'article_files'));
	camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $Campsite['FILE_DIRECTORY']));
}

camp_html_display_msgs();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="expires" content="now" />
    <title><?php echo $translator->trans("Attach File to Article", array(), 'article_files'); ?></title>

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
        <li><a href="#new-file"><?php echo $translator->trans('Attach new file', array(), 'article_files'); ?></a></li>
        <li><a href="#existing-file"><?php echo $translator->trans('Attach existing file', array(), 'article_files'); ?></a></li>
    </ul>

    <div id="new-file">
<?php } ?>

<p></p>
<form name="dialog" method="POST" action="/<?php echo $ADMIN; ?>/articles/files/do_add.php?archive=<?php echo (int) $inArchive; ?>" enctype="multipart/form-data" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<table border="0" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td colspan="2">
    <b><?php echo $translator->trans("Attach File to Article", array(), 'article_files'); ?></b>
    <hr noshade size="1" color="black" />
  </td>
</tr>
<tr>
  <td align="right"><?php echo $translator->trans("File"); ?>:</td>
  <td>
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php p(intval(camp_convert_bytes($preferencesService->MaxUploadFileSize))); ?>" />
    <input type="file" name="f_file" size="32" class="input_file" /><br />
    <?php echo $translator->trans("Maximum Upload Size", array(), 'article_files'); p(" = " . $preferencesService->MaxUploadFileSize); ?>
  </td>
</tr>
<tr>
  <td align="right"><?php echo $translator->trans("Description"); ?>:</td>
  <td>
    <input type="text" name="f_description" value="" size="32" class="input_text" alt="blank" emsg="<?php echo $translator->trans("Please enter a description for the file.", array(), 'article_files'); ?>" />
  </td>
</tr>
<tr>
  <td align="left" colspan="2" style="padding-left: 15px;"><?php echo $translator->trans("Should this file only be available for this translation of the article, or for all translations?", array(), 'article_files'); ?></td>
</tr>
<tr>
  <td colspan="2" class="indent"  style="padding-left: 30px;">
    <input type="radio" name="f_language_specific" value="yes"><?php echo $translator->trans("Only this translation", array(), 'article_files'); ?><br />
    <input type="radio" name="f_language_specific" value="no" checked /><?php echo $translator->trans("All translations", array(), 'article_files'); ?>
  </td>
</tr>
<tr>
  <td align="left" colspan="2" style="padding-left: 15px;"><?php echo $translator->trans("Do you want this file to open in the user's browser, or to automatically download?", array(), 'article_files'); ?></td>
</tr>
<tr>
  <td colspan="2" style="padding-left: 30px;">
    <input type="radio" name="f_content_disposition" value=""><?php echo $translator->trans("Open in the browser", array(), 'article_files'); ?><br />
    <input type="radio" name="f_content_disposition" value="attachment" checked /><?php echo $translator->trans("Automatically download", array(), 'article_files'); ?>
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
      <input type="submit" name="Save" value="<?php echo $translator->trans('Save'); ?>" class="button" />
      &nbsp;&nbsp;
      <?php } ?>
      <input type="button" name="Cancel" value="<?php echo $translator->trans('Cancel'); ?>" class="button" onclick="parent.$.fancybox.close();" />
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
        <input type="submit" class="button" value="<?php echo $translator->trans('Attach'); ?>" />
    </div>

    </form>
</div>

</div><!-- /#tabs -->
<?php } ?>

</body>
</html>
