<?php
/**
 * Show a list of attachments in a long horizontal table.
 * @author $Author: holman $
 */
$GLOBALS['g_campsiteDir'] = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once($GLOBALS['g_campsiteDir'].'/conf/liveuser_configuration.php');

// Only logged in admin users allowed
if (!$LiveUser->isLoggedIn()) {
    header("Location: /$ADMIN/login.php");
    exit(0);
} else {
    $userId = $LiveUser->getProperty('auth_user_id');
    $userTmp = new User($userId);
    if (!$userTmp->exists() || !$userTmp->isAdmin()) {
        header("Location: /$ADMIN/login.php");
        exit(0);
    }
    unset($userTmp);
}

require_once('config.inc.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/lib_campsite.php");
require_once('classes/AttachmentManager.php');

$Campsite['SUBDIR'] = str_replace('/javascript/tinymce/plugins/campsiteattachment', '', $Campsite['SUBDIR']);

$manager = new AttachmentManager($AMConfig);

$languageSelected = (isset($_REQUEST['language_selected']) && is_numeric($_REQUEST['language_selected']))
    ? $_REQUEST['language_selected'] : null;
$articleId = (isset($_REQUEST['article_id']) && is_numeric($_REQUEST['article_id']))
    ? $_REQUEST['article_id'] : null;

// Get the list of files and directories
$list = array();
if (!is_null($articleId)) {
    $list = $manager->getFiles($articleId, $languageSelected);
}


/**
 * Draw the files in a table.
 */
function drawFiles($list, &$manager)
{
    global $languageSelected, $Campsite;

    $counter = 0;
    foreach($list as $entry => $file)
    {
        $counter++;
        $languageId = ($file['attachment']->getLanguageId()) ? $file['attachment']->getLanguageId() : $languageSelected;
        $downloadURL = $Campsite['SUBDIR'] . '/attachment/' . basename($file['attachment']->getStorageLocation()) . '?g_download=1';
?>
    <td>
      <table width="100" cellpadding="0" cellspacing="0">
      <tr>
        <td class="block" id="block_<?php echo $counter; ?>" onclick="CampsiteAttachmentDialog.select(<?php echo $file['attachment']->getAttachmentId(); ?>, '<?php echo $downloadURL; ?>', '<?php echo camp_javascriptspecialchars($file["attachment"]->getDescription($languageId)); ?>', '<?php echo $counter; ?>');">
          <a href="javascript:;" onclick="CampsiteAttachmentDialog.select(<?php echo $file['attachment']->getAttachmentId(); ?>, '<?php echo $downloadURL; ?>', '<?php echo camp_javascriptspecialchars($file["attachment"]->getDescription($languageId)); ?>');" title="<?php echo addslashes($file['attachment']->getDescription($languageId)); ?>"><?php echo $file['attachment']->getFileName(); ?></a><br />
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
    <td class="noResult"><script>document.write(i18n("No Attachments Found"));</script></td>
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
  <script type="text/javascript" src="js/campsiteattachment.js"></script>
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
        $downloadURL = $Campsite['SUBDIR'] . '/attachment/' . basename($firstAttachment['attachment']->getStorageLocation()) . '?g_download=1';
?>
  <!-- automatically select the first attachment -->
  <script>
    CampsiteAttachmentDialog.select(<?php echo $firstAttachment['attachment']->getAttachmentId(); ?>, '<?php echo $downloadURL; ?>', '<?php echo htmlspecialchars($file["attachment"]->getDescription($languageId)); ?>');
  </script>
<?php } ?>

<?php } else { drawNoResults(); } ?>
</body>
</html>
