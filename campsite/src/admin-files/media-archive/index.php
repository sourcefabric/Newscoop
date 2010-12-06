<?php
camp_load_translation_strings('media_archive');
camp_load_translation_strings('api');

require_once LIBS_DIR . '/ImageList/ImageList.php';
require_once LIBS_DIR . '/MediaList/MediaList.php';

$crumbs = array();
$crumbs[] = array(getGS('Content'), "");
$crumbs[] = array(getGS('Media Archive'), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;
?>

<?php camp_html_display_msgs(); ?>

<div id="archive">
<ul>
    <li><a href="#images"><?php putGS('Images archive'); ?></a></li>
    <li><a href="#files"><?php putGS('Files archive'); ?></a></li>
</ul>

<div id="images">

    <?php if ($g_user->hasPermission('AddImage')) { ?>
    <p class="actions">
        <a href="add.php"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" alt="<?php  putGS('Add new image'); ?>"> <?php putGS('Add new image'); ?></a>
    </p>
    <?php } ?>

    <?php
        $list = new ImageList;
        $list->setSearch(TRUE);
        $list->render();
    ?>

    <fieldset class="actions">
        <input type="submit" name="delete" value="<?php putGS('Delete selected'); ?>" />
    </fieldset>
</div><!-- /#images -->

<div id="files">
    <?php
        $list = new MediaList;
        $list->setColVis(TRUE);
        $list->setSearch(TRUE);
        $list->render();
    ?>

    <fieldset class="actions">
        <input type="submit" name="delete" value="<?php putGS('Delete selected'); ?>" />
    </fieldset>
</div><!-- /#files -->

</div><!-- /#archive -->
<script type="text/javascript">
<!--
$(document).ready(function() {
    // tabs
    $('#archive').tabs()
        .css('border', 'none');

    // delete button
    $('input[name=delete]').click(function() {
        var tab = $(this).closest('div');
        var table = $('table.datatable', tab);
        var items = $('tbody input:checked', table);

        // check for items
        if (!items.size()) {
            alert('<?php putGS('Select some items first.'); ?>');
            return false;
        }

        // confirm
        if (!confirm('<?php putGS('Are you sure you want to delete selected items?'); ?>')) {
            return false;
        }

        // get ids
        var ids = [];
        items.each(function() {
            ids.push($(this).attr('name'));
        });

        // delete
        var callback = [];
        if (table.hasClass('medialist')) { // files
            callback = ['MediaList', 'doDelete'];
        } else {
            callback = ['ImageList', 'doDelete'];
        }
        callServer(callback, [ids], function (json) {
            var smartlistId = table.closest('.smartlist').attr('id').split('-')[1];
            tables[smartlistId].fnDraw(true);
            flashMessage('<?php putGS('Items deleted.'); ?>');
        });

        return false;
    });
});
//-->
</script>

<?php camp_html_copyright_notice(); ?>
</body>
</html>
