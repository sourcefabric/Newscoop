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
<div class="smartlist table">

<table id="smartlist-<?php echo $this->id; ?>" cellpadding="0" cellspacing="0" class="datatable">
<thead>
    <tr>
        <th><input type="checkbox" /></th>
        <th><?php echo putGS('Language'); ?></th>
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
    <tr><td colspan="16"><?php putGS('Loading data'); ?></td></tr>
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

<?php if (!self::$rendered) { ?>
<style type="text/css">
@import url(<?php echo $this->web ?>/css/adm/ColVis.css);
</style>
<script type="text/javascript" src="<?php echo $this->web; ?>/javascript/jquery/ColVis.min.js"></script>
<?php } // render ?>

<script type="text/javascript">
filters = [];

$(document).ready(function() {
$('#smartlist-<?php echo $this->id; ?>').dataTable({
    'bProcessing': false,
    <?php if ($this->items === NULL) { ?>
    'bServerSide': true,
    'sAjaxSource': '/<?php echo $this->admin; ?>/smartlist/do_data.php',
    <?php } ?>
    'bJQueryUI': true,
    'sDom': '<"H"Cfrip>t<"F"ipl>',
    'fnServerData': function (sSource, aoData, fnCallback) {
        for (var i in filters) {
            aoData.push({
                'name': i,
                'value': filters[i],
            });
        }
        $.getJSON(sSource, aoData, function(json) {
            fnCallback(json);
        });
    }, 
    'bStateSave': true,
    'sScrollX': '100%',
    'sScrollXInner': '110%',
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
                switch (obj.aData[6]) {
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
            'aTargets': [6]
        },
        { // hide columns
            'bVisible': false,
            'aTargets': [1, 4, 9, 10, 13, 15],
        },
        { // not sortable
            'bSortable': false,
            'aTargets': [0, 1, 3, 4, 5, 6, 7, 8, 9, 10, 15],
        },
        { // id
            'sWidth': '10px',
            'sClass': 'id',
            'aTargets': [0],
        },
        { // name
            'sWidth': '100px',
            'aTargets': [2],
        },
        { // short
            'sWidth': '50px',
            'aTargets': [6, 7, 8, 9, 10, 11, 12]
        },
        { // dates
            'sWidth': '80px',
            'aTargets': [-1, -2, -3]
        },
        { // default width
            'sWidth': '50px',
            'aTargets': ['_all'],
        },
    ],
    'oColVis': { // disable Show/hide column
        'aiExclude': [0, 1]
    },
    'fnDrawCallback': function() {
        $('table.datatable tbody tr').click(function() {
            $(this).toggleClass('selected');
            input = $('input[type=checkbox]', $(this)).attr('checked', $(this).hasClass('selected'));
        });
        $('table.datatable tbody input[type=checkbox]').change(function() {
            if ($(this).attr('checked')) {
                $(this).parents('tr').addClass('selected');
            } else {
                $(this).parents('tr').removeClass('selected');
            }
        });
    },
});

});
--></script>
