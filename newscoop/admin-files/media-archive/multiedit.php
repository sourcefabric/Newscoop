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

// get all images without set date (0000-00-00)
$imageSearch = new ImageSearch('0000', 'id');
$imageSearch->run();
$imageData = $imageSearch->getImages();

if (empty($imageData)) {
    camp_html_add_msg(getGS('No images for multi editing.'), 'ok');
    camp_html_goto_page("/$ADMIN/media-archive/index.php");
}

$crumbs = array();
$crumbs[] = array(getGS('Content'), "");
$crumbs[] = array(getGS('Media Archive'), "/$ADMIN/media-archive/index.php");
$crumbs[] = array(getGS('Edit images'), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);

echo $breadcrumbs;

camp_html_display_msgs();

?>

<div class="ui-widget-content padded-strong block-shadow">

<form name="image_multiedit" method="POST" action="/<?php echo $ADMIN; ?>/media-archive/do_multiedit.php" enctype="multipart/form-data">

<fieldset class="plain">

<?php echo SecurityToken::FormParameter(); ?>
<ul id="edit-images">
    <?php foreach ($imageData as $image): ?>
    <?php if (!empty($image['Description']) || empty($image['thumbnail_url'])) { continue; } ?>
    <li>
        <div><img src="<?php echo $image['thumbnail_url']; ?>" border="0"></div>
		<fieldset>
		    <legend><?php  putGS("Change image information"); ?></legend>

		    <dl>
		        <dt><?php  putGS("Description"); ?>:</dt>
		        <dd><input type="text" name="image[<?php echo $image['id']; ?>][f_description]" value="<?php echo htmlspecialchars($image['description']); ?>" size="32" class="input_text"></dd>
		    </dl>
		    <dl>
		        <dt><?php  putGS("Photographer"); ?>:</dt>
                <dd>
                    <input type="text" name="image[<?php echo $image['id']; ?>][f_photographer]" value="<?php echo htmlspecialchars($image['photographer']);?>" size="32" class="input_text copy">
                </dd>
		    </dl>
		    <dl>
		        <dt><?php  putGS("Place"); ?>:</dt>
                <dd>
                    <input type="text" name="image[<?php echo $image['id']; ?>][f_place]" value="<?php echo htmlspecialchars($image['place']); ?>" size="32" class="input_text copy">
                </dd>
		    </dl>
		    <dl>
		        <dt><?php  putGS("Date"); ?>:</dt>
                <dd>
                    <input type="text" name="image[<?php echo $image['id']; ?>][f_date]" value="<?php echo date('Y-m-d'); ?>" size="11" maxlength="10" class="input_text copy datepicker">
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

<script type="text/javascript">
/**
 * Copy field value to all fields of that name.
 *
 * @param string field
 * @param int imageId
 *
 * @return void
 */
function copyToAll(field, imageId)
{
    var value = $("input[name*=" + imageId + "][name*=" + field + "]").val();
    $("input[name*=" + field + "]").val(value);
}

// add copy to all link to all inputs with class="copy"
$('input.copy').each(function() {
    var name = $(this).attr('name');
    var match = name.match(/([a-z0-9_]+)/g);
    var id = match[1];
    var field = match[2];

    var elem = '<br /><a class="copy-to-all" href="javascript:copyToAll(\'' + field + '\',' + id + ');">';
    elem += '<?php putGS('Use for all'); ?>';
    elem += '</a>';
    $(this).after(elem);
});

// datepicker for date
$('.datepicker').datepicker({
    dateFormat: 'yy-mm-dd'
});
</script>

<?php camp_html_copyright_notice(); ?>
