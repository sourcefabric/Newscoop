<?php
/**
 * Show a list of attachments in a long horizontal table.
 * @author $Author: holman $
 */

// set SCRIPT_FILE for correct attachment urls
$_SERVER['SCRIPT_NAME'] = preg_replace('#js/.*$#', 'admin.php', $_SERVER['SCRIPT_NAME']);

$GLOBALS['g_campsiteDir'] = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(__FILE__)))) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    '/usr/share/php/libzend-framework-php',
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap();

Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session( 'Zend_Auth_Admin' ));
$userId = Zend_Auth::getInstance()->getIdentity();

$userTmp = new User($userId);
if (!$userTmp->exists() || !$userTmp->isAdmin()) {
	header("Location: /$ADMIN/login.php");
	exit(0);
}
unset($userTmp);
require_once('config.inc.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/lib_campsite.php");
require_once('classes/AttachmentManager.php');

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
        $downloadURL = $file['attachment']->getAttachmentUrl() . '?g_download=1';
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
    <td class="error">Unable to read the directory: <?php echo $manager->config['base_dir']; ?></td>
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
        $downloadURL = $firstAttachment['attachment']->getAttachmentUrl() . '?g_download=1';
        $languageId = $firstAttachment['attachment']->getLanguageId();
        if (empty($languageId)) {
            $languageId = (int) $_REQUEST['language_selected'];
        }
?>
  <!-- automatically select the first attachment -->
  <script>
    CampsiteAttachmentDialog.select(<?php echo $firstAttachment['attachment']->getAttachmentId(); ?>, '<?php echo $downloadURL; ?>', '<?php echo htmlspecialchars($firstAttachment["attachment"]->getDescription($languageId)); ?>');
  </script>
<?php } ?>

<?php } else { drawNoResults(); } ?>
</body>
</html>
