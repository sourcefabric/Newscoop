<?php
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");
require_once($GLOBALS['g_campsiteDir']."/classes/XR_CcClient.php");

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

// Get the whole topics tree
$allTopics = Topic::GetTree();

//
$crumbs = array();
$crumbs[] = array(getGS('Content'), '');
$crumbs[] = array(getGS('Article List'), '');
echo camp_html_breadcrumbs($crumbs);
?>

<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/smartlist.css" />

<div id="message" class="message">&nbsp;</div>
<div id="doc" class="yui-skin-sam">
	<div id="hd">
		<div id="breadcrumbContainer">
		    <div id="contentSelectContainer">
			<label id="contentPublicationSelectContainer">
				<input type="button" class="menuButton" id="publication" value="<em><?php p($menuPubTitle); ?></em>">
				<select id="publicationSelect">
				    <?php if ($publicationsNo > 0) { ?>
				    <option value="0"><?php p($menuPubTitle); ?></option>
				    <?php
				              foreach($publications as $tmpPublication) { ?>
                    <option value="<?php echo $tmpPublication->getPublicationId(); ?>"><?php echo $tmpPublication->getName(); ?></option>
					<?php     }
					    } ?>
                </select>
            </label>
            <label id="contentIssueSectionSelectContainer">
				<input type="button" class="menuButton" id="issue" value="<?php putGS('All Issues'); ?>">
				<select id="issueSelect">
					<option value=""><?php putGS('All Issues'); ?></option>
				</select>

				<input type="button" class="menuButton" id="section" value="<?php putGS('All Sections'); ?>">
				<select id="sectionSelect">
				    <option value=""><?php putGS('All Sections'); ?></option>
				</select>
			</label>
			</div>
            <div id="searchContainer">
                <div id="autocomplete">
                    <label for="dt_input">Search Term: </label><input id="dt_input" type="text" value="" />
                    <div id="dt_ac_container"></div>
                </div>
		    </div>
		</div>
    </div>

    <div id="bd">
        <div id="controlsContainer">
            <div id="filterSelectContainer">
                <input type="button" class="menuButton" id="filter_type" value="<?php putGS('Filter by...'); ?>">
                <select id="actionSelect">
                    <option value=""><?php putGS('Filter by...'); ?></option>
                    <option value="author"><?php putGS('Author'); ?></option>
                    <option value="publish_date"><?php putGS('Date'); ?></option>
                    <option value="publish_range"><?php putGS('Date Range'); ?></option>
                    <option value="iduser"><?php putGS('Creator'); ?></option>
                    <option value="workflow_status"><?php putGS('Status'); ?></option>
                    <option value="topic"><?php putGS('Topic'); ?></option>
                    <option value="type"><?php putGS('Type'); ?></option>
                </select>
                <label id="filtermenubutton-container"></label>
                <div id="myAutoComplete">
                  <input id="myInput" type="text">
                  <div id="myContainer"></div>
                  <input id="myHidden" type="hidden">
                </div>
                <!--<label id="publish-date-single"></label>//-->
                <input type="button" id="publish-date-single" class="publish-date-single" name="publish-date-single" value="" />
                <input type="button" id="publish-date-range" class="publish-date-range" name="publish-date-range" value="" />
                <div id="cal1Container"></div>
                <div id="cal2Container"></div>
                <div id="dates">
                  <input type="hidden" name="dateTxt" id="dateTxt">
                  <input type="hidden" name="in" id="in">
                  <input type="hidden" name="out" id="out">
                </div>
            </div>

            <div id="actionSelectContainer">
                <input type="button" id="confColsPushButton" name="colsPushButton" value="<?php putGS('Show/Hide Columns'); ?>" />
                <div id="dt-dlg" class="inprogress">
                    <div class="hd"><?php putGS('Choose which columns you would like to see'); ?></div>
                    <div id="dt-dlg-picker" class="bd"></div>
                </div>
                <input type="button" class="menuButton" id="action" value="<?php putGS('Actions...'); ?>">
                <select id="actionSelect">
                    <option value="none"><?php putGS('Actions...'); ?></option>
                    <option value="workflow_publish"><?php putGS('Status: Publish'); ?></option>
                    <option value="workflow_submit"><?php putGS('Status: Submit'); ?></option>
                    <option value="workflow_new"><?php putGS('Status: Set New'); ?></option>
                    <option value="switch_onfrontpage"><?php putGS("Toggle: 'On Front Page'"); ?></option>
                    <option value="switch_onsectionpage"><?php putGS("Toggle: 'On Section Page'"); ?></option>
                    <option value="switch_comments"><?php putGS("Toggle: 'Comments'"); ?></option>
                    <!--<option value="schedule_publish"><?php putGS('Publish Schedule'); ?></option>//-->
                    <option value="unlock"><?php putGS('Unlock'); ?></option>
                    <option value="delete"><?php putGS('Delete'); ?></option>
                    <option value="duplicate"><?php putGS('Duplicate'); ?></option>
                    <option value="duplicate_interactive"><?php putGS('Duplicate to another section'); ?></option>
                    <option value="move"><?php putGS('Move'); ?></option>
                </select>
            </div>
		</div>

        <div id="dataPaginator1"><!-- The Paginator widget is rendered here --></div>
		<div id="articlesTable"><!-- The DataTable widget is rendered here --></div>
		<div id="dataPaginator2"><!-- The Paginator widget is rendered here --></div>
	</div>
</div>

<style>
@import url(<?php echo $Campsite['WEBSITE_URL']; ?>/css/adm/jquery-ui-1.8.5.custom.css);
</style>

<table cellpadding="0" cellspacing="0" class="datatable">
<thead>
    <tr>
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
        <th><?php echo putGS('Last Modified'); ?></th>
        <th><?php echo putGS('Publish Date'); ?></th>
        <th><?php echo putGS('Create Date'); ?></th>
    </tr>
</thead>
<tbody>
    <tr>
        <td colspan="14"><?php putGS('Loading data'); ?></td>
    </tr>
</tbody>
</table>

<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery.dataTables.min.js"></script>
<script type="text/javascript">
$('table.datatable').dataTable({
    'bProcessing': true,
    'bServerSide': true,
    'sAjaxSource': './assets/php/dynamicfilter/data.php',
    'bJQueryUI': true,
    'sPaginationType': 'full_numbers',
    'aoColumnDefs': [
        { 'sName': 'name', 'aTargets': [ 0 ] },
        { 'sName': 'type', 'aTargets': [ 1 ] },
        { 'sName': 'created_by', 'aTargets': [ 2 ] },
        { 'sName': 'author', 'aTargets': [ 3 ] },
        { 'sName': 'status', 'aTargets': [ 4 ] },
        { 'sName': 'on_front_page', 'aTargets': [ 5 ] },
        { 'sName': 'on_section_page', 'aTargets': [ 6 ] },
        { 'sName': 'images', 'aTargets': [ 7 ] },
        { 'sName': 'topics', 'aTargets': [ 8 ] },
        { 'sName': 'comments', 'aTargets': [ 9 ] },
        { 'sName': 'reads', 'aTargets': [ 10 ] },
        { 'sName': 'last_modified', 'aTargets': [ 11 ] },
        { 'sName': 'publish_date', 'aTargets': [ 12 ] },
        { 'sName': 'created_date', 'aTargets': [ 13 ] },
    ]
});
</script>

<?php camp_html_copyright_notice(); ?>
