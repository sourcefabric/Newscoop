<?php
/**
 * Show a list of attachments in a long horizontal table.
 * @author $Author: holman $
 * @version $Id: attachments.php 8002 2009-04-07 11:24:23Z holman $
 */

require_once('config.inc.php');
require_once('classes/AttachmentManager.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/lib_campsite.php");
camp_load_translation_strings("tiny_media_plugin");

$manager = new AttachmentManager($AMConfig);

$languageSelected = (isset($_REQUEST['language_selected'])) ? $_REQUEST['language_selected'] : null;

// Get the list of files and directories
$list = $manager->getFiles($_REQUEST['article_id'], $languageSelected);


/**
 * Draw the files in a table.
 */
function drawFiles($list, &$manager)
{
    global $languageSelected;

    $counter = 0;
    foreach($list as $entry => $file)
    {
        $counter++;
        $languageId = ($file['attachment']->getLanguageId()) ? $file['attachment']->getLanguageId() : $languageSelected;
	$downloadURL = '/attachment/' . basename($file['attachment']->getStorageLocation());
?>
    <td>
      <table width="100" cellpadding="0" cellspacing="0">
      <tr>
        <td class="block" id="block_<?php echo $counter; ?>" onclick="CampsiteMediaDialog.select(<?php echo $file['attachment']->getAttachmentId(); ?>, '<?php echo $downloadURL; ?>', '<?php echo htmlspecialchars($file["attachment"]->getDescription($languageId)); ?>', '<?php echo $counter; ?>');">
          <a href="javascript:;" onclick="CampsiteMediaDialog.select(<?php echo $file['attachment']->getAttachmentId(); ?>, '<?php echo $downloadURL; ?>', '<?php echo htmlspecialchars($file["attachment"]->getDescription($languageId)); ?>');" title="<?php echo htmlspecialchars($file['attachment']->getDescription($languageId)); ?>"><?php echo $file['attachment']->getFileName(); ?></a><br />
       <?php echo htmlspecialchars($file['attachment']->getDescription($languageId)); ?>
        </td>
      </tr>
      </table>
    </td>
  <?php
  }//foreach
}//function drawFiles


function drawNoResults()
{
?>
<table width="100%">
  <tr>
    <td class="noResult"><?php putGS("No Media Files Found"); ?></td>
  </tr>
</table>
<?php
} // fn drawNoResults


function drawErrorBase(&$manager)
{
?>
<table width="100%">
  <tr>
    <td class="error">Invalid base directory: <?php echo $manager->config['base_dir']; ?></td>
  </tr>
</table>
<?php
} // fn drawErrorBase
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <title>Image List</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="css/attachmentlist.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="assets/dialog.js"></script>
  <script type="text/javascript">
  /*<![CDATA[*/
      if(window.top)
          I18N = window.top.I18N;

      function hideMessage()
      {
          var topDoc = window.top.document;
          var messages = topDoc.getElementById('messages');
          if(messages)
              messages.style.display = "none";
      }

      init = function()
      {
          hideMessage();
          var topDoc = window.top.document;
      }

      function editImage(image)
      {
	  var url = "editor.php?img="+image;
	  Dialog(url, function(param)
	  {
	      if (!param) // user must have pressed Cancel
		  return false;
	      else
		  return true;
	  }, null);
      }
/*]]>*/
    </script>

  <script type="text/javascript" src="../../tiny_mce_popup.js"></script>
  <script type="text/javascript" src="js/campsitemedia.js"></script>
  <script type="text/javascript" src="assets/images.js"></script>
</head>
<body>
<?php if ($manager->isValidBase() == false) { drawErrorBase($manager); }
      elseif(count($list) > 0) { ?>
  <table>
  <tr>
<?php drawFiles($list, $manager); ?>
  </tr>
  </table>

<?php
    $firstAttachment = array_shift($list);
    if (!empty($firstAttachment)) {
        $downloadURL = '/attachment/' . basename($firstAttachment['attachment']->getStorageLocation());
?>
  <!-- automatically select the first attachment -->
  <script>
    CampsiteMediaDialog.select(<?php echo $firstAttachment['attachment']->getAttachmentId(); ?>, '<?php echo $downloadURL; ?>', '<?php echo htmlspecialchars($file["attachment"]->getDescription($languageId)); ?>');
  </script>
<?php } ?>

<?php } else { drawNoResults(); } ?>
</body>
</html>
