<?php
/**
 * @package Campsite
 */

require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl')) {
    camp_html_display_error(getGS("You do not have the right to upload templates."));
    exit;
}

$Path = Input::Get('Path', 'string', '');
$TOL_Language = camp_session_get('TOL_Language', 'en');

if (!Template::IsValidPath($Path)) {
    camp_html_goto_page("/$ADMIN/templates/");
}
$languages = Language::GetLanguages(null, null, null, array(), array(), true);

$fullPath = $Campsite['TEMPLATE_DIRECTORY'].$Path;
if (!is_writable($fullPath)) {
    camp_html_add_msg(getGS("Unable to $1 template.", 'upload'));
    camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $fullPath));
    camp_html_goto_page("/$ADMIN/templates/?Path=".urlencode($Path));
    exit;
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates/");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($Path));
$crumbs[] = array(getGS("Upload template"), "");
echo camp_html_breadcrumbs($crumbs);

camp_html_display_msgs();
?>

<!-- Load Queue widget CSS and jQuery -->
<style>
@import url(<?php echo $Campsite['WEBSITE_URL']; ?>/js/plupload/css/plupload.queue.css);
</style>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/plupload/js/plupload.full.min.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/plupload/js/jquery.plupload.queue.min.js"></script>

<br />
    <form method="POST" action="/<?php echo $ADMIN; ?>/templates/do_upload_templ.php" enctype="multipart/form-data">
<?php echo SecurityToken::FormParameter(); ?>
<div id="uploader"></div>
<div id="uploader_error"></div>

<div class="plupload-addon-bottom clearfix">
    <h3>
        <?php p(getGS("If the file you specified is a text file, you can convert its character set using the dropdown below.")); ?>
    </h3>
    <div class="info"><?php putGS("Template charset"); ?>:</div>
    <div class="inputs select-only">
        <input type="hidden" name="f_path" value="<?php p(htmlspecialchars($Path)); ?>" />
        <select name="f_charset" class="input_select">
        <option value="">-- <?php putGS("Select a language/character set") ?> --</option>
        <option value="UTF-8"><?php putGS("All languages"); ?>/UTF-8</option>
        <?php foreach ($languages as $language) { ?>
        <option value="<?php p($language->getCodePage()); ?>"><?php p($language->getNativeName().'/'.$language->getCodePage()); ?></option>
        <?php } ?>
        </select>
    </div>
    <div class="info last">
    	<?php putGS("(optional)"); ?>
    </div>
    <div class="buttons">
        <input type="submit" class="save-button" name="save" value="<?php putGS('Save'); ?>" />
	</div>
</div>
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
            {title : "Templates", extensions : "tpl"},
            {title : "Image files", extensions : "jpg,gif,png"},
            {title : "CSS files", extensions : "css"},
            {title : "PHP files", extensions : "php"},
            {title : "Javascript files", extensions : "js"}
        ],
    });

    // Client side form validation
    $('form').submit(function(e) {
        var uploader = $('#uploader').pluploadQueue();

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
