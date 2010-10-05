<?php
camp_load_translation_strings("imagearchive");
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');

if (!$g_user->hasPermission('AddImage')) {
	camp_html_goto_page("/$ADMIN/logout.php");
}

$crumbs = array();
$crumbs[] = array(getGS('Content'), "");
$crumbs[] = array(getGS('Image Archive'), "/$ADMIN/imagearchive/index.php");
$crumbs[] = array(getGS('Edit images'), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);

echo $breadcrumbs;

camp_html_display_msgs();

$imageSearch = new ImageSearch('0000', 'id');
$imageSearch->run();
$imageData = $imageSearch->getImages();

if (empty($imageData)) {
    camp_html_add_msg(getGS('No images for multi editing.'), 'ok');
    camp_html_goto_page("/$ADMIN/imagearchive/index.php");
}

?>

<form name="image_multiedit" method="POST" action="do_multiedit.php" enctype="multipart/form-data">
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

<style>
@import url('/css/adm/jquery-ui-1.8.5.custom.css');
</style>

<script type="text/javascript" src="/javascript/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/javascript/jquery-ui-1.8.5.custom.min.js"></script>
<script type="text/javascript">
function copyToAll(field, imageId)
{
    var value = $("input[name*=" + imageId + "][name*=" + field + "]").val();
    $("input[name*=" + field + "]").val(value);
}

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

$('.datepicker').datepicker({
    dateFormat: 'yy-mm-dd'
});

</script>

<fieldset class="buttons">
    <input type="submit" name="Save" value="<?php  putGS('Save'); ?>" class="button">
</fieldset>
</form>

<?php camp_html_copyright_notice(); ?>
