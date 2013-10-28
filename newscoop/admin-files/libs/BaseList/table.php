<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */
$translator = \Zend_Registry::get('container')->getService('translator');
?>
<div class="table">

<table id="table-<?php echo $this->id; ?>" cellpadding="0" cellspacing="0" class="datatable <?php echo strtolower(get_class($this)); ?>">
<thead>
    <tr>
        <?php foreach ($this->cols as $title) { ?>
        <?php if ($title === NULL) { ?>
        <th><input type="checkbox" /></th>
        <?php } else { ?>
        <th><?php echo $title; ?></th>
        <?php }} ?>
    </tr>
</thead>
<tbody>
<?php if ($this->items === NULL) { ?>
    <tr><td colspan="<?php echo sizeof($this->cols); ?>"><?php echo $translator->trans('Loading data', array(), 'library'); ?></td></tr>
<?php } else if (!empty($this->items)) { ?>
    <?php foreach ($this->items as $item) { ?>
    <tr>
        <?php foreach ($item as $row) { ?>
        <td><?php echo $row; ?></td>
        <?php } ?>
    </tr>
    <?php } ?>
<?php } ?>
</tbody>
</table>
</div>
<?php if (!self::$renderTable) { ?>
<script type="text/javascript"><!--
tables = [];
filters = [];
--></script>
<?php } // render ?>
<script type="text/javascript"><!--
$(document).ready(function() {
var table = $('#table-<?php echo $this->id; ?>');
var filters = [];
$.smartlist_filter = '';
tables['<?php echo $this->id; ?>'] = table.dataTable({
    'bAutoWidth': true,
    'bDestroy': true,
    'bJQueryUI': true,
    'bStateSave': true,
    'sDom': '<?php echo $this->getSDom(); ?>',
    'aaSorting': [<?php echo $this->getSorting(); ?>],
    'oLanguage': {
        'oPaginate': {
            'sFirst': '<?php echo $translator->trans('First', array(), 'library'); ?>',
            'sLast': '<?php echo $translator->trans('Last', array(), 'library'); ?>',
            'sNext': '<?php echo $translator->trans('Next'); ?>',
            'sPrevious': '<?php echo $translator->trans('Previous'); ?>',
        },
        'sZeroRecords': '<?php echo $translator->trans('No records found.', array(), 'library'); ?>',
        'sSearch': '<?php echo $translator->trans('Search'); ?>:',
        'sInfo': '<?php echo $translator->trans('Showing _START_ to _END_ of _TOTAL_ entries', array(), 'library'); ?>',
        'sEmpty': '<?php echo $translator->trans('No entries to show', array(), 'library'); ?>',
        'sInfoFiltered': '<?php echo $translator->trans(' - filtering from _MAX_ records', array(), 'library'); ?>',
        'sLengthMenu': '<?php echo $translator->trans('Display _MENU_ records', array(), 'library'); ?>',
        'sInfoEmpty': '',
    },
    'aoColumnDefs': [
        { // inputs for id
            'fnRender': function(obj) {
                var id = obj.aData[0];
                return '<input type="checkbox" name="item[]" value="' + id + '" />';
            },
            'aTargets': [0]
        },
        <?php if (is_int($this->inUseColumn)) { ?>
        { // inputs for id
            'fnRender': function(obj) {
                var inUse = obj.aData[0];
                if (obj.aData[<?php echo $this->inUseColumn; ?>]) {
                    return '<span class="used"><?php echo $translator->trans('Yes'); ?></span>';
                } else {
                    return '<span><?php echo $translator->trans('No'); ?></span>';
                }
            },
            'bSortable': false,
            'aTargets': [<?php echo $this->inUseColumn; ?>]
        },
        <?php } ?>
        { // hide columns
            'bVisible': false,
            'aTargets': [<?php echo implode(', ', $this->hidden); ?>]
        },
        { // not sortable
            'bSortable': false,
            'aTargets': [0, <?php echo implode(', ', $this->notSortable); ?>]
        },
        { // id
            'sClass': 'id',
            'aTargets': [0]
        },
    ],
    'fnDrawCallback': function() {
        $('#table-<?php echo $this->id; ?> tbody tr').click(function(event) {
            
        }).each(function() {
            <?php if ($this->type == 'image') { ?>
                // set 'row_' + id as row id
                var id = $(this).find('.id').find('input').val();
                $(this).attr('id', 'row_' + id);
                $($(this).children()[2]).addClass('description');
                $($(this).children()[3]).addClass('photographer');
                $($(this).children()[4]).addClass('place');
                $($(this).children()[5]).addClass('date');
            <?php } ?>
        });
        
        $('#table-<?php echo $this->id; ?> tbody tr td').click(function(event) {
            <?php if ($this->type == 'image') { ?>
                var id = $(this).parent().find('.id').find('input').val();
                if ($(this).hasClass('description')) {
                    edit('description', id);
                }
                if ($(this).hasClass('photographer')) {
                    edit('photographer', id);
                }
                if ($(this).hasClass('place')) {
                    edit('place', id);
                }
                if ($(this).hasClass('date')) {
                    edit('date', id);
                }
            <?php } ?>
        });

        $('#table-<?php echo $this->id; ?> tbody input:checkbox').change(function() {
            if ($(this).attr('checked')) {
                $(this).parents('tr').addClass('selected');
            } else {
                $(this).parents('tr').removeClass('selected');
            }
        });

        $('#table-<?php echo $this->id; ?> thead input:checkbox').change(function() {
            var main = $(this);
            $('#table-<?php echo $this->id; ?> tbody input:checkbox').each(function() {
                if (main.attr('checked')) {
                    $(this).attr('checked', 'checked');
                } else {
                    $(this).removeAttr('checked');
                }
                $(this).change();
            });
        });

        /**
         * hack for loading fancy box for datatable elements
         */
        if( typeof newscoopMediaArchiveDataTable == 'function') {
            newscoopMediaArchiveDataTable(this);
        }
    },
	'fnCookieCallback': function (sName, oData, sExpires, sPath) {
        oData['abVisCols'] = []; // don't save visibility
		return sName + "="+JSON.stringify(oData)+"; expires=" + sExpires +"; path=" + sPath;
	},
    <?php if ($this->items !== NULL) { // display all items ?>
    'bPaging': false,
    'iDisplayLength': <?php echo sizeof($this->items); ?>,
    <?php } else { // no items - server side ?>
    'bServerSide': true,
    'sAjaxSource': '', // callServer handle
    'bPaging': true,
    'sPaginationType': 'full_numbers',
    'fnServerData': function (sSource, aoData, fnCallback) {
        aoData.push({ 'name': 'filter', 'value': $.smartlist_filter });
        callServer(['<?php echo get_class($this); ?>', 'doData'], aoData, fnCallback);
    },
    <?php } ?>
    <?php if ($this->colVis) { ?>
    'oColVis': { // disable Show/hide column
        'aiExclude': [0, 1],
        'buttonText': '<?php echo $translator->trans('Show / hide columns', array(), 'library'); ?>',
    },
    <?php } ?>
}).css('position', 'relative').css('width', '100%');

});
--></script>
