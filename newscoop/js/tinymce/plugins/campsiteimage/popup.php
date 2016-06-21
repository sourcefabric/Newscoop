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
        $imageParams .= '&image_title=' . urlencode(str_replace('\\', '', $_REQUEST['image_title']));
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

$preferencesService = \Zend_Registry::get('container')->getService('preferences');
$richtextCaption = $preferencesService->MediaRichTextCaptions;
$captionLimit = $preferencesService->MediaCaptionLength;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <title>{#campsiteimage_dlg.title}</title>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="/js/tinymce/plugins/campsiteimage/css/campsiteimage.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="/js/jquery/jquery-1.7.1.min.js"></script>
  <script type="text/javascript" src="/js/jquery/jquery-ui-1.8.6.custom.min.js"></script>
  <script type="text/javascript" src="/js/tinymce/tiny_mce_popup.js"></script>
  <script type="text/javascript">
  <?php
    require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/media-archive/editor_load_tinymce.php");

    if ($richtextCaption == 'Y') { ?>
      var captionsEnabled = true;
      var captionLimit = <?php echo (int) $captionLimit; ?>;
  <?php } else { ?>
      var captionsEnabled = false;
  <?php } ?>
  </script>
  <script type="text/javascript" src="/js/tinymce/plugins/campsiteimage/js/campsiteimage.js?v=4.3.2"></script>
  <script type="text/javascript" src="/js/tinymce/plugins/campsiteimage/assets/popup.js"></script>
  <script type="text/javascript" src="/js/tinymce/plugins/campsiteimage/assets/dialog.js"></script>
  <script type="text/javascript" src="/js/tinymce/plugins/campsiteimage/assets/manager.js"></script>
</head>
<body>
  <form action="images.php" id="uploadForm" method="post" enctype="multipart/form-data" onSubmit="<?php
    if ($richtextCaption == 'Y') {
        echo 'return validateTinyMCEEditors();';
    }
?>">
  <fieldset>
    <div class="dirs">
      <iframe src="/js/tinymce/plugins/campsiteimage/images.php?article_id=<?php echo $_REQUEST['article_id'] . $imageParams.'&time='.time(); ?>" name="imgManager" id="imgManager" class="imageFrame" scrolling="auto" title="Image Selection" frameborder="0"></iframe>
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
      <td>
          <?php
            if ($richtextCaption == 'Y') {
              $user = \Zend_Registry::get('container')->getService('user');
              $languageSelectedObj = new Language((int) camp_session_get('LoginLanguageId', 0));
              $editorLanguage = !empty($_COOKIE['TOL_Language']) ? $_COOKIE['TOL_Language'] : $languageSelectedObj->getCode();

              editor_load_tinymce('f_caption', $user->getCurrentUser(), $editorLanguage, array('max_chars' => $captionLimit, 'toolbar_length' => 19));
          ?>
              <textarea name="f_caption" id="f_caption" rows="8" cols="70"></textarea>
          <?php } else { ?>
              <input type="text" id="f_caption" name="f_caption" class="largelWidth" value="" />
          <?php } ?>
      </td>
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
      <td colspan="2" align="right"><small>{#campsiteimage_dlg.resizenotice}</small></td>
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
