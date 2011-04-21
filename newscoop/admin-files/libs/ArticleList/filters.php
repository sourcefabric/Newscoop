<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once $GLOBALS['g_campsiteDir'] . '/classes/Publication.php';
require_once $GLOBALS['g_campsiteDir'] . '/classes/Issue.php';
require_once $GLOBALS['g_campsiteDir'] . '/classes/Section.php';
require_once $GLOBALS['g_campsiteDir'] . '/classes/Topic.php';
require_once $GLOBALS['g_campsiteDir'] . '/classes/Author.php';

// get publications
$publications = Publication::GetPublications();
$publicationsNo = is_array($publications) ? sizeof($publications) : 0;
$menuPubTitle = $publicationsNo > 0 ? getGS('All Publications') : getGS('No publications found');

// get issues
$issues = Issue::GetIssues($this->publication, NULL);
$issuesNo = is_array($issues) ? sizeof($issues) : 0;
$menuIssueTitle = $issuesNo > 0 ? getGS('All Issues') : getGS('No issues found');



// get sections
$sections = array();
$section_objects = Section::GetSections($this->publication, $this->issue, $this->language);

foreach ($section_objects as $section) {
	if (!isset($sections[$section->getSectionNumber()])) {
		$sections[$section->getSectionNumber()] = $section;
	}
}

$sectionsNo = is_array($sections) ? sizeof($sections) : 0;
$menuSectionTitle = $sectionsNo > 0 ? getGS('All Sections') : getGS('No sections found');

$topics = array();
foreach (Topic::GetTree() as $topic) {
	$topic = array_pop($topic);
	$topics[$topic->getTopicId()] = $topic->getName($this->language);
}

?>
<div class="filters">
<fieldset class="filters"><legend><?php putGS('Filter'); ?></legend> <select
	name="publication" id="publication_filter">
	<?php if ($publicationsNo > 0) { ?>
	<option value="0"><?php p($menuPubTitle); ?></option>
	<?php foreach($publications as $tmpPublication) { ?>
	<option value="<?php echo $tmpPublication->getPublicationId(); ?>"><?php echo $tmpPublication->getName(); ?></option>
	<?php }
	} ?>
</select> <select name="issue" id="issue_filter">
<?php if ($issuesNo > 0) { ?>
	<option value="0"><?php p($menuIssueTitle); ?></option>
	<?php foreach($issues as $issue) { ?>
	<option
		value="<?php echo $issue->getPublicationId().'_'.$issue->getIssueNumber().'_'.$issue->getLanguageId(); ?>"><?php echo $issue->getName(); ?></option>
		<?php }
} ?>
</select> <select name="section" id="section_filter">
<?php if ($sectionsNo > 0) { ?>
	<option value="0"><?php p($menuSectionTitle); ?></option>
	<?php foreach($sections as $section) { ?>
	<option
		value="<?php echo $section->getPublicationId().'_'.$section->getIssueNumber().'_'.$section->getLanguageId().'_'.$section->getSectionNumber(); ?>"><?php echo $section->getName(); ?></option>
		<?php }
} ?>
</select>

<div class="extra">

<dl>
	<dt><label for="filter_date"><?php putGS('Publish date'); ?></label></dt>
	<dd><input id="filter_date" type="text" name="publish_date"
		class="date" /></dd>
</dl>
<dl>
	<dt><label for="filter_from"><?php putGS('Published after'); ?></label></dt>
	<dd><input id="filter_from" type="text" name="publish_date_from"
		class="date" /></dd>
</dl>
<dl>
	<dt><label for="filter_to"><?php putGS('Published before'); ?></label></dt>
	<dd><input id="filter_to" type="text" name="publish_date_to"
		class="date" /></dd>
</dl>
<dl>
	<dt><label for="filter_author"><?php putGS('Author'); ?></label></dt>
	<dd><select name="author">
		<option value=""><?php putGS('All'); ?></option>
		<?php foreach (Author::GetAuthors() as $author) { ?>
		<option value="<?php echo $author->getName(); ?>"><?php echo $author->getName(); ?></option>
		<?php } ?>
	</select></dd>
</dl>
<dl>
	<dt><label for="filter_creator"><?php putGS('Creator'); ?></label></dt>
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
		<?php foreach ($topics as $id => $topic) { ?>
		<option value="<?php echo $id; ?>"><?php echo $topic; ?></option>
		<?php } ?>
	</select></dd>
</dl>
<dl>
	<dt><label for="filter_language"><?php putGS('Language'); ?></label></dt>
	<dd><select id="filter_name" name="language">
		<option value=""><?php putGS('All'); ?></option>
		<?php foreach(Language::GetLanguages() as $language) { ?>
		<option value="<?php echo $language->getLanguageId(); ?>"><?php echo $language->getNativeName(); ?></option>
		<?php } ?>
	</select></dd>
</dl>
</div>
</fieldset>
</div>
<!-- /.smartlist-filters -->

		<?php if (!self::$renderFilters) { ?>
<script type="text/javascript">

function handleArgs() {
	if($('#filter_name').val() < 0) {
		langId = 0;
	} else {
		langId = $('#filter_name').val();
	}

	if($('#publication_filter').val() < 0) {
		publicationId = 0;
	} else {
		publicationId = $('#publication_filter').val();
	}

	if($('#issue_filter').val() < 0) {
		issueId = 0;
	} else {
		issueId = $('#issue_filter').val();
	}

	args = new Array();
	args.push({
        'name': 'language',
        'value': langId
    });
	args.push({
        'name': 'publication',
        'value': publicationId
    });
	args.push({
        'name': 'issue',
        'value': issueId
    });

    return args;
}

function handleFilterIssues(args) {

	var args = eval('(' + args + ')');
	$('#issue_filter >option').remove();
	$('#issue_filter').append($("<option></option>").val('0').html(args.menuItemTitle));

	var items = args.items;
	for(var i=0; i < items.length; i++) {
		var item = items[i];
		$('#issue_filter').append($("<option></option>").val(item.val).html(item.name));
	}
}

function handleFilterSections(args) {

	var args = eval('(' + args + ')');
	$('#section_filter >option').remove();
	$('#section_filter').append($("<option></option>").val('0').html(args.menuItemTitle));

	var items = args.items;
	for(var i=0; i < items.length; i++) {
		var item = items[i];
		$('#section_filter').append($("<option></option>").val(item.val).html(item.name));
	}
}

function refreshFilterIssues() {
	var args = handleArgs();
	callServer(['ArticleList', 'getFilterIssues'], args, handleFilterIssues);
}

function refreshFilterSections() {
	var args = handleArgs();
	callServer(['ArticleList', 'getFilterSections'], args, handleFilterSections);
}

$(document).ready(function() {
//handle language change first
$('#filter_name').change(function() {

	refreshFilterIssues();
	refreshFilterSections();
})

$('#publication_filter').change(function() {
	refreshFilterIssues();
	refreshFilterSections();
})

$('#issue_filter').change(function() {
	var smartlist = $(this).closest('.smartlist');
	var smartlistId = smartlist.attr('id').split('-')[1];
	filters[smartlistId]['section'] = 0;
	refreshFilterSections();
})



// filters handle
$('.smartlist .filters select, .smartlist .filters input').change(function() {
    var smartlist = $(this).closest('.smartlist');
    var smartlistId = smartlist.attr('id').split('-')[1];
    var name = $(this).attr('name');
    var value = $(this).val();
    filters[smartlistId][name] = value;
    if($(this).attr('id') == 'filter_name' || $(this).attr('id') == 'publication_filter' ) {
		filters[smartlistId]['issue'] = 0;
		filters[smartlistId]['section'] = 0;
	}
    tables[smartlistId].fnDraw(true);
    return false;
});


// datepicker for dates
$('input.date').datepicker({
    dateFormat: 'yy-mm-dd',
    maxDate: '+0d',
});

// filters managment
$('fieldset.filters .extra').each(function() {
    var extra = $(this);
    $('dl', extra).hide();
    $('<select class="filters"></select>')
        .appendTo(extra)
        .each(function() { // init options
            var select = $(this);
            $('<option value=""><?php putGS('Filter by...'); ?></option>')
                .appendTo(select);
            $('dl dt label', extra).each(function() {
                var label = $(this).text();
                $('<option value="'+label+'">'+label+'</option>')
                    .appendTo(select);
            });
        }).change(function() {
            var select = $(this);
            var value = $(this).val();
            $(this).val('');
            $('dl', $(this).parent()).each(function() {
                var label = $('label', $(this)).text();
                var option = $('option[value="' + label + '"]', select);
                if (label == value) {
                    $(this).show();
                    $(this).insertBefore($('select.filters', $(this).parent()));
                    if ($('a', $(this)).length == 0) {
                        $('<a class="detach">X</a>').appendTo($('dd', $(this)))
                            .click(function() {
                                $(this).parents('dl').hide();
                                $('input, select', $(this).parent()).val('').change();
                                select.change();
                                option.show();
                            });
                    }
                    option.hide();
                }
            });
    }); // change
});

$('fieldset.filters').each(function() {
    var fieldset = $(this);
    var smartlist = fieldset.closest('.smartlist');
    var smartlistId = smartlist.attr('id').split('-')[1];

    // reset all button
    var resetMsg = '<?php putGS('Reset all filters'); ?>';
    $('<a href="#" class="reset" title="'+resetMsg+'">'+resetMsg+'</a>')
        .appendTo(fieldset)
        .click(function() {
            // reset extra filters
            $('.extra dl', fieldset).each(function() {
                $(this).hide();
                $('select, input', $(this)).val('');
            });
            $('select.filters', fieldset).val('');
            $('select.filters option', fieldset).show();

            // reset main filters
            $('> select', fieldset).val('0').change();

            // redraw table
            filters[smartlistId] = {};
            tables[smartlistId].fnDraw(true);
            return false;
        });
});

}); // document.ready

</script>
		<?php } ?>
