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

//
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 1);
if (isset($_SESSION['f_language_selected'])) {
    $f_old_language_selected = (int)$_SESSION['f_language_selected'];
} else {
    $f_old_language_selected = 0;
}
$f_language_selected = (int)camp_session_get('f_language_selected', 0); 
// Get all publications
$publications = Publication::GetPublications();
$publicationsNo = is_array($publications) ? sizeof($publications) : 0;
$menuPubTitle = $publicationsNo > 0 ? getGS('All Publications') : getGS('No publications found');

// Get all issues
$issues = Issue::GetIssues(Null, $f_language_id);
$issuesNo = is_array($issues) ? sizeof($issues) : 0;
$menuIssueTitle = $issuesNo > 0 ? getGS('All Issues') : getGS('No issues found');

// Get all sections
$sections = Section::GetSections(Null, Null, $f_language_id);
$sectionsNo = is_array($sections) ? sizeof($sections) : 0;
$menuSectionTitle = $sectionsNo > 0 ? getGS('All Sections') : getGS('No sections found');

// Get the whole topics tree
$allTopics = Topic::GetTree();

// Get Authors
$authors = Author::GetAllExistingNames();

//
$crumbs = array();
$crumbs[] = array(getGS('Content'), ''); $crumbs[] = array(getGS('Article List'), '');
echo camp_html_breadcrumbs($crumbs);
?>

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

<fieldset class="filters">
    <legend><?php putGS('Filters'); ?></legend>

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
            <?php foreach ($authors as $id => $name) { ?>
            <option value="<?php echo $name; ?>"><?php echo $name; ?></option>
            <?php } ?>
        </select></dd>
    </dl>
</fieldset>

<fieldset class="actions">
    <legend><?php putGS('Actions'); ?></legend>
    <select name="action">
        <option value="0">---</option>
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

<table cellpadding="0" cellspacing="0" class="datatable">
<thead>
    <tr>
        <th><input type="checkbox" name="all" value="1" /></th>
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
    <tr>
        <td colspan="15"><?php putGS('Loading data'); ?></td>
    </tr>
</tbody>
</table>

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
    'bProcessing': true,
    'bServerSide': true,
    'sAjaxSource': './assets/php/dynamicfilter/data.php',
    'bJQueryUI': true,
    'sDom': '<"H"Cfrlip>t<"F"lip>',
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
            'aTargets': [1, 4, 9, 10, 13, 15],
        },
        { // not sortable
            'bSortable': false,
            'aTargets': [0, 1, 6, 7, 8, 9, 10, 11, 15],
        },
        { // width for name
            'sWidth': '15em',
            'aTargets': [2],
        },
        { // width for flags + numbers
            'sWidth': '7em',
            'aTargets': [6, 7, 8, 9, 10, 11, 12]
        },
    ],
    'oColVis': { // disable Show/hide column
        'aiExclude': [0, 1]
    },
    'bStateSave': true,
    'sScrollX': '100%',
    'sScrollXInner': '110%',
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
    var action_ary = $(this).val().split(/[[\]]/);
    var action = action_ary[0];
    var param = action_ary[1];

    var items = [];
    $('table.datatable td input:checkbox:checked').each(function() {
        items.push($(this).attr('name'));
    });

    if (items.length == 0) {
        $(this).val('0');
        alert("<?php putGS('Select some articles first.'); ?>");
        return;
    }

    $.getJSON('./assets/dt_actions.php', {
        'action': action,
        'items': items,
        '<?php echo SecurityToken::SECURITY_TOKEN; ?>': '<?php echo SecurityToken::GetToken(); ?>'
    }, function(data, textStatus) {
        if (!data.success) {
            alert('Error: ' + data.message);
        } else {
            alert('Info: ' + data.message);
        }
        table.fnDraw(true);
    });
});

// datepicker for dates
$('input.date').datepicker({
    dateFormat: 'yy-mm-dd',
    showButtonPanel: true
});

// check all/none
$('table.datatable th input[type=checkbox]').change(function() {
    $('table.datatable td input[type=checkbox]').attr('checked', $(this).attr('checked'));
});

});
</script>

<?php camp_html_copyright_notice(); ?>
