<?php
require_once LIBS_DIR . '/ImageList/ImageList.php';
require_once LIBS_DIR . '/MediaList/MediaList.php';
$translator = \Zend_Registry::get('container')->getService('translator');

camp_html_content_top($translator->trans('Media Archive', array(), 'media_archive'), NULL);
?>
<script>
    var adminUrl = '<?php global $ADMIN; echo($ADMIN); ?>';
</script>

<?php camp_html_display_msgs(); ?>

<div id="archive">
<ul>
    <li><a href="#images"><?php echo $translator->trans('Images'); ?></a></li>
    <li><a href="#slideshows"><?php echo $translator->trans('Slideshows', array(), 'media_archive'); ?></a></li>
    <li><a href="#files"><?php echo $translator->trans('Files'); ?></a></li>
</ul>

<div id="images">
    <fieldset class="actions">
    <?php if ($g_user->hasPermission('AddImage')) { ?>
    <span class="actions" style="margin-right:20px;">
        <a href="/<?php echo $ADMIN; ?>/media-archive/add.php" title="<?php echo $translator->trans('Add new image', array(), 'media_archive'); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" alt="<?php echo $translator->trans('Add new image', array(), 'media_archive'); ?>">&nbsp;<?php echo $translator->trans('Add new image', array(), 'media_archive'); ?></a>
        &nbsp;&nbsp;
        <a href="/<?php echo $ADMIN; ?>/media-archive/edit.php?f_fix_thumbs=1" title="<?php echo $translator->trans('Fix all thumbnails', array(), 'media_archive'); ?>"><?php echo $translator->trans('Fix all thumbnails', array(), 'media_archive'); ?></a>
    </span>
    <?php } ?>

    <?php if ($g_user->hasPermission('DeleteImage')) { ?>
        <input type="submit" class="button" name="delete" value="<?php echo $translator->trans('Delete selected', array(), 'media_archive'); ?>" />
        <input type="submit" class="button" name="approve" value="<?php echo $translator->trans('Approve selected', array(), 'media_archive'); ?>" />
        <input type="submit" class="button" name="disapprove" value="<?php echo $translator->trans('Disapprove selected', array(), 'media_archive'); ?>" />
    <?php } ?>
    </fieldset>

    <?php
        $list = new ImageList;
        $list->setSearch(TRUE);
        $list->render();
    ?>
</div><!-- /#images -->

<div id="slideshows">
<?php
    $limit = 25;
    $paginator = Zend_Paginator::factory($this->_helper->service('package')->getCountBy(array()));
    $paginator->setItemCountPerPage($limit);
    $paginator->setCurrentPageNumber(1);
    echo $this->view->partial('slideshow-list.phtml', array(
        'slideshows' => $this->view->slideshowsJson($this->_helper->service('package')->findBy(array(), array('id' => 'desc'), $limit, 0)),
        'pages' => $paginator->count(),
        'articlePage' => false,
    )); ?>
</div>

<div id="files">
    <fieldset class="actions">
    <?php if ($g_user->hasPermission('AddFile')) { ?>
    <span class="actions" style="margin-right:20px;">
        <a href="/<?php echo $ADMIN; ?>/media-archive/add_file.php" title="<?php echo $translator->trans('Add new file', array(), 'media_archive'); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" alt="<?php echo $translator->trans('Add new file', array(), 'media_archive'); ?>">&nbsp;<?php echo $translator->trans('Add new file', array(), 'media_archive'); ?></a>
    </span>
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

    <?php if ($g_user->hasPermission('DeleteFile')) { ?>
        <input type="submit" class="button" name="delete" value="<?php echo $translator->trans('Delete selected', array(), 'media_archive'); ?>" />
        <input type="submit" class="button" name="approve" value="<?php echo $translator->trans('Approve selected', array(), 'media_archive'); ?>" />
        <input type="submit" class="button" name="disapprove" value="<?php echo $translator->trans('Disapprove selected', array(), 'media_archive'); ?>" />
    <?php } ?>
    </fieldset>

    <?php
        $list = new MediaList;
        $list->setColVis(TRUE);
        $list->setSearch(TRUE);
        $list->render();
    ?>
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
            alert('<?php echo $translator->trans('Select some items first.', array(), 'media_archive'); ?>');
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
            flashMessage("<?php echo $translator->trans("You cant delete used files.", array(), 'media_archive'); ?>", 'error');
            return true;
        }

        // confirm
        if (!used && !confirm('<?php echo $translator->trans('Are you sure you want to delete selected items?', array(), 'media_archive'); ?>')) {
            return false;
        } else if (used && !confirm("<?php echo $translator->trans("You cant delete used files.", array(), 'media_archive'), ' ', $translator->trans("Do you want to delete unused only?", array(), 'media_archive'); ?>")) {
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
            flashMessage('<?php echo $translator->trans('Items deleted.', array(), 'media_archive'); ?>');
        });

        return false;
    });
    
    // approve button
    $('input[name=approve]').click(function() {
        var tab = $(this).closest('div');
        var table = $('table.datatable', tab);
        var items = $('tbody input:checked', table);

        // check for items
        if (!items.size()) {
            alert('<?php echo $translator->trans('Select some items first.', array(), 'media_archive'); ?>');
            return false;
        }

        // get ids
        var ids = [];
        var used = false;
        items.each(function() {
            ids.push($(this).attr('value'));
        });

        if (!ids.length) { // only used selected, nothing to delete
            flashMessage("<?php echo $translator->trans("You cant update used files.", array(), 'media_archive'); ?>", 'error');
            return true;
        }

        // confirm
        if (!confirm('<?php echo $translator->trans('Are you sure you want to update selected items?', array(), 'media_archive'); ?>')) {
            return false;
        }

        // delete
        var callback = [];
        if (table.hasClass('medialist')) { // files
            callback = ['MediaList', 'doApprove'];
        } else {
            callback = ['ImageList', 'doApprove'];
        }
        callServer(callback, [ids], function (json) {
            var smartlistId = table.closest('.smartlist').attr('id').split('-')[1];
            tables[smartlistId].fnDraw(true);
            flashMessage('<?php echo $translator->trans('Items updated.', array(), 'media_archive'); ?>');
        });

        return false;
    });
    
    // disapprove button
    $('input[name=disapprove]').click(function() {
        var tab = $(this).closest('div');
        var table = $('table.datatable', tab);
        var items = $('tbody input:checked', table);

        // check for items
        if (!items.size()) {
            alert('<?php echo $translator->trans('Select some items first.', array(), 'media_archive'); ?>');
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
            flashMessage("<?php echo $translator->trans("You cant update used files.", array(), 'media_archive'); ?>", 'error');
            return true;
        }

        // confirm
        if (!used && !confirm('<?php echo $translator->trans('Are you sure you want to update selected items?', array(), 'media_archive'); ?>')) {
            return false;
        } else if (used && !confirm("<?php echo $translator->trans("You cant update used files.", array(), 'media_archive'), ' ', $translator->trans("Do you want to update unused only?", array(), 'media_archive'); ?>")) {
            return false; // delete canceled
        }

        // delete
        var callback = [];
        if (table.hasClass('medialist')) { // files
            callback = ['MediaList', 'doDisapprove'];
        } else {
            callback = ['ImageList', 'doDisapprove'];
        }
        callServer(callback, [ids], function (json) {
            var smartlistId = table.closest('.smartlist').attr('id').split('-')[1];
            tables[smartlistId].fnDraw(true);
            flashMessage('<?php echo $translator->trans('Items updated.', array(), 'media_archive'); ?>');
        });

        return false;
    });
});

function view(field, id, value) {
    console.log(value);
    if (!value) {
        value = $('#row_' + id).find('.' + field).data('old');
    }
    var element = $('#row_' + id).find('.' + field);
    var editElement = $('#edit_'+field+'_'+id);
    
    editElement.remove();
    element.html(value);
    element.show();
}

function edit(field, id) {
    var element = $('#row_' + id).find('.' + field);
    var previous = element.prev();
    var value = element.html();
    
    element.data('old', value);
    element.hide();
    if (field == 'date') {
        previous.after('<td id="edit_'+field+'_'+id+'"><input id="input_'+field+'_'+id+'" value="'+value+'"><br><button onClick="save(\''+field+'\', '+id+');"><?php echo $translator->trans('Save'); ?></button><button class="cancel" onClick="view(\''+field+'\', '+id+');"><?php echo $translator->trans('Cancel'); ?></button></td>');
        $('#input_'+field+'_'+id).datepicker({
            dateFormat : 'yy-mm-dd',
            defaultDate: 'value'
        });
    }
    else {
        previous.after('<td id="edit_'+field+'_'+id+'"><input id="input_'+field+'_'+id+'" value="'+value+'"><br><button onClick="save(\''+field+'\', '+id+');"><?php echo $translator->trans('Save'); ?></button><button class="cancel" onClick="view(\''+field+'\', '+id+');"><?php echo $translator->trans('Cancel'); ?></button></td>');
    }
}

function save(field, id) {
    var value = $('#input_' + field + '_' + id).val();
    $.get('/' + adminUrl + '/media-archive/ajax_save.php', {f_image_id: id, f_field: field, f_value: value});
    view(field, id, value);
}

/**
 * Function to be called from popup after file is uploaded
 * @return void
 */
function onUpload()
{
    var smartlistId = $('table.medialist').attr('id').split('-')[1];
    tables[smartlistId].fnDraw(true);
    flashMessage('<?php echo $translator->trans('File uploaded.', array(), 'media_archive'); ?>');
}


</script>

<?php camp_html_copyright_notice(); ?>
</body>
</html>
