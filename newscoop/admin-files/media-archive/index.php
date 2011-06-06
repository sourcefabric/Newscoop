<?php
camp_load_translation_strings('media_archive');
camp_load_translation_strings('api');
camp_load_translation_strings('library');

require_once LIBS_DIR . '/ImageList/ImageList.php';
require_once LIBS_DIR . '/MediaList/MediaList.php';

camp_html_content_top(getGS('Media Archive'), NULL);
?>

<?php camp_html_display_msgs(); ?>

<div id="archive">
<ul>
    <li><a href="#images"><?php putGS('Images'); ?></a></li>
    <li><a href="#files"><?php putGS('Files'); ?></a></li>
</ul>

<div id="images">

    <?php if ($g_user->hasPermission('AddImage')) { ?>
    <p class="actions">
        <a href="/<?php echo $ADMIN; ?>/media-archive/add.php" title="<?php putGS('Add new image'); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" alt="<?php putGS('Add new image'); ?>"><?php putGS('Add new image'); ?></a>
    </p>
    <?php } ?>

    <?php
        $list = new ImageList;
        $list->setSearch(TRUE);
        $list->render();
    ?>

    <?php if ($g_user->hasPermission('DeleteImage')) { ?>
    <fieldset class="actions">
        <input type="submit" name="delete" value="<?php putGS('Delete selected'); ?>" />
    </fieldset>
    <?php } ?>
</div><!-- /#images -->

<div id="files">
    <?php if ($g_user->hasPermission('AddFile')) { ?>
    <p class="actions">
        <a href="/<?php echo $ADMIN; ?>/media-archive/add_file.php" title="<?php putGS('Add new file'); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" alt="<?php putGS('Add new file'); ?>"><?php putGS('Add new file'); ?></a>
    </p>
    <script type="text/javascript"><!--
        $(document).ready(function() {
            $('a#new_file').click(function() {
                var url = $(this).attr('href');
                window.open(url, 'new_file', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=500, height=400, top=200, left=100');
                return false;
            });
        });
    //--></script>
    <?php } ?>

    <?php
        $list = new MediaList;
        $list->setColVis(TRUE);
        $list->setSearch(TRUE);
        $list->render();
    ?>

    <?php if ($g_user->hasPermission('DeleteFile')) { ?>
    <fieldset class="actions">
        <input type="submit" name="delete" value="<?php putGS('Delete selected'); ?>" />
    </fieldset>
    <?php } ?>
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

        // get ids
        var ids = [];
        var used = false;
        items.each(function() {
            if ($('.used', $(this).closest('tr')).size()) {
                used = true;
            } else {
                ids.push($(this).attr('value'));
            }
        });

        if (!ids.length) { // only used selected, nothing to delete
            flashMessage("<?php putGS("You can't delete used files."); ?>", 'error');
            return true;
        }

        // confirm
        if (!used && !confirm('<?php putGS('Are you sure you want to delete selected items?'); ?>')) {
            return false;
        } else if (used && !confirm("<?php echo getGS("You can't delete used files."), ' ', getGS("Do you wan't to delete unused only?"); ?>")) {
            return false; // delete canceled
        }

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

/**
 * Function to be called from popup after file is uploaded
 * @return void
 */
function onUpload()
{
    var smartlistId = $('table.medialist').attr('id').split('-')[1];
    tables[smartlistId].fnDraw(true);
    flashMessage('<?php putGS('File uploaded.'); ?>');
}
//-->
</script>

<?php camp_html_copyright_notice(); ?>
</body>
</html>
