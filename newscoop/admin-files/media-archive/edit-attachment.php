<?php
camp_load_translation_strings("media_archive");
camp_load_translation_strings("article_files");
camp_load_translation_strings("library");
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

<?php camp_html_display_msgs(); ?>

<div class="wrapper"><div class="main-content-wrapper">

<h2><?php echo $object->getFileName(); ?></h2>
<p class="dates"><?php putGS('Created'); ?>: <?php echo $object->getTimeCreated(); ?>, <?php putGS('Last modified'); ?>: <?php echo $object->getLastModified(); ?></p>

<?php echo new MediaPlayer($object->getAttachmentUrl() . '?g_show_in_browser=1', $object->getMimeType()); ?>

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

<form name="edit" method="POST" action="/<?php echo $ADMIN; ?>/media-archive/do_edit-attachment.php">
    <?php echo SecurityToken::FormParameter(); ?>
    <input type="hidden" name="f_attachment_id" value="<?php echo $object->getAttachmentId(); ?>" />

<div class="ui-widget-content big-block block-shadow padded-strong">
    <fieldset class="plain">

    <legend><?php putGS('Change attachment information'); ?></legend>

    <ul>
        <li>
            <label for="description"><?php putGS("Description"); ?>:</label>
            <input id="description" type="text" name="f_description" value="<?php echo htmlspecialchars($object->getDescription($object->getLanguageId())); ?>" size="50" maxlength="255" class="input_text" />
        </li>
        <li>
            <label><?php putGS("Do you want this file to open in the user's browser, or to automatically download?"); ?></label>
            <input id="disposition0" class="input_radio" type="radio" name="f_content_disposition" value=""<?php if ($object->getContentDisposition() == NULL) { echo ' checked="checked"'; } ?> />
            <label for="disposition0" class="inline-style left-floated" style="padding-right:15px"><?php putGS("Open in the browser"); ?></label>
            <input id="disposition1" class="input_radio" type="radio" name="f_content_disposition" value="attachment"<?php if ($object->getContentDisposition() == 'attachment') { echo ' checked="checked"'; } ?> />
            <label for="disposition1" class="inline-style left-floated"><?php putGS("Automatically download"); ?></label>
        </li>
        <li>
            <label>&nbsp;</label>
            <input type="submit" name="Save" value="<?php  putGS('Save'); ?>" class="button" />
        </li>
    </ul>

    </fieldset>
</div>

</form>

</div></div><!-- /.main-content-wrapper /.wrapper -->

<?php camp_html_copyright_notice(); ?>
</body>
</html>
