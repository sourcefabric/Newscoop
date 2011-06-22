<?php
$GLOBALS['g_campsiteDir'] = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <title>{#campsiteattachment.title}</title>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="css/campsiteattachment.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="../../tiny_mce_popup.js"></script>
  <script type="text/javascript" src="js/campsiteattachment.js"></script>
  <script type="text/javascript" src="assets/popup.js"></script>
  <script type="text/javascript" src="assets/dialog.js"></script>
  <script type="text/javascript" src="assets/manager.js"></script>
</head>
<body>
  <form action="attachments.php" id="uploadForm" method="post">
  <fieldset>
    <div class="dirs">
      <iframe src="attachments.php?article_id=<?php echo $_REQUEST['article_id']; ?>&language_selected=<?php echo $_REQUEST['language_selected']; ?>" name="attachmentsManager" id="attachmentsManager" class="attachmentFrame" scrolling="auto" title="Attachment Selection" frameborder="0"></iframe>
    </div>
  </fieldset>

  <!-- file attachment properties -->
  <table class="inputTable">
    <input type="hidden" id="f_attachment_id" value="" />
    <input type="hidden" id="f_url" value="" />
    <input type="hidden" id="f_description" value="" />
  </table>
  <!--// file attachment properties -->
  <div style="text-align: right;">
    <hr />
    <button type="button" class="buttons" onclick="CampsiteAttachmentDialog.insert();">OK</button>
    <button type="button" class="buttons" onclick="CampsiteAttachmentDialog.close();">Cancel</button>
  </div>
  </form>
</body>
</html>
