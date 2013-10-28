<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/ArticleImage.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Image.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!$g_user->hasPermission("AddImage")) {
	camp_html_display_error($translator->trans("You do not have the right to add images", array(), 'media_archive'), null, true);
	exit;
}
$maxId = Image::GetMaxId();
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

if (!is_writable($Campsite['IMAGE_DIRECTORY'])) {
	camp_html_add_msg($translator->trans("Unable to add new image.", array(), 'media_archive'));
	camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $Campsite['IMAGE_DIRECTORY']));
}

$articleObj = new Article($f_language_selected, $f_article_number);
$publicationObj = new Publication($f_publication_id);
$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
$sectionObj = new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);

$ImageTemplateId = ArticleImage::GetUnusedTemplateId($f_article_number);

$q_now = $g_ado_db->GetOne("SELECT LEFT(NOW(), 10)");

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

camp_html_display_msgs();

?>

<form method="POST" action="/<?php echo $ADMIN; ?>/media-archive/do_upload.php" enctype="multipart/form-data">
<?php echo SecurityToken::FormParameter(); ?>
<input type="hidden" name="f_article_edit" value="1">
<input type="hidden" name="f_publication_id" value="<?php echo($f_publication_id); ?>">
<input type="hidden" name="f_issue_number" value="<?php echo($f_issue_number); ?>">
<input type="hidden" name="f_section_number" value="<?php echo($f_section_number); ?>">
<input type="hidden" name="f_language_id" value="<?php echo($f_language_id); ?>">
<input type="hidden" name="f_language_selected" value="<?php echo($f_language_selected); ?>">
<input type="hidden" name="f_article_number" value="<?php echo($f_article_number); ?>">
<input type="hidden" name="f_place" value="0" id="f_place">
<div id="uploader"></div>
<div id="uploader_error"></div>


<div class="plupload-addon-bottom clearfix">
  <div class="buttons">
    <input type="submit" value="<?php echo $translator->trans('Attach'); ?>" name="save" class="save-button">
    <input type="submit" value="<?php echo $translator->trans('Attach & Place', array(), 'article_images'); ?>" name="save" class="save-button" onClick="document.getElementById('f_place').value = 1;">
  </div>
</div>

</form>
<p>&nbsp;</p>
<script type="text/javascript" src="../../../js/jquery/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../js/jquery/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javasctipt" src="../../../js/jquery/i18n/jquery.ui.datepicker-' . $this->locale . '.js"></script>
<script type="text/javascript" src="../../../js/jquery/jquery-ui-timepicker-addon.min.js"></script>
<script type="text/javascript" src="../../../js/jquery/fg.menu.js"></script>
<script type="text/javascript" src="../../../js/jquery/jquery.cookie.js"></script>
<script type="text/javascript" src="../../../js/jquery/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../../../js/jquery/ColVis.min.js"></script>
<script type="text/javascript" src="../../../js/jquery/jquery.widgets.js"></script>
<script type="text/javascript" src="../../../js/jquery/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript" src="../../../js/admin.js"></script>

<link rel="stylesheet" type="text/css" media="screen" href="../../../js/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" />

<?php $this->view->plupload('', array(
    'url' => '../../media-archive/uploader.php',
    'filters' => array(
        $translator->trans('Image files', array(), 'article_images') => "jpg,gif,png",
    ),
)); ?>
