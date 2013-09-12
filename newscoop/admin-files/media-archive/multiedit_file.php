<?php
/**
 * @package Newscoop
 *
 * @author Mihai Nistor <mihai.nistor@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

$translator = \Zend_Registry::get('container')->getService('translator');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');

if (!$g_user->hasPermission('AddFile')) {
    camp_html_goto_page("/$ADMIN/logout.php");
}

// get all files without description (0)
$fileData = Attachment::GetUnedited($g_user->getUserId());

if (empty($fileData)) {
    camp_html_add_msg($translator->trans('No files for multi editing.', array(), 'media_archive'), 'ok');
    camp_html_goto_page("/$ADMIN/media-archive/index.php");
}

$crumbs = array();
$crumbs[] = array($translator->trans('Content'), "");
$crumbs[] = array($translator->trans('Media Archive', array(), 'media_archive'), "/$ADMIN/media-archive/index.php");
$crumbs[] = array($translator->trans('Edit files', array(), 'media_archive'), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);

echo $breadcrumbs;

camp_html_display_msgs();

?>
<?php include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");?>
<script type="text/javascript">
    if (window.top.location.href != window.location.href) {
        window.top.location.href = window.location.href;
    }
</script>

<div class="ui-widget-content padded-strong block-shadow">

<form name="image_multiedit" method="POST" action="/<?php echo $ADMIN; ?>/media-archive/do_multiedit_file.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">

<fieldset class="plain">

<?php echo SecurityToken::FormParameter(); ?>
<ul id="edit-files">
    <?php foreach ($fileData as $index => $file): ?>
    <li>
        <h2><?php echo $file->getFileName(); ?></h2>
        <fieldset>
            <legend><?php  echo $translator->trans("Change file information", array(), 'media_archive'); ?></legend>

            <dl>
                <dt><?php  echo $translator->trans("Description"); ?>:</dt>
                <dd><input type="text" name="file[<?php echo $file->getAttachmentId(); ?>][f_description]" value="" size="32" class="input_text" size="32" class="input_text" alt="blank" emsg="<?php echo $translator->trans("Please enter a description for the file: $1.", array('$1' => $file->getFileName()), 'media_archive'); ?>"/></dd>
            </dl>
            <dl>
                <dt><?php  echo $translator->trans("Should this file only be available for this translation of the article, or for all translations?", array(), 'media_archive'); ?></dt>
                <dd>
                    <p>
                        <input id="language_specific_0_<?php echo $index; ?>" class="input_radio" type="radio" name="file[<?php echo $file->getAttachmentId(); ?>][f_language_specific]" value="yes">
                        <label for="language_specific_0_<?php echo $index; ?>" class="inline-style left-floated" style="padding-right:15px"><?php echo $translator->trans("Only this translation", array(), 'media_archive'); ?></label>
                        <input id="language_specific_1_<?php echo $index; ?>" class="input_radio" type="radio" name="file[<?php echo $file->getAttachmentId(); ?>][f_language_specific]" value="no" checked="checked" />
                        <label for="language_specific_1_<?php echo $index; ?>" class="inline-style left-floated" style="padding-right:15px"><?php echo $translator->trans("All translations", array(), 'media_archive'); ?></label>
                    </p>
                </dd>
            </dl>
            <dl>
                <dt><?php  echo $translator->trans("Do you want this file to open in the user's browser, or to automatically download?", array(), 'media_archive'); ?></dt>
                <dd>
                    <p>
                        <input id="disposition_0_<?php echo $index; ?>" class="input_radio" type="radio" name="file[<?php echo $file->getAttachmentId(); ?>][f_content_disposition]" value=""/>
                        <label for="disposition_0_<?php echo $index; ?>" class="inline-style left-floated" style="padding-right:15px"><?php echo $translator->trans("Open in the browser", array(), 'media_archive'); ?></label>
                        <input id="disposition_1_<?php echo $index; ?>" class="input_radio" type="radio" name="file[<?php echo $file->getAttachmentId(); ?>][f_content_disposition]" value="attachment" checked="checked" />
                        <label for="disposition_1_<?php echo $index; ?>" class="inline-style left-floated" style="padding-right:15px"><?php echo $translator->trans("Automatically download", array(), 'media_archive'); ?></label>
                    </p>
                </dd>
            </dl>
        </fieldset>
    </li>
    <?php endforeach; ?>
</ul>

</fieldset>

<fieldset class="plain" style="margin-top: 13px">
    <input type="submit" name="Save" value="<?php  echo $translator->trans('Save'); ?>" class="save-button" />
</fieldset>

</form>

</div>

<?php camp_html_copyright_notice(); ?>
