<?php
camp_load_translation_strings('media_archive');
camp_load_translation_strings('api');
camp_load_translation_strings('library');
require_once LIBS_DIR . '/ImageList/ImageList.php';
require_once LIBS_DIR . '/MediaList/MediaList.php';

camp_html_content_top(getGS('Media Archive'), NULL);
?>
<script>
    var adminUrl = '<?php global $ADMIN; echo($ADMIN); ?>';
</script>

<?php camp_html_display_msgs(); ?>

<div id="archive">
<ul>
    <li><a href="#images"><?php putGS('Images'); ?></a></li>
    <li><a href="#slideshows"><?php putGS('Slideshows'); ?></a></li>
    <li><a href="#files"><?php putGS('Files'); ?></a></li>
</ul>

<div id="images">
    <fieldset class="actions">
    <span style="margin-left:10px;"></span>

    <?php if ($g_user->hasPermission('AddImage')) { ?>
    <span class="actions" style="margin-right:20px;">
        <a href="/<?php echo $ADMIN; ?>/media-archive/add.php" title="<?php putGS('Add new image'); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" alt="<?php putGS('Add new image'); ?>">&nbsp;<?php putGS('Add new image'); ?></a>
        &nbsp;&nbsp;
        <a href="/<?php echo $ADMIN; ?>/media-archive/edit.php?f_fix_thumbs=1" title="<?php putGS('Fix all thumbnails'); ?>"><?php putGS('Fix all thumbnails'); ?></a>
    </span>
    <?php } ?>

    <?php if ($g_user->hasPermission('DeleteImage')) { ?>
        <input type="submit" class="button" name="delete" value="<?php putGS('Delete selected'); ?>" />
        <input type="submit" class="button" name="approve" value="<?php putGS('Approve selected'); ?>" />
        <input type="submit" class="button" name="disapprove" value="<?php putGS('Disapprove selected'); ?>" />
    <?php } ?>

    <span style="float:right;margin-right:20px;" title="<?php putGS('For images automatically loaded via remote feeds'); ?>"><input type="checkbox" id="newswires-images" /> <label for="newswires-images"><?php putGS('Display newsfeed images'); ?></label></span>

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
    )); ?>
</div>

<div id="files">
    <fieldset class="actions">
    <?php if ($g_user->hasPermission('AddFile')) { ?>
    <span class="actions" style="margin-right:20px;">
        <a href="/<?php echo $ADMIN; ?>/media-archive/add_file.php" title="<?php putGS('Add new file'); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" alt="<?php putGS('Add new file'); ?>">&nbsp;<?php putGS('Add new file'); ?></a>
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
        <input type="submit" class="button" name="delete" value="<?php putGS('Delete selected'); ?>" />
        <input type="submit" class="button" name="approve" value="<?php putGS('Approve selected'); ?>" />
        <input type="submit" class="button" name="disapprove" value="<?php putGS('Disapprove selected'); ?>" />
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
    
    // approve button
    $('input[name=approve]').click(function() {
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
            ids.push($(this).attr('value'));
        });

        if (!ids.length) { // only used selected, nothing to delete
            flashMessage("<?php putGS("You can't update used files."); ?>", 'error');
            return true;
        }

        // confirm
        if (!confirm('<?php putGS('Are you sure you want to update selected items?'); ?>')) {
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
            flashMessage('<?php putGS('Items updated.'); ?>');
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
            flashMessage("<?php putGS("You can't update used files."); ?>", 'error');
            return true;
        }

        // confirm
        if (!used && !confirm('<?php putGS('Are you sure you want to update selected items?'); ?>')) {
            return false;
        } else if (used && !confirm("<?php echo getGS("You can't update used files."), ' ', getGS("Do you want to update unused only?"); ?>")) {
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
            flashMessage('<?php putGS('Items updated.'); ?>');
        });

        return false;
    });

    $('#newswires-images').change(function() {
        $.smartlist_filter = $(this).attr('checked') ? 'newsfeed' : '';
        var smartlistId = $('.smartlist', $(this).closest('#images')).attr('id').split('-')[1];
        tables[smartlistId].fnDraw(true);
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
        previous.after('<td id="edit_'+field+'_'+id+'"><input id="input_'+field+'_'+id+'" value="'+value+'"><br><button onClick="save(\''+field+'\', '+id+');"><?php putGS('Save'); ?></button><button class="cancel" onClick="view(\''+field+'\', '+id+');"><?php putGS('Cancel'); ?></button></td>');
        $('#input_'+field+'_'+id).datepicker({
            dateFormat : 'yy-mm-dd',
            defaultDate: 'value'
        });
    }
    else {
        previous.after('<td id="edit_'+field+'_'+id+'"><input id="input_'+field+'_'+id+'" value="'+value+'"><br><button onClick="save(\''+field+'\', '+id+');"><?php putGS('Save'); ?></button><button class="cancel" onClick="view(\''+field+'\', '+id+');"><?php putGS('Cancel'); ?></button></td>');
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
    flashMessage('<?php putGS('File uploaded.'); ?>');
}

/**
 * Callback function used in datatable "fnDrawCallback" callback
 * @param  object element table jquery object
 */
function newscoopMediaArchiveDataTable (element) {
    var fancyBoxSelector;
    if (element.hasClass('imagelist')) {
        fancyBoxSelector = $('#images table td a');
    } else if (element.hasClass('medialist')) {
        fancyBoxSelector = $('#files a');
    }

    fancyBoxSelector.fancybox({
        hideOnContentClick: false,
        width: 1300,
        height: 800,
        type: 'iframe'
    });
};

//-->
</script>

<?php camp_html_copyright_notice(); ?>
</body>
</html>
