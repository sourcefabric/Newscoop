<?php
/**
 * @package Newscoop
 *
 * @author Mihai Nistor <mihai.nistor@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

camp_load_translation_strings("media_archive");
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Translation.php');

if (!$g_user->hasPermission('AddFile')) {
    camp_html_goto_page("/$ADMIN/logout.php");
}
$q_now = $g_ado_db->GetOne("SELECT LEFT(NOW(), 10)");

$crumbs = array();
$crumbs[] = array(getGS('Content'), "");
$crumbs[] = array(getGS('Media Archive'), "/$ADMIN/media-archive/index.php");
$crumbs[] = array(getGS('Add new file'), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);

echo $breadcrumbs;

camp_html_display_msgs();
?>

<br />
    <form method="POST" action="/<?php echo $ADMIN; ?>/media-archive/do_upload_file.php" enctype="multipart/form-data">
<?php echo SecurityToken::FormParameter(); ?>
<div id="uploader"></div>
<div id="uploader_error"></div>

<div class="plupload-addon-bottom clearfix">
  <div class="buttons">
    <input type="submit" value="<?php putGS('Save All'); ?>" name="save" class="save-button">
  </div>
</div>

</form>
<p>&nbsp;</p>

<?php $this->view->plupload('', array(
    'url' => './uploader_file.php',
)); ?>

<?php camp_html_copyright_notice(); ?>
