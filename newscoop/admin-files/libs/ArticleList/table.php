<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */
?>
<div class="table">

<table id="table-<?php echo $this->id; ?>" cellpadding="0" cellspacing="0" class="datatable">
<thead>
    <tr>
        <?php foreach ($this->cols as $label) { ?>
        <?php if (!isset($label)) { ?>
        <th><input type="checkbox" /></th>
        <?php } else { ?>
        <th><?php echo $label; ?></th>
        <?php }} ?>
    </tr>
</thead>
<tbody>
<?php if ($this->items === NULL) { ?>
    <tr><td colspan="<?php echo sizeof($this->cols); ?>"><?php putGS('Loading data'); ?></td></tr>
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
<?php if ($this->order) { ?>
<form method="post" action="<?php echo $this->path; ?>/do_order.php" onsubmit="return sendOrder(this, '<?php echo $this->id; ?>');">
    <?php echo SecurityToken::FormParameter(); ?>
    <input type="hidden" name="language" value="<?php echo $this->language; ?>" />
    <input type="hidden" name="order" value="" />

<fieldset class="buttons">
    <input id="button-set-order" type="submit" name="Save" value="<?php putGS('Save order'); ?>" />
</fieldset>
</form>
<div style="clear: both"></div>
<?php } ?>
<?php if (!self::$renderTable) { ?>
<script type="text/javascript"><!--
tables = [];
filters = [];

function sendOrder(form, hash)
{
    var order = $('#table-' + hash + ' tbody').sortable('toArray');
    callServer(['ArticleList', 'doOrder'], [
        order,
        $('input[name=language]', $(form)).val(),
        ], function(data) {
            tables[hash].fnSort([[2, 'asc']]);
            tables[hash].fnDraw(true);
            flashMessage('<?php putGS('Order updated.'); ?>');
        });
    return false;
}
--></script>
<?php } // render ?>
<script type="text/javascript"><!--
$(document).ready(function() {
var table = $('#table-<?php echo $this->id; ?>');
filters['<?php echo $this->id; ?>'] = [];
tables['<?php echo $this->id; ?>'] = table.dataTable({
    'bAutoWidth': true,
    'bScrollCollapse': true,
    'bDestroy': true,
    'sDom': '<?php echo $this->getSDom(); ?>',
    'aaSorting': [
        <?php foreach ($this->orderBy as $column => $dir) { ?>
        [<?php echo $column; ?>, '<?php echo $dir; ?>'],
        <?php } ?>
        [2, 'asc']
    ],
    'oLanguage': {
        'oPaginate': {
            'sFirst': '<?php putGS('First'); ?>',
            'sNext': '<?php putGS('Next'); ?>',
            'sPrevious': '<?php putGS('Previous'); ?>',
            'sLast': '<?php putGS('Last'); ?>',
        },
        'sZeroRecords': '<?php putGS('No records found.'); ?>',
        'sSearch': '<?php putGS('Search'); ?>:',
        'sInfo': '<?php putGS('Showing _START_ to _END_ of _TOTAL_ entries'); ?>',
        'sEmpty': '<?php putGS('No entries to show'); ?>',
        'sInfoFiltered': '<?php putGS(' - filtering from _MAX_ records'); ?>',
        'sLengthMenu': '<?php putGS('Display _MENU_ records'); ?>',
        'sInfoEmpty': '',
    },
    'aoColumnDefs': [
        { // inputs for id
            'fnRender': function(obj) {
                var id = obj.aData[0] + '_' + obj.aData[1];
                return '<input type="checkbox" name="' + id + '" />';
            },
            'aTargets': [0]
        },
        { // status workflow
            'fnRender': function(obj) {
                switch (obj.aData[7]) {
                    case 'Y':
                        return '<?php putGS('Published'); ?>';
                    case 'N':
                        return '<?php putGS('New'); ?>';
                    case 'S':
                        return '<?php putGS('Submitted'); ?>';
                    case 'M': return '<?php putGS('Publish with issue'); ?>';
                }
            },
            'aTargets': [7]
        },
        { // hide columns
            'bVisible': false,
            'aTargets': [<?php if (!self::$renderActions) { ?>0, <?php } ?>1, 2, 5, 10, 11, 16, 18,
                <?php echo implode(', ', $this->hidden); ?>
            ]
        },
        { // not sortable
            'bSortable': false,
            'aTargets': [0, 1, 2, 4, 5, 6, 7, 8, 9, 10, 11, 14, 15, 18, 19]
        },
        { // id
            'sClass': 'id',
            'sWidth': '3em',
            'aTargets': [0]
        },
        { // name
            'sClass': 'name',
            'sWidth': '13em',
            'aTargets': [3]
        },
        { // type & author
            'sWidth': '8em',
            'aTargets': [4, 6]
        },
        { // short
            'sClass': 'flag',
            'sWidth': '5em',
            'aTargets': [7, 8, 9, 10, 11, 12, 13, 14, 15, 19]
        },
        { // dates
            'sClass': 'date',
            'sWidth': '5em',
            'aTargets': [16, 17, 18]
        }
    ],
    'fnDrawCallback': function() {
        $('#table-<?php echo $this->id; ?> tbody tr').click(function(event) {
            if (event.target.type == 'checkbox') {
                return; // checkbox click, handled by it's change
            }

            var input = $('input:checkbox', $(this));
            if (input.attr('checked')) {
                input.removeAttr('checked');
            } else {
                input.attr('checked', 'checked');
            }
            input.change();
        }).each(function() {
            var tr = $(this);
            // detect locks
            if ($('.name .ui-icon-locked', tr).not('.current-user').size()) {
                tr.addClass('locked');
            }
        });


        $('#table-<?php echo $this->id; ?> tbody input:checkbox').change(function() {
            if ($(this).attr('checked')) {
                $(this).parents('tr').addClass('selected');
            } else {
                $(this).parents('tr').removeClass('selected');
            }
        });

        <?php if ($this->order) { ?>
        $('#table-<?php echo $this->id; ?> tbody').sortable();
        <?php } ?>
    },
    <?php if ($this->items !== NULL) { // display all items ?>
    'bPaging': false,
    'iDisplayLength': <?php echo sizeof($this->items); ?>,
    <?php } else { // no items - server side ?>
    'bServerSide': true,
    'sAjaxSource': '<?php echo $this->path; ?>/do_data.php',
    'sPaginationType': 'full_numbers',
    'fnServerData': function (sSource, aoData, fnCallback) {
        for (var i in filters['<?php echo $this->id; ?>']) {
            aoData.push({
                'name': i,
                'value': filters['<?php echo $this->id; ?>'][i],
            });
        }
        <?php foreach (array('publication', 'issue', 'section', 'language') as $filter) {
            if ($filter == 'language' && !$this->order) {
                continue; // ignore language on non-section pages
            }

            if (!empty($this->$filter)) { ?>
            aoData.push({
                'name': '<?php echo $filter; ?>',
                'value': '<?php echo $this->$filter; ?>',
            });
        <?php }} ?>
            callServer(['ArticleList', 'doData'], aoData, fnCallback);
    },
    'bStateSave': true,
    'fnStateLoadCallback': function(oSettings, oData) {
        oData.sFilter = ''; // reset filter
        <?php if ($this->order) { ?>
        oData.aaSorting = [[2, 'asc']]; // show correct order on reload
        <?php } ?>
        return true;
    },
    <?php } ?>
    <?php if ($this->colVis) { ?>
    'oColVis': { // disable Show/hide column
        'aiExclude': [0, 1, 2],
        'buttonText': '<?php putGS('Show / hide columns'); ?>',
    },
    <?php } ?>
    <?php if ($this->order) { ?>
    'fnRowCallback': function(nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
        var id = $(aData[0]).attr('name').split('_')[0];
        $(nRow).attr('id', 'article_' + id);
        return nRow;
    },
    <?php } ?>
    'bJQueryUI': true
}).css('position', 'relative').css('width', '100%');

});
--></script>
