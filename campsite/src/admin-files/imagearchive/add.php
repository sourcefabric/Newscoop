<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

camp_load_translation_strings("imagearchive");
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
	camp_html_goto_page("/$ADMIN/imagearchive/index.php");
	exit;
}

$crumbs = array();
$crumbs[] = array(getGS('Content'), "");
$crumbs[] = array(getGS('Image Archive'), "/$ADMIN/imagearchive/index.php");
$crumbs[] = array(getGS('Add new image'), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);

echo $breadcrumbs;

camp_html_display_msgs();
?>

<!-- Load Queue widget CSS and jQuery -->
<style>
@import url(<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/plupload/css/plupload.queue.css);
</style>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-1.4.2.min.js"></script>
<!-- Load plupload and all it's runtimes and the jQuery queue widget -->
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/plupload/js/plupload.full.min.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/plupload/js/jquery.plupload.queue.min.js"></script>

<br />
<form method="POST" action="do_upload.php" enctype="multipart/form-data">
<?php echo SecurityToken::FormParameter(); ?>
<div id="uploader"></div>
<div id="uploader_error"></div>

<table border="0" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td colspan="2" align="center" style="padding-top: 15px;">
    <?php p(wordwrap(getGS("Specify image url if you want to load it."), 60, "<br />")); ?>
  </td>
</td>
<tr>
    <td align="right" ><?php putGS('URL'); ?>:</td>
    <td align="left">
        <input id="form-url" type="text" name="f_image_url" size="32" class="input_text" />
    </td>
</tr>
<tr>
  <td colspan="2" align="center">
    <input type="submit" class="button" name="save" value="<?php putGS('Save'); ?>" />
  </td>
</tr>
</table>
</form>
<p>&nbsp;</p>

<script type="text/javascript">
$(function() {
    $("#uploader").pluploadQueue({
        // General settings
        runtimes : 'html5',
        url : 'multifile_uploader.php',
        unique_names : true,
        filters : [
            {title : "Image files", extensions : "jpg,gif,png"},
        ],
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
    'Select files' : '<?php putGS('Select files'); ?>',
    'Add files to the upload queue and click the start button.' : '<?php putGS('Add files to the upload queue and click the start button.'); ?>',
    'Filename' : '<?php putGS('Filename'); ?>',
    'Status' : '<?php putGS('Status'); ?>',
    'Size' : '<?php putGS('Size'); ?>',
    'Add files' : '<?php putGS('Add files'); ?>',
    'Start upload' : '<?php putGS('Start upload'); ?>',
    'Stop current upload' : '<?php putGS('Stop current upload'); ?>',
    'Start uploading queue' : '<?php putGS('Start uploading queue'); ?>',
    'Drag files here.' : '<?php putGS('Drag files here.'); ?>'
});
</script>

<?php camp_html_copyright_notice(); ?>
