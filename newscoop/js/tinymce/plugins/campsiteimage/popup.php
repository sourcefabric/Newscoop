<?php
$GLOBALS['g_campsiteDir'] = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

// run zend
require_once $GLOBALS['g_campsiteDir'] . '/application.php';
$application->bootstrap();

/**
 * The main GUI for the ImageManager.
 * @author $Author: paul $
 * @author $Author: vlad $
 * @version $Id: manager.php 5087 2006-06-01 21:54:08Z paul $
 * @package ImageManager
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');

$userId = Zend_Auth::getInstance()->getIdentity();
$userTmp = new User($userId);
if (!$userTmp->exists() || !$userTmp->isAdmin()) {
    header("Location: /$ADMIN/login.php");
    exit(0);
}
unset($userTmp);

require_once('config.inc.php');
require_once('classes/ImageManager.php');

$manager = new ImageManager($IMConfig);
$imageParams = '';
if (isset($_REQUEST['image_id'])) {
    $imageParams = '&image_id=' . $_REQUEST['image_id'];
    if (isset($_REQUEST['image_alt'])) {
        $imageParams .= '&image_alt=' . str_replace('\\', '', $_REQUEST['image_alt']);
    }
    if (isset($_REQUEST['image_title'])) {
        $imageParams .= '&image_title=' . str_replace('\\', '', $_REQUEST['image_title']);
    }
    if (isset($_REQUEST['image_alignment'])) {
        if (in_array($_REQUEST['image_alignment'], array('left','right','middle'))) {
            $imageParams .= '&image_alignment=' . $_REQUEST['image_alignment'];
        }
    }
    if (isset($_REQUEST['image_ratio'])) {
        $imageParams .= '&image_ratio=' . (int) $_REQUEST['image_ratio'];
    }
    if (isset($_REQUEST['image_resize_width'])) {
        $imageParams .= '&image_resize_width=' . (int) $_REQUEST['image_resize_width'];
    }
    if (isset($_REQUEST['image_resize_height'])) {
        $imageParams .= '&image_resize_height=' . (int) $_REQUEST['image_resize_height'];
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <title>{#campsiteimage_dlg.title}</title>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="css/campsiteimage.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="../../tiny_mce_popup.js"></script>
  <script type="text/javascript" src="js/campsiteimage.js"></script>
  <script type="text/javascript" src="assets/popup.js"></script>
  <script type="text/javascript" src="assets/dialog.js"></script>
  <script type="text/javascript" src="assets/manager.js"></script>
</head>
<body>
  <form action="images.php" id="uploadForm" method="post" enctype="multipart/form-data">
  <fieldset>
    <div class="dirs">
      <iframe src="images.php?article_id=<?php echo $_REQUEST['article_id'] . $imageParams; ?>" name="imgManager" id="imgManager" class="imageFrame" scrolling="auto" title="Image Selection" frameborder="0"></iframe>
    </div>
  </fieldset>

  <!-- image properties -->
  <table class="inputTable">
    <input type="hidden" id="f_image_template_id" value="" />
    <input type="hidden" id="f_url" value="" />
    <input type="hidden" id="f_vert" value="" />
    <input type="hidden" id="f_horiz" value="" />
    <input type="hidden" id="f_border" value="" />
    <input type="hidden" id="f_width" value="" />
    <input type="hidden" id="f_height" value="" />
    <input type="hidden" id="orginal_width" />
    <input type="hidden" id="orginal_height" />
    <tr>
      <td align="right"><label for="f_alt">{#campsiteimage_dlg.alt}</label></td>
      <td><input type="text" id="f_alt" class="largelWidth" value="" /></td>
    </tr>
    <tr>
      <td align="right"><label for="f_caption">{#campsiteimage_dlg.caption}</label></td>
      <td><input type="text" id="f_caption" class="largelWidth" value="" /></td>
    </tr>
    <tr>
      <td align="right"><label for="f_align">{#campsiteimage_dlg.alignment}:</label></td>
      <td>
        <select size="1" id="f_align"  title="{#campsiteimage_dlg.positiontitle}">
          <option value="">{#campsiteimage_dlg.notset}</option>
          <option value="left">{#campsiteimage_dlg.left}</option>
          <option value="right">{#campsiteimage_dlg.right}</option>
          <option value="middle">{#campsiteimage_dlg.middle}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td align="right"><label for="f_ratio">{#campsiteimage_dlg.resizeratio}:</label></td>
      <td><input type="text" id="f_ratio" class="largelWidth" value="" /></td>
    </tr>
    <tr>
      <td align="right"><label for="f_resize_width">{#campsiteimage_dlg.resizewidth}:</label></td>
      <td><input type="hidden" id="f_original_width" value="" /><input type="text" id="f_resize_width" class="largelWidth" value="" /></td>
    </tr>
    <tr>
      <td align="right"><label for="f_resize_height">{#campsiteimage_dlg.resizeheight}:</label></td>
      <td><input type="hidden" id="f_original_height" value="" /><input type="text" id="f_resize_height" class="largelWidth" value="" /></td>
    </tr>
    </table>
    <!--// image properties -->
    <div style="text-align: right;">
      <hr />
      <?php if (isset($_REQUEST['image_id'])) { ?>
      <button type="button" class="buttons" onclick="CampsiteImageDialog.insert();">{#campsiteimage_dlg.edit}</button>
      <?php } else { ?>
      <button type="button" class="buttons" onclick="CampsiteImageDialog.insert();">{#campsiteimage_dlg.ok}</button>
      <?php } ?>
      <button type="button" class="buttons" onclick="CampsiteImageDialog.close();">{#campsiteimage_dlg.cancel}</button>
    </div>
  </form>
</body>
</html>
