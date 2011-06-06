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

<!-- Load Queue widget CSS and jQuery -->
<style>
@import url(<?php echo $Campsite['WEBSITE_URL']; ?>/js/plupload/css/plupload.queue.css);
</style>
<!-- Load plupload and all it's runtimes and the jQuery queue widget -->
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/plupload/js/plupload.full.min.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/plupload/js/jquery.plupload.queue.min.js"></script>

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

<script type="text/javascript">
$(function() {
    $("#uploader").pluploadQueue({
        // General settings
        runtimes : 'html5',
        url : 'uploader.php',
        unique_names : true,
        filters : [
            {title : "Image files", extensions : "jpg,gif,png"}
        ]
    });

    // Client side form validation
    $('form').submit(function(e) {
        var uploader = $('#uploader').pluploadQueue();
        var url = $('#form-url').val();

        // Validate number of uploaded files
        if (uploader.total.uploaded == 0) {
            // Files in queue upload them first
            if (uploader.files.length > 0) {
                // When all files are uploaded submit form
                uploader.bind('UploadProgress', function() {
                    if (uploader.total.uploaded == uploader.files.length) {
                        $('form').submit();
                    }
                });
                uploader.start();
            } else if (url.length > 0) {
                return;
            } else {
                alert('You must at least upload one file.');
            }
            e.preventDefault();
        }
    });
});

plupload.addI18n({
    'Select files' : '<?php putGS('Select files'); ?>', // Select images
    'Add files to the upload queue and click the start button.' : '<?php putGS('Add files to the upload queue and click the start button.'); ?>',
    'Filename' : '<?php putGS('Filename'); ?>',
    'Status' : '<?php putGS('Status'); ?>',
    'Size' : '<?php putGS('Size'); ?>',
    'Add files' : '<?php putGS('Add files'); ?>', // Add images
    'Start upload' : '<?php putGS('Start upload'); ?>',
    'Stop current upload' : '<?php putGS('Stop current upload'); ?>',
    'Start uploading queue' : '<?php putGS('Start uploading queue'); ?>',
    'Drag files here.' : '<?php putGS('Drag files here.'); ?>'
});
</script>

<?php camp_html_copyright_notice(); ?>
