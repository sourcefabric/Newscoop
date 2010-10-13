<?php
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");
require_once($GLOBALS['g_campsiteDir']."/classes/XR_CcClient.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Publication.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Issue.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Section.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Topic.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Author.php");

camp_load_translation_strings("articles");
camp_load_translation_strings("universal_list");

// get publications
$publications = Publication::GetPublications();
$publicationsNo = is_array($publications) ? sizeof($publications) : 0;
$menuPubTitle = $publicationsNo > 0 ? getGS('All Publications') : getGS('No publications found');

// get issues
$issues = Issue::GetIssues(Null, $f_language_id);
$issuesNo = is_array($issues) ? sizeof($issues) : 0;
$menuIssueTitle = $issuesNo > 0 ? getGS('All Issues') : getGS('No issues found');

// get sections
$sections = Section::GetSections(Null, Null, $f_language_id);
$sectionsNo = is_array($sections) ? sizeof($sections) : 0;
$menuSectionTitle = $sectionsNo > 0 ? getGS('All Sections') : getGS('No sections found');
?>
<div class="smartlist">

<div class="controls">

<fieldset class="filters">
    <legend><?php putGS('Select issue'); ?></legend>
    <select name="publication">
        <?php if ($publicationsNo > 0) { ?>
        <option value="0"><?php p($menuPubTitle); ?></option>
        <?php foreach($publications as $tmpPublication) { ?>
        <option value="<?php echo $tmpPublication->getPublicationId(); ?>"><?php echo $tmpPublication->getName(); ?></option>
        <?php }
        } ?>
    </select>

    <select name="issue">
        <?php if ($issuesNo > 0) { ?>
        <option value="0"><?php p($menuIssueTitle); ?></option>
        <?php foreach($issues as $issue) { ?>
        <option value="<?php echo $issue->getIssueNumber(); ?>"><?php echo $issue->getName(); ?></option>
        <?php }
        } ?>
    </select>

    <select name="section">
        <?php if ($sectionsNo > 0) { ?>
        <option value="0"><?php p($menuSectionTitle); ?></option>
        <?php foreach($sections as $section) { ?>
        <option value="<?php echo $section->getSectionNumber(); ?>"><?php echo $section->getName(); ?></option>
        <?php }
        } ?>
    </select>
</fieldset>

<fieldset class="filters more">
    <legend><?php putGS('Filter'); ?></legend>
    <dl>
        <dt><label for="filter_date"><?php putGS('Date'); ?></label></dt>
        <dd><input id="filter_date" type="text" name="publish_date" class="date" /></dd>
    </dl>
    <dl>
        <dt><label for="filter_from"><?php putGS('From'); ?></label></dt>
        <dd><input id="filter_from" type="text" name="publish_date_from" class="date" /></dd>
    </dl>
    <dl>
        <dt><label for="filter_to"><?php putGS('To'); ?></label></dt>
        <dd><input id="filter_to" type="text" name="publish_date_to" class="date" /></dd>
    </dl>
    <dl>
        <dt><label for="filter_author"><?php putGS('Author'); ?></label></dt>
        <dd><select name="author">
            <option value=""><?php putGS('All'); ?></option>
            <?php foreach (Author::GetAuthors() as $author) { ?>
            <option value="<?php echo $author->getId(); ?>"><?php echo $author->getName(); ?></option>
            <?php } ?>
        </select></dd>
    </dl>
    <dl> <dt><label for="filter_creator"><?php putGS('Creator'); ?></label></dt>
        <dd><select name="creator">
            <option value=""><?php putGS('All'); ?></option>
            <?php foreach (User::GetUsers() as $creator) { ?>
            <option value="<?php echo $creator->getUserId(); ?>"><?php echo $creator->getRealName(); ?></option>
            <?php } ?>
        </select></dd>
    </dl>
    <dl>
        <dt><label for="filter_status"><?php putGS('Status'); ?></label></dt>
        <dd><select name="workflow_status">
            <option value=""><?php putGS('All'); ?></option>
            <option value="published"><?php putGS('Published'); ?></option>
            <option value="new"><?php putGS('New'); ?></option>
            <option value="submitted"><?php putGS('Submitted'); ?></option>
            <option value="withissue"><?php putGS('Publish with issue'); ?></option>
        </select></dd>
    </dl>
    <dl>
        <dt><label for="filter_topic"><?php putGS('Topic'); ?></label></dt>
        <dd><select name="topic">
            <option value=""><?php putGS('All'); ?></option>
            <?php foreach (Topic::GetTopics($f_language_id) as $topic) { ?>
            <option value="<?php echo $topic->getTopicId(); ?>"><?php echo $topic->getName($f_language_id); ?></option>
            <?php } ?>
        </select></dd>
    </dl>
</fieldset>

<fieldset class="actions">
    <legend><?php putGS('Select action'); ?></legend>
    <select name="action">
        <option value="">---</option>
        <option value="workflow_publish"><?php putGS('Status: Publish'); ?></option>
        <option value="workflow_submit"><?php putGS('Status: Submit'); ?></option>
        <option value="workflow_new"><?php putGS('Status: Set New'); ?></option>
        <option value="switch_onfrontpage"><?php putGS("Toggle: 'On Front Page'"); ?></option>
        <option value="switch_onsectionpage"><?php putGS("Toggle: 'On Section Page'"); ?></option>
        <option value="switch_comments"><?php putGS("Toggle: 'Comments'"); ?></option>
        <option value="unlock"><?php putGS('Unlock'); ?></option>
        <option value="delete"><?php putGS('Delete'); ?></option>
        <option value="duplicate"><?php putGS('Duplicate'); ?></option>
        <option value="duplicate_interactive"><?php putGS('Duplicate to another section'); ?></option>
        <option value="move"><?php putGS('Move'); ?></option>
    </select>
</fieldset>

</div><!-- /.controls -->
<div class="data">

<table cellpadding="0" cellspacing="0" class="datatable">
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
    <tr><td colspan="16"><?php putGS('Loading data'); ?></td></tr>
</tbody>
</table>

</div><!-- /.data -->

<style>
@import url(<?php echo $Campsite['WEBSITE_URL']; ?>/css/adm/jquery-ui-1.8.5.custom.css);
@import url(<?php echo $Campsite['WEBSITE_URL']; ?>/css/adm/ColVis.css);
</style>

<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-ui-1.8.5.custom.min.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/ColVis.min.js"></script>
<script type="text/javascript">
filters = [];
$(document).ready(function() {

var table = $('table.datatable').dataTable({
    'bProcessing': false,
    'bServerSide': true,
    'sAjaxSource': '/<?php echo $ADMIN; ?>/smartlist.data.php',
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
    'bAutoWidth': false,
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
        {
            'sClass': 'id',
            'aTargets': [0],
        },
        {
            'sClass': 'name',
            'aTargets': [2],
        },
        {
            'sClass': 'short',
            'aTargets': [6, 7, 8, 9, 10, 11, 12]
        },
        {
            'sClass': 'date',
            'aTargets': [-1, -2, -3]
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

// filters handle
$('.filters select, .filters input').change(function() {
    var name = $(this).attr('name');
    var value = $(this).val();
    filters[name] = value;
    table.fnDraw(true);
    return false;
});

// actions handle
$('.actions select').change(function() {
    var action = $(this).val();
    $(this).val('');

    var items = [];
    $('table.datatable td input:checkbox:checked').each(function() {
        items.push($(this).attr('name'));
    });

    if (items.length == 0) {
        alert("<?php putGS('Select some articles first.'); ?>");
        return;
    }

    $.getJSON('/<?php echo $ADMIN; ?>/smartlist.action.php', {
        'action': action,
        'items': items,
        '<?php echo SecurityToken::SECURITY_TOKEN; ?>': '<?php echo SecurityToken::GetToken(); ?>'
    }, function(data, textStatus) {
        if (!data.success) {
            alert('Error: ' + data.message);
        } else {
            // TODO dialog
        }
        table.fnDraw(true);
    });
});

// datepicker for dates
$('input.date').datepicker({
    dateFormat: 'yy-mm-dd',
});

// check all/none
$('table.datatable thead input[type=checkbox]').change(function() {
    var checked = $(this).attr('checked');
    $('table.datatable tbody input[type=checkbox]').each(function() {
        $(this).attr('checked', checked);
        if (checked) {
            $(this).parents('tr').addClass('selected');
        } else {
            $(this).parents('tr').removeClass('selected');
        }
    });
});

// filters managment
$('fieldset.filters.more').each(function() {
    $('dl', $(this)).hide();
    $('<select class="filters"></select>')
        .appendTo($(this))
        .change(function() {
        var value = $(this).val();
        $('option', $(this)).detach();
        $(this).append('<option value=""><?php putGS('Filter by...'); ?></option>');
        $('dl', $(this).parent()).each(function() {
            var label = $('label', $(this)).text();
            if (label == value) {
                $(this).show();
                $(this).insertBefore($('select.filters', $(this).parent()));
                if ($('a', $(this)).length == 0) {
                    $('<a class="detach">X</a>').appendTo($('dd', $(this)))
                        .click(function() {
                            $(this).parents('dl').hide();
                            $('input, select', $(this).parent()).val('').change();
                            $('select.filters').change();
                        });
                }
            } else if ($(this).css('display') == 'none') {
                $(this).siblings('select.filters').append('<option value="'+label+'">'+label+'</option>');
            }
        });
    }).change();
});

});
</script>
</div><!-- /.smartlist -->
