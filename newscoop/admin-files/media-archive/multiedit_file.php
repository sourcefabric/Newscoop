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

if (!$g_user->hasPermission('AddFile')) {
    camp_html_goto_page("/$ADMIN/logout.php");
}

// get all files without description (0)
$fileData = Attachment::GetUnedited($g_user->getUserId());

if (empty($fileData)) {
    camp_html_add_msg(getGS('No files for multi editing.'), 'ok');
    camp_html_goto_page("/$ADMIN/media-archive/index.php");
}

$crumbs = array();
$crumbs[] = array(getGS('Content'), "");
$crumbs[] = array(getGS('Media Archive'), "/$ADMIN/media-archive/index.php");
$crumbs[] = array(getGS('Edit files'), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);

echo $breadcrumbs;

camp_html_display_msgs();

?>
<?php include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");?>
<div class="ui-widget-content padded-strong block-shadow">

<form name="image_multiedit" method="POST" action="/<?php echo $ADMIN; ?>/media-archive/do_multiedit_file.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">

<fieldset class="plain">

<?php echo SecurityToken::FormParameter(); ?>
<ul id="edit-files">
    <?php foreach ($fileData as $index => $file): ?>
    <li>
        <h2><?php echo $file->getFileName(); ?></h2>
        <fieldset>
            <legend><?php  putGS("Change file information"); ?></legend>

            <dl>
                <dt><?php  putGS("Description"); ?>:</dt>
                <dd><input type="text" name="file[<?php echo $file->getAttachmentId(); ?>][f_description]" value="" size="32" class="input_text" size="32" class="input_text" alt="blank" emsg="<?php putGS("Please enter a description for the file: '$1'.",$file->getFileName()); ?>"/></dd>
            </dl>
            <dl>
                <dt><?php  putGS("Should this file only be available for this translation of the article, or for all translations?"); ?></dt>
                <dd>
                    <p>
                        <input id="language_specific_0_<?php echo $index; ?>" class="input_radio" type="radio" name="file[<?php echo $file->getAttachmentId(); ?>][f_language_specific]" value="yes">
                        <label for="language_specific_0_<?php echo $index; ?>" class="inline-style left-floated" style="padding-right:15px"><?php putGS("Only this translation"); ?></label>
                        <input id="language_specific_1_<?php echo $index; ?>" class="input_radio" type="radio" name="file[<?php echo $file->getAttachmentId(); ?>][f_language_specific]" value="no" checked="checked" />
                        <label for="language_specific_1_<?php echo $index; ?>" class="inline-style left-floated" style="padding-right:15px"><?php putGS("All translations"); ?></label>
                    </p>
                </dd>
            </dl>
            <dl>
                <dt><?php  putGS("Do you want this file to open in the user's browser, or to automatically download?"); ?></dt>
                <dd>
                    <p>
                        <input id="disposition_0_<?php echo $index; ?>" class="input_radio" type="radio" name="file[<?php echo $file->getAttachmentId(); ?>][f_content_disposition]" value=""/>
                        <label for="disposition_0_<?php echo $index; ?>" class="inline-style left-floated" style="padding-right:15px"><?php putGS("Open in the browser"); ?></label>
                        <input id="disposition_1_<?php echo $index; ?>" class="input_radio" type="radio" name="file[<?php echo $file->getAttachmentId(); ?>][f_content_disposition]" value="attachment" checked="checked" />
                        <label for="disposition_1_<?php echo $index; ?>" class="inline-style left-floated" style="padding-right:15px"><?php putGS("Automatically download"); ?></label>
                    </p>
                </dd>
            </dl>
        </fieldset>
    </li>
    <?php endforeach; ?>
</ul>

</fieldset>

<fieldset class="plain" style="margin-top: 13px">
    <input type="submit" name="Save" value="<?php  putGS('Save'); ?>" class="save-button" />
</fieldset>

</form>

</div>

<?php camp_html_copyright_notice(); ?>
