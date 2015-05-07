<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once LIBS_DIR . '/MediaList/MediaList.php';
require_once LIBS_DIR . '/MediaPlayer/MediaPlayer.php';

$f_attachment_id = Input::Get('f_attachment_id', 'int', 0);
$translator = \Zend_Registry::get('container')->getService('translator');

if (!Input::IsValid()) {
    camp_html_goto_page("/$ADMIN/media-archive/index.php#files");
}

$em = \Zend_Registry::get('container')->getService('em');
$attachment = $em->getRepository('Newscoop\Entity\Attachment')->findOneById($f_attachment_id);
$attachmentService = \Zend_Registry::get('container')->getService('attachment');

$label_text = '';
$crumbs = array();
$crumbs[] = array($translator->trans('Content'), "");
$crumbs[] = array($translator->trans('Media Archive', array(), 'home'), "/$ADMIN/media-archive/index.php#files");
if ($g_user->hasPermission('ChangeImage')) {
    $label_text = $translator->trans('Change attachment information', array(), 'media_archive');
}
else {
    $label_text = $translator->trans('View attachment', array(), 'media_archive');
}

$crumbs[] = array($label_text, '');
$breadcrumbs = camp_html_breadcrumbs($crumbs);

$controller->view->headTitle($label_text.' - Newscoop Admin', 'SET');

echo $breadcrumbs;
?>

<?php camp_html_display_msgs(); ?>

<div class="wrapper"><div class="main-content-wrapper">

<h2><?php echo $attachment->getName(); ?></h2>
<p class="dates"><?php echo $translator->trans('Created', array(), 'media_archive'); ?>: <?php echo $attachment->getCreated()->format('Y-m-d H:i:s'); ?>, <?php echo $translator->trans('Last modified', array(), 'media_archive'); ?>: <?php echo $attachment->getUpdated()->format('Y-m-d H:i:s'); ?></p>

<?php echo new MediaPlayer($attachmentService->getAttachmentUrl($attachment) . '?g_show_in_browser=1', $attachment->getMimeType()); ?>

<dl class="attachment">
    <dt><?php echo $translator->trans('Type'); ?>:</dt>
    <dd><?php echo $attachment->getMimeType(); ?></dd>

    <dt><?php echo $translator->trans('Size', array(), 'media_archive'); ?>:</dt>
    <dd><?php echo MediaList::FormatFileSize($attachment->getSizeInBytes()); ?></dd>

    <?php if ($attachment->getHttpCharset()) { ?>
    <dt><?php echo $translator->trans('Charset', array(), 'media_archive'); ?>:</dt>
    <dd><?php echo $attachment->getHttpCharset(); ?></dd>
    <?php } ?>

</dl>

<form name="edit" method="POST" action="/<?php echo $ADMIN; ?>/media-archive/do_edit-attachment.php">
    <?php echo SecurityToken::FormParameter(); ?>
    <input type="hidden" name="f_attachment_id" value="<?php echo $attachment->getId(); ?>" />

<div class="ui-widget-content big-block block-shadow padded-strong">
    <fieldset class="plain">

    <legend><?php echo $translator->trans('Change attachment information', array(), 'media_archive'); ?></legend>
    <ul>
        <li>
            <label for="description"><?php echo $translator->trans("Description"); ?>:</label>
            <input id="description" type="text" name="f_description" value="<?php echo htmlspecialchars($attachment->getDescription()); ?>" size="50" maxlength="255" class="input_text" />
        </li>
        <li>
            <label><?php echo $translator->trans("Do you want this file to open in the users browser, or to automatically download?", array(), 'media_archive'); ?></label>
            <input id="disposition0" class="input_radio" type="radio" name="f_content_disposition" value=""<?php if ($attachment->getContentDisposition() == NULL) { echo ' checked="checked"'; } ?> />
            <label for="disposition0" class="inline-style left-floated" style="padding-right:15px"><?php echo $translator->trans("Open in the browser", array(), 'media_archive'); ?></label>
            <input id="disposition1" class="input_radio" type="radio" name="f_content_disposition" value="attachment"<?php if ($attachment->getContentDisposition() == 'attachment') { echo ' checked="checked"'; } ?> />
            <label for="disposition1" class="inline-style left-floated"><?php echo $translator->trans("Automatically download", array(), 'media_archive'); ?></label>
        </li>
        <li>
            <label>&nbsp;</label>
            <input type="submit" name="Save" value="<?php  echo $translator->trans('Save'); ?>" class="button" />
        </li>
    </ul>

    </fieldset>
</div>

</form>

</div></div><!-- /.main-content-wrapper /.wrapper -->
<?php camp_html_copyright_notice(); ?>
</body>
</html>
