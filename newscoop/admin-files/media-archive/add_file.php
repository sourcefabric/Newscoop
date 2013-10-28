<?php
/**
 * @package Newscoop
 *
 * @author Mihai Nistor <mihai.nistor@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Translation.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!$g_user->hasPermission('AddFile')) {
    camp_html_goto_page("/$ADMIN/logout.php");
}

$q_now = $g_ado_db->GetOne("SELECT LEFT(NOW(), 10)");

camp_html_display_msgs();
?>

<html>
  <head>
    <?php 
      include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/html_head.php");
      include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");
    ?>
    <link rel="stylesheet" type="text/css" media="screen" href="../../js/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" />
  </head>
  <body>

  <br />
      <form method="POST" action="/<?php echo $ADMIN; ?>/media-archive/do_upload_file.php" enctype="multipart/form-data">
  <?php echo SecurityToken::FormParameter(); ?>
  <div id="uploader"></div>
  <div id="uploader_error"></div>

  <div class="plupload-addon-bottom clearfix">
    <div class="buttons">
      <input type="submit" value="<?php echo $translator->trans('Save All', array(), 'media_archive'); ?>" name="save" class="save-button">
    </div>
  </div>

  </form>
  <p>&nbsp;</p>

  <?php $this->view->plupload('', array(
      'url' => './uploader_file.php',
  )); ?>

  <?php camp_html_copyright_notice(); ?>
  </body>
</html>
