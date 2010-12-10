<?php
camp_load_translation_strings("media_archive");
camp_load_translation_strings("article_files");
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once LIBS_DIR . '/MediaList/MediaList.php';
require_once LIBS_DIR . '/MediaPlayer/MediaPlayer.php';

$f_attachment_id = Input::Get('f_attachment_id', 'int', 0);

if (!Input::IsValid()) {
	camp_html_goto_page("/$ADMIN/media-archive/index.php#files");
}

$object = new Attachment($f_attachment_id);

$crumbs = array();
$crumbs[] = array(getGS("Content"), "");
$crumbs[] = array(getGS("Media Archive"), "/$ADMIN/media-archive/index.php#files");
if ($g_user->hasPermission('ChangeImage')) {
	$crumbs[] = array(getGS('Change attachment information'), "");
}
else {
	$crumbs[] = array(getGS('View attachment'), "");
}
$breadcrumbs = camp_html_breadcrumbs($crumbs);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

echo $breadcrumbs;
?>
<p></p>

<?php camp_html_display_msgs(); ?>
<p></p>

<h2><?php echo $object->getFileName(); ?></h2>
<p class="dates"><?php putGS('Created'); ?>: <?php echo $object->getTimeCreated(); ?>, <?php putGS('Last modified'); ?>: <?php echo $object->getLastModified(); ?></p>

<?php echo new MediaPlayer($Campsite['WEBSITE_URL'] . '/attachment/' . basename($object->getStorageLocation()) . '?g_show_in_browser=1', $object->getMimeType()); ?>

<dl class="attachment">
    <dt><?php putGS('Type'); ?>:</dt>
    <dd><?php echo $object->getMimeType(); ?></dd>

    <dt><?php putGS('Size'); ?>:</dt>
    <dd><?php echo MediaList::FormatFileSize($object->getSizeInBytes()); ?></dd>

    <?php if ($object->getCharset()) { ?>
    <dt><?php putGS('Charset'); ?>:</dt>
    <dd><?php echo $object->getCharset(); ?></dd>
    <?php } ?>

</dl>

<form name="edit" method="POST" action="do_edit-attachment.php">
    <?php echo SecurityToken::FormParameter(); ?>
	<input type="hidden" name="f_attachment_id" value="<?php echo $object->getAttachmentId(); ?>" />

<fieldset>
    <legend><?php putGS('Change attachment information'); ?></legend>

    <dl>
        <dt><label for="description"><?php putGS("Description"); ?>:</label</dt>
        <dd><input id="description" type="text" name="f_description" value="<?php echo htmlspecialchars($object->getDescription($object->getLanguageId())); ?>" size="50" maxlength="255" class="input_text" /></dd>
    </dl>

    <dl>
        <dt><label><?php putGS("Do you want this file to open in the user's browser, or to automatically download?"); ?></label></dt>
        <dd><input id="disposition0" type="radio" name="f_content_disposition" value=""<?php if ($object->getContentDisposition() == NULL) { echo ' checked="checked"'; } ?> /> <label for="disposition0"><?php putGS("Open in the browser"); ?></label></dd>
        <dd><input id="disposition1" type="radio" name="f_content_disposition" value="attachment"<?php if ($object->getContentDisposition() == 'attachment') { echo ' checked="checked"'; } ?> /> <label for="disposition1"><?php putGS("Automatically download"); ?></label></dd>
    </dl>

    <dl class="buttons">
        <dt>&nbsp;</dt>
        <dd><input type="submit" name="Save" value="<?php  putGS('Save'); ?>" class="button" /></dd>
    </dl>
</fieldset>

</form>

<?php camp_html_copyright_notice(); ?>
</body>
</html>
