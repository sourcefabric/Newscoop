<?php
/**
 * @package Campsite
 *
 * @author Vlad Nicoara <vlad.nicoara@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */
$translator = \Zend_Registry::get('container')->getService('translator');
?>

<script>
jQuery.fn.dataTableExt.oApi.fnSetFilteringDelay = function ( oSettings, iDelay ) {
    /*
     * Inputs:      object:oSettings - dataTables settings object - automatically given
     *              integer:iDelay - delay in milliseconds
     * Usage:       $('#example').dataTable().fnSetFilteringDelay(250);
     * Author:      Zygimantas Berziunas (www.zygimantas.com) and Allan Jardine
     * License:     GPL v2 or BSD 3 point style
     * Contact:     zygimantas.berziunas /AT\ hotmail.com
     */
    var
        _that = this,
        iDelay = (typeof iDelay == 'undefined') ? 250 : iDelay;
     
    this.each( function ( i ) {
        $.fn.dataTableExt.iApiIndex = i;
        var
            $this = this, 
            oTimerId = null, 
            sPreviousSearch = null,
            anControl = $( 'input', _that.fnSettings().aanFeatures.f );
         
            anControl.unbind( 'keyup' ).bind( 'keyup', function(event) {
	            var $$this = $this;
	            var searchKeyword;
	            var inputKeyword;
	            
	            inputKeyword = anControl.val();
	            searchKeyword = inputKeyword;
	            
	            if (sPreviousSearch === null || sPreviousSearch != anControl.val()) {
	                window.clearTimeout(oTimerId);
	                sPreviousSearch = anControl.val();  
	                oTimerId = window.setTimeout(function() {
	                    $.fn.dataTableExt.iApiIndex = i;
	                    searchKeyword = inputKeyword; 
	                    _that.fnFilter( searchKeyword );
	                }, iDelay);
	            }
	        });
         
        return this;
    } );
    return this;
}

</script>

<input id="search_table_id" type="hidden" value="table-<?php echo $this->id; ?>" />
<div class="table">

<table id="table-<?php echo $this->id; ?>" cellpadding="0" cellspacing="0" class="datatable">

</table>
</div>

<?php if (!self::$renderTable) { ?>
<script type="text/javascript"><!--

//TODO should not name these generic name like filters and tables, shoudld put them in some namespace...
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
            flashMessage('<?php echo $translator->trans('Order updated.', array(), 'library'); ?>');
        });
    return false;
}
--></script>
<?php } // render ?>
<script type="text/javascript"><!--
$(document).ready(function()
{

	var table = $('#table-<?php echo $this->id; ?>');
	// TODO restrictive, inaccessible from outside, where's the interface for this?
	if (typeof contextListFilters == 'undefined')
		filters['<?php echo $this->id; ?>'] = [];
	else
		filters['<?php echo $this->id; ?>'] = contextListFilters;

    tables['<?php echo $this->id; ?>'] = table.dataTable({
    	'bLengthChange': false,
        'bAutoWidth': true,
        'bScrollCollapse': true,
        'bssDestroy': true,
        'sDom': '<?php echo $this->getContextSDom(); ?>',
        'oLanguage': {
            'oPaginate': {
                'sFirst': '<?php echo $translator->trans('First', array(), 'library'); ?>',
                'sNext': '<?php echo $translator->trans('Next'); ?>',
                'sPrevious': '<?php echo $translator->trans('Previous'); ?>',
                'sLast': '<?php echo $translator->trans('Last', array(), 'library'); ?>',
            },

            'sZeroRecords': '<?php echo $translator->trans('No records found.', array(), 'library'); ?>',
            'sSearch': '',
            'sInfo': '<?php echo $translator->trans('Showing _START_ to _END_ of _TOTAL_ entries', array(), 'library'); ?>',
            'sEmpty': '<?php echo $translator->trans('No entries to show', array(), 'library'); ?>',
            'sInfoFiltered': '<?php echo $translator->trans(' - filtering from _MAX_ records', array(), 'library'); ?>',
            'sLengthMenu': '<?php echo $translator->trans('Display _MENU_ records', array(), 'library'); ?>',
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

            { // hide columns
                'bVisible': false,
                'aTargets': [0,1]
            },
            { // not sortable
                'bSortable': false,
                'aTargets': [0, 1, 2]
            },
            { // id
                'sClass': 'id',
                'sWidth': '3em',
                'aTargets': [0]
            },
            { // name
                'sClass': 'name',
                'sWidth': '13em',
                'aTargets': [2]
            },
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

                // update check all checkbox on item change
                var table = $('#table-<?php echo $this->id; ?>');
                if ($('tbody input:checkbox', table).size() == $('tbody input:checkbox:checked', table).size()) { // all checked
                    $('.smartlist thead input:checkbox').attr("checked", true);
                } else {
                    $('.smartlist thead input:checkbox').attr("checked", false);
                }
            });

            <?php if ($this->order) { ?>


            <?php } ?>
        },
        <?php if ($this->items !== NULL) { // display all items ?>
        'bPaging': false,
        'iDisplayLength': 5,
        <?php } else { // no items - server side ?>
        'bPaging': true,
        'bServerSide': true,
        'iDisplayLength' : 5,
        'sAjaxSource': '<?php echo $this->path; ?>/do_data.php',
        'sPaginationType': 'full_numbers',
        'aaSorting' : [[0, 'desc']],
        'fnServerData': function (sSource, aoData, fnCallback)
        {
            var addedFilters = new Array();
            for (var i in filters['<?php echo $this->id; ?>'])
			{
    			addedFilters.push(i);
                aoData.push
                ({
                    'name': i,
                    'value': filters['<?php echo $this->id; ?>'][i],
                });
            }

            <?php foreach (array('publication', 'issue', 'section', 'language') as $filter) : ?>
    		    <?php if ($filter == 'language' && !$this->order) continue; /*ignore language on non-section pages, TODO what does this mean?*/ ?>
    		    <?php if (!empty($this->$filter)) : ?>
                	if ($.inArray('<?php echo $filter ?>', addedFilters) == -1
                    	&& $.trim(filters['<?php echo $this->id; ?>']['<?php echo $filter ?>']) == '')
                    	aoData.push
                        ({
                            'name': '<?php echo $filter; ?>',
                            'value': '<?php echo $this->$filter; ?>',
                        });
                <?php endif ?>
            <?php endforeach ?>

            callServer(['ContextList', 'doData'], aoData, fnCallback);
        },
        'fnStateLoadCallback': function(oSettings, oData) {
            oData.sFilter = ''; // reset filter
            <?php if ($this->order) { ?>
            oData.aaSorting = [[2, 'asc']]; // show correct order on reload
            <?php } ?>
            return true;
        },
        <?php } ?>
        <?php if ($this->colVis) { ?>
        'oColVisx': { // disable Show/hide column
            //'aiExclude': [0, 1, 2],
           // 'buttonText': '<?php echo $translator->trans('Show / hide columns', array(), 'library'); ?>',
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
    }).css('position', 'relative').css('width', '100%').fnSetFilteringDelay(500);

});
--></script>
