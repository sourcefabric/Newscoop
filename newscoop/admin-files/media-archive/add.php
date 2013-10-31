<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!$g_user->hasPermission('AddImage')) {
	camp_html_goto_page("/$ADMIN/logout.php");
}
$q_now = $g_ado_db->GetOne("SELECT LEFT(NOW(), 10)");

if (!is_writable($Campsite['IMAGE_DIRECTORY'])) {
	camp_html_add_msg($translator->trans("Unable to add new image.", array(), 'media_archive'));
	camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $Campsite['IMAGE_DIRECTORY']));
	camp_html_goto_page("/$ADMIN/media-archive/index.php");
	exit;
}

$crumbs = array();
$crumbs[] = array($translator->trans('Content'), "");
$crumbs[] = array($translator->trans('Media Archive', array(), 'media_archive'), "/$ADMIN/media-archive/index.php");
$crumbs[] = array($translator->trans('Add new image', array(), 'media_archive'), "");
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
    <?php echo $translator->trans('Specify image url if you want to load it.', array(), 'media_archive'); ?>
  </div>
  <div class="inputs">
    <label for="form-url"><?php echo $translator->trans('URL'); ?>:</label><input type="text" class="input_text" size="32" name="f_image_url" id="form-url">
  </div>
  <div class="buttons">
    <input type="submit" value="<?php echo $translator->trans('Save All', array(), 'media_archive'); ?>" name="save" class="save-button">
  </div>
</div>

</form>
<p>&nbsp;</p>

<?php $this->view->plupload('', array(
    'url' => './uploader.php',
    'filters' => array(
        $translator->trans('Image files', array(), 'media_archive') => "jpg,jpeg,gif,png",
    ),
)); ?>

<?php camp_html_copyright_notice(); ?>
