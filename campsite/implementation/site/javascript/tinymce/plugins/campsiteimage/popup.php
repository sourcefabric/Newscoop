<?php
/**
 * The main GUI for the ImageManager.
 * @author $Author: paul $
 * @version $Id: manager.php 5087 2006-06-01 21:54:08Z paul $
 * @package ImageManager
 */

require_once('config.inc.php');
require_once('classes/ImageManager.php');

$manager = new ImageManager($IMConfig);
$imageParams = '';
if (isset($_REQUEST['image_id'])) {
    $imageParams = '&image_id=' . $_REQUEST['image_id'];
    if (isset($_REQUEST['image_alt'])) {
        $imageParams .= '&image_alt=' . htmlspecialchars($_REQUEST['image_alt'], ENT_QUOTES);
    }
    if (isset($_REQUEST['image_title'])) {
        $imageParams .= '&image_title=' . htmlspecialchars($_REQUEST['image_title'], ENT_QUOTES);
    }
    if (isset($_REQUEST['image_alignment'])) {
        $imageParams .= '&image_alignment=' . htmlspecialchars($_REQUEST['image_alignment'], ENT_QUOTES);
    }
    if (isset($_REQUEST['image_ratio'])) {
        $imageParams .= '&image_ratio=' . htmlspecialchars($_REQUEST['image_ratio'], ENT_QUOTES);
    }
    if (isset($_REQUEST['image_resize_width'])) {
        $imageParams .= '&image_resize_width=' . htmlspecialchars($_REQUEST['image_resize_width'], ENT_QUOTES);
    }
    if (isset($_REQUEST['image_resize_height'])) {
        $imageParams .= '&image_resize_height=' . htmlspecialchars($_REQUEST['image_resize_height'], ENT_QUOTES);
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
      <td align="right"><label for="f_alt">Alt</label></td>
      <td><input type="text" id="f_alt" class="largelWidth" value="" /></td>
    </tr>
    <tr>
      <td align="right"><label for="f_caption">Caption</label></td>
      <td><input type="text" id="f_caption" class="largelWidth" value="" /></td>
    </tr>
    <tr>
      <td align="right"><label for="f_align">Alignment:</label></td>
      <td>
        <select size="1" id="f_align"  title="Positioning of this image">
          <option value="">Not set</option>
          <option value="left">Left</option>
          <option value="right">Right</option>
          <option value="middle">Middle</option>
        </select>
      </td>
    </tr>
    <tr>
      <td align="right"><label for="f_ratio">Resizing Ratio:</label></td>
      <td><input type="text" id="f_ratio" class="largelWidth" value="" /></td>
    </tr>
    <tr>
      <td align="right"><label for="f_resize_width">Resizing Width:</label></td>
      <td><input type="text" id="f_resize_width" class="largelWidth" value="" /></td>
    </tr>
    <tr>
      <td align="right"><label for="f_resize_height">Resizing Height:</label></td>
      <td><input type="text" id="f_resize_height" class="largelWidth" value="" /></td>
    </tr>
    </table>
    <!--// image properties -->
    <div style="text-align: right;">
      <hr />
      <?php if (isset($_REQUEST['image_id'])) { ?>
      <button type="button" class="buttons" onclick="CampsiteImageDialog.edit();">Edit</button>
      <?php } else { ?>
      <button type="button" class="buttons" onclick="CampsiteImageDialog.insert();">OK</button>
      <?php } ?>
      <button type="button" class="buttons" onclick="CampsiteImageDialog.close();">Cancel</button>
    </div>
  </form>
</body>
</html>
