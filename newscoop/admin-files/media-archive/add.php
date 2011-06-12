<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

camp_load_translation_strings("media_archive");
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');

if (!$g_user->hasPermission('AddImage')) {
	camp_html_goto_page("/$ADMIN/logout.php");
}
$q_now = $g_ado_db->GetOne("SELECT LEFT(NOW(), 10)");

if (!is_writable($Campsite['IMAGE_DIRECTORY'])) {
	camp_html_add_msg(getGS("Unable to add new image."));
	camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $Campsite['IMAGE_DIRECTORY']));
	camp_html_goto_page("/$ADMIN/media-archive/index.php");
	exit;
}

$crumbs = array();
$crumbs[] = array(getGS('Content'), "");
$crumbs[] = array(getGS('Media Archive'), "/$ADMIN/media-archive/index.php");
$crumbs[] = array(getGS('Add new image'), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);

echo $breadcrumbs;

camp_html_display_msgs();
?>

<br />
    <form method="POST" action="/<?php echo $ADMIN; ?>/media-archive/do_upload.php" enctype="multipart/form-data">
<?php echo SecurityToken::FormParameter(); ?>
<div id="uploader"></div>
<div id="uploader_error"></div>

<div class="plupload-addon-bottom clearfix">
  <div class="info">
    <?php putGS('Specify image url if you want to load it.'); ?>
  </div>
  <div class="inputs">
    <label for="form-url"><?php putGS('URL'); ?>:</label><input type="text" class="input_text" size="32" name="f_image_url" id="form-url">
  </div>
  <div class="buttons">
    <input type="submit" value="<?php putGS('Save All'); ?>" name="save" class="save-button">
  </div>
</div>

</form>
<p>&nbsp;</p>

<?php $this->view->plupload('', array(
    'url' => './uploader.php',
    'filters' => array(
        getGS('Image files') => "jpg,gif,png",
    ),
)); ?>

<?php camp_html_copyright_notice(); ?>
