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
        <th><input type="checkbox" /></th>
        <th><?php echo putGS('Language'); ?></th>
        <th><?php echo putGS('Order'); ?></th>
        <th><?php echo putGS('Name'); ?></th>
        <th><?php echo putGS('Type'); ?></th>
        <th><?php echo putGS('Created by'); ?></th>
        <th><?php echo putGS('Author'); ?></th>
        <th><?php echo putGS('Status'); ?></th>
        <th><?php echo putGS('On Front Page'); ?></th>
        <th><?php echo putGS('On Section Page'); ?></th>
        <th><?php echo putGS('Images'); ?></th>
        <th><?php echo putGS('Topics'); ?></th>
        <th><?php echo putGS('Comments'); ?></th>
        <th><?php echo putGS('Reads'); ?></th>
        <th><?php echo putGS('Create Date'); ?></th>
        <th><?php echo putGS('Publish Date'); ?></th>
        <th><?php echo putGS('Last Modified'); ?></th>
    </tr>
</thead>
<tbody>
<?php if ($this->items === NULL) { ?>
    <tr><td colspan="17"><?php putGS('Loading data'); ?></td></tr>
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
<form method="post" action="<?php echo $this->admin; ?>/do_order.php" onsubmit="return sendOrder(this, '<?php echo $this->id; ?>');">
    <?php echo SecurityToken::FormParameter(); ?>
    <input type="hidden" name="language" value="<?php echo $this->language; ?>" />
    <input type="hidden" name="order" value="" />

<fieldset class="buttons">
    <input type="submit" name="Save" value="<?php putGS('Save order'); ?>" />
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
    $.getJSON('/<?php echo $this->admin; ?>/smartlist/do_order.php',
        {
        'order': order,
        'language': $('input[name=language]', $(form)).val(),
        '<?php echo SecurityToken::SECURITY_TOKEN; ?>': '<?php echo SecurityToken::GetToken(); ?>'
        }, function(json) {
            if (json.success) {
                tables[hash].fnDraw(true);
            } else {
                alert('Error: ' + json.message);
            }
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
    'bProcessing': false,
    <?php if ($this->items === NULL) { ?>
    'bServerSide': true,
    'sAjaxSource': '/<?php echo $this->admin; ?>/smartlist/do_data.php',
    <?php } ?>
    'bJQueryUI': true,
    'sDom': '<?php echo $this->getSDom(); ?>',
    'fnServerData': function (sSource, aoData, fnCallback) {
        for (var i in filters['<?php echo $this->id; ?>']) {
            aoData.push({
                'name': i,
                'value': filters['<?php echo $this->id; ?>'][i],
            });
        }
        <?php foreach (array('publication', 'issue', 'section', 'language') as $filter) {
            if (!empty($this->$filter)) { ?>
            aoData.push({
                'name': '<?php echo $filter; ?>',
                'value': '<?php echo $this->$filter; ?>',
            });
        <?php }} ?>
        $.getJSON(sSource, aoData, function(json) {
            fnCallback(json);
        });
    }, 
    //'bStateSave': true,
    'bAutoWidth': false,
    'sScrollX': '100%',
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
                    case 'M':
                        return '<?php putGS('Pub. With Issue'); ?>';
                }
            },
            'aTargets': [7]
        },
        { // hide columns
            'bVisible': false,
                'aTargets': [<?php if (!self::$renderActions) { ?>0, <?php } ?>1, 2, 5, 10, 11, 14, 16],
        },
        { // not sortable
            'bSortable': false,
            'aTargets': [0, 1, 2, 4, 5, 6, 7, 8, 9, 10, 11, 16],
        },
        { // id
            'sClass': 'id',
            'aTargets': [0],
        },
        { // name
            'sClass': 'name',
            'aTargets': [3],
        },
        { // short
            'sClass': 'flag',
            'aTargets': [7, 8, 9, 10, 11, 12, 13]
        },
        { // dates
            'sClass': 'date',
            'aTargets': [14, 15, 16]
        },
    ],
    'oColVis': { // disable Show/hide column
        'aiExclude': [0, 1, 2]
    },
    'fnDrawCallback': function() {
        $('#table-<?php echo $this->id; ?> tbody tr').click(function() {
            $(this).toggleClass('selected');
            input = $('input:checkbox', $(this)).attr('checked', $(this).hasClass('selected'));
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
    <?php if ($this->order) { ?>
    'fnRowCallback': function(nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
        var id = $(aData[0]).attr('name').split('_')[0];
        $(nRow).attr('id', 'article_' + id);
        return nRow;
    },
    'aaSorting': [[2, 'asc']],
    <?php } ?>
});

});
--></script>
