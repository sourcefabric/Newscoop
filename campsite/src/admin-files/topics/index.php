<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/topics/topics_common.php");

$f_show_languages = camp_session_get('f_show_languages', array());

$topics = Topic::GetTree();
// return value is sorted by language
$allLanguages = Language::GetLanguages(null, null, null, array(), array(), true);

$loginLanguageId = 0;
$loginLanguage = Language::GetLanguages(null, camp_session_get('TOL_Language', 'en'), null, array(), array(), true);
if (is_array($loginLanguage) && count($loginLanguage) > 0) {
	$loginLanguage = array_pop($loginLanguage);
	$loginLanguageId = $loginLanguage->getLanguageId();
}

if (count($f_show_languages) <= 0) {
	$f_show_languages = DbObjectArray::GetColumn($allLanguages, 'Id');
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Topics"), "");
echo camp_html_breadcrumbs($crumbs);

camp_html_display_msgs("0.5em", 0);
?>

<form action="index.php" method="post">
<fieldset class="controls">
    <legend><?php putGS("Show languages"); ?></legend>
    <div class="buttons">
        <input type="button" value="<?php putGS("Select All"); ?>" onclick="checkAllLang(this);" class="button" />
        <input type="button" value="<?php putGS("Select None"); ?>" onclick="uncheckAllLang(this);" class="button" />
    </div>
    <div class="lang">
        <?php foreach ($allLanguages as $tmpLanguage) { ?>
        <input type="checkbox" name="f_show_languages[]" value="<?php p($tmpLanguage->getLanguageId()); ?>" id="checkbox_<?php p($tmpLanguage->getLanguageId()); ?>" <?php if (in_array($tmpLanguage->getLanguageId(), $f_show_languages)) { echo 'checked="checked"'; } ?> />
        <label for="checkbox_<?php echo $tmpLanguage->getLanguageId(); ?>">	<?php echo htmlspecialchars($tmpLanguage->getCode()); ?></label>
    	<?php } ?>
		<input type="submit" name="f_show" value="<?php putGS("Show"); ?>" class="button" />
    </div>
</fieldset>
</form>

<fieldset class="controls search">
    <legend><?php putGS('Search'); ?></legend>
    <input type="text" name="search" /> <input type="submit" name="search_submit" value="<?php putGS('Search'); ?>" />
</fieldset>

<?php  if ($g_user->hasPermission('ManageTopics')) { ?>
<form method="post" action="do_add.php" onsubmit="return validate(this);">
<?php echo SecurityToken::FormParameter(); ?>
<input type="hidden" name="f_topic_parent_id" value="0" />
<fieldset class="controls">
    <legend><?php  putGS("Add root topic"); ?></legend>
    <select name="f_topic_language_id" class="input_select" title="<?php putGS("You must select a language."); ?>">
        <option value="0"><?php putGS("---Select language---"); ?></option>
        <?php foreach ($allLanguages as $tmpLanguage) {
            camp_html_select_option($tmpLanguage->getLanguageId(),
                                    $loginLanguageId,
                                    $tmpLanguage->getNativeName());
        } ?>
	</select>

    <input type="text" name="f_topic_name" value="" class="input_text" size="20" title="<?php putGS('You must enter a name for the topic.'); ?>" />
    <input type="submit" name="add" value="<?php putGS("Add"); ?>" class="button" />
</fieldset>
</form>
<?php  } ?>

<?php
if (count($topics) == 0) { ?>
<blockquote>
	<p><?php  putGS('No topics'); ?></p>
</blockquote>>

<?php } else { ?>

<?php
$level = 0;
foreach ($topics as $topicPath) {
    $topic_level = 0;
    foreach ($topicPath as $topicObj) {
        $topic_level++;
    }

    if ($topic_level > $level) {
        echo empty($level) ? '<ul class="tree sortable">' : '<ul>';
    } else {
        echo str_repeat('</li></ul>', $level - $topic_level), '</li>';
    }
    $level = $topic_level;

	$currentTopic = camp_array_peek($topicPath, false, -1);
	$parentId = $currentTopic->getParentId();
?>

    <li id="topic_<?php echo $currentTopic->getTopicId() ?>">
    <input type="hidden" name="position[<?php echo $currentTopic->getTopicId(); ?>]" />

<?php
	$isFirstTranslation = true;
    $topicTranslations = $currentTopic->getTranslations();
	foreach ($topicTranslations as $topicLanguageId => $topicName) {
		if (!in_array($topicLanguageId, $f_show_languages)) {
			continue;
		}

        $topicLanguage = new Language($topicLanguageId);
        $topicId = $currentTopic->getTopicId();
?>

        <div><div class="item" title="<?php putGS('Click to hide/show sub-tree. Drag to change order.'); ?>">
            <span class="lang" title="<?php putGS('Drag to change order'); ?>"><?php echo $topicLanguage->getCode(); ?></span>
            <strong title="<?php putGS('Click to edit'); ?>"><?php echo htmlspecialchars($topicName); ?></strong>

            <a class="delete" href="<?php p("/$ADMIN/topics/do_del.php?f_topic_delete_id=".$currentTopic->getTopicId()."&amp;f_topic_language_id=$topicLanguageId"); ?>&amp;<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the topic $1?', htmlspecialchars($topicName)); ?>');" title="<?php putGS("Delete"); ?>">
                <?php putGS("Delete"); ?>
            </a>

            <form method="post" action="do_edit.php" onsubmit="return validate(this);">
                <?php echo SecurityToken::FormParameter(); ?>
	            <input type="hidden" name="f_topic_edit_id" value="<?php echo $topicId; ?>" />
	            <input type="hidden" name="f_topic_language_id" value="<?php  echo $topicLanguageId; ?>" />

            <fieldset class="name">
                <legend><?php  putGS("Change topic name"); ?></legend>
                <input type="text" class="input_text" name="f_name" value="<?php echo htmlspecialchars($topicName); ?>" size="32" maxlength="255"  title="<?php putGS('You must fill in the $1 field.',getGS('Name')); ?>" />
	            <input type="submit" class="button" name="Save" value="<?php  putGS('Save'); ?>" />
            </fieldset>
            </form>

            <form method="post" action="do_add.php" onsubmit="return validate(this);">
                <?php echo SecurityToken::FormParameter(); ?>
                <input type="hidden" name="f_topic_parent_id" value="<?php p($currentTopic->getTopicId()); ?>" />
                <input type="hidden" name="f_topic_language_id" value="<?php p($topicLanguageId); ?>" />
            

            <fieldset class="subtopic">
                <legend><?php putGS("Add subtopic:"); ?></legend>
                <label><?php p($topicLanguage->getNativeName()); ?></label>
                <input type="text" name="f_topic_name" value="" class="input_text" size="15" title="<?php putGS('You must enter a name for the topic.'); ?>" />
                <input type="submit" name="f_submit" value="<?php putGS("Add"); ?>" class="button" />
            </fieldset>
            </form>

            <?php if ($isFirstTranslation) {
                $isFirstTranslation = false;
            ?>
            <form method="post" action="do_add.php" onsubmit="return validate(this);">
                <?php echo SecurityToken::FormParameter(); ?>
                <input type="hidden" name="f_topic_id" value="<?php p($currentTopic->getTopicId()); ?>" />

            <fieldset class="translate">
                <legend><?php putGS("Add translation:"); ?></legend>
                <select name="f_topic_language_id" class="input_select" title="<?php putGS("You must select a language."); ?>">
                    <option value="0"><?php putGS("---Select language---"); ?></option>
                    <?php foreach ($allLanguages as $tmpLanguage) {
                    camp_html_select_option($tmpLanguage->getLanguageId(),
                                            null, $tmpLanguage->getNativeName());
                    } ?>
                </select>
                <input type="text" name="f_topic_name" value="" class="input_text" size="15" title="<?php putGS('You must enter a name for the topic.'); ?>" />
                <input type="submit" name="f_submit" value="<?php putGS("Translate"); ?>" class="button" />
            </fieldset>
            </form>
            <?php } ?>
        </div></div>

    <?php
    } // foreach
}
echo str_repeat('</li></ul>', $level);
?>

<form method="post" action="do_order.php" onsubmit="return updateOrder(this);">
    <?php echo SecurityToken::FormParameter(); ?>
    <input type="hidden" name="languages" value="<?php echo implode('_', $f_show_languages); ?>" />
<fieldset class="buttons">
    <input type="submit" name="Save" value="<?php putGS('Save order'); ?>" />
</fieldset>
</form>

<script type="text/javascript"><!--
/**
 * Check all checkboxes within same fieldset.
 * @param object elem
 */
function checkAllLang(elem)
{
    $('input[type=checkbox]', $(elem).parents('fieldset')).attr('checked', 'checked');
}

/**
 * Uncheck all checkboxes within same fieldset.
 * @param object elem
 */
function uncheckAllLang(elem)
{
    $('input[type=checkbox]', $(elem).parents('fieldset')).removeAttr('checked');
}

$(document).ready(function() {

var sorting = false;

// add classes for styling
$('ul.tree.sortable li').each(function() {
    $(this).children('div').first().addClass('first');
    $(this).children('div').last().addClass('last');
});

// hide subtopics
$('ul.tree.sortable > li > ul').hide();

// show subtopics on click
$('ul.tree.sortable div.item').click(function() {
    if (sorting) {
        return; // ignore
    }
    $(this).parent().siblings('ul').toggle();
});

// add subtupics count
$('ul.tree.sortable > li').each(function() {
    var count = $('li', $(this)).length;
    if (count > 0) {
        $('.first > div.item', $(this)).first()
            .append('<span class="sub">' + count + ' <?php putGS('Subtopics'); ?></span>');
    }
});

// hide item forms
$('ul.tree.sortable fieldset').hide();

// show forms on click
$('ul.tree.sortable div.item strong').click(function() {
    if (sorting) {
        return; // ignore
    }
    $('fieldset', $(this).parent()).toggle();
    $(this).parent().toggleClass('active');
    return false;
});

// make tree sortable
$('ul.tree.sortable, ul.tree.sortable ul').sortable({
    revert: 100,
    distance: 5,
    start: function(event, ui) {
        sorting = true;
        ui.item.addClass('move');
    },
    stop: function(event, ui) {
        sorting = false;
        ui.item.removeClass('move');
        $('fieldset.buttons').addClass('active');
    }
});

}); // /document.ready

/**
 * Update one list order.
 *
 * @param object list
 * @param object form
 *
 * @return void
 */
function updateListOrder(list, form)
{
    var orderAry = list.sortable('toArray');
    for (var i = 0; i < orderAry.length; i++) {
        var elem = $('#' + orderAry[i] + ' > input[type=hidden]').first().val(i + 1);
        elem.appendTo(form);
    }
}

/**
 * Update order info in tree
 *
 * @return bool
 */
function updateOrder(form)
{
    updateListOrder($('ul.tree.sortable'), form);
    $('ul.tree.sortable ul').each(function() {
        updateListOrder($(this), form);
    });
    return true;
}

/**
 * Validate form.
 *
 * @param object form
 *
 * @return bool
 */
function validate(form)
{
    var select = $('select', form);
    var input = $('input[type=text]', form).first();
    var emsg = [];

    if (select.length && select.first().val() == 0) {
        emsg.push(select.first().attr('title'));
    }

    if (!input.val()) {
        emsg.push(input.attr('title'));
    }

    if (emsg.length > 0) {
        alert(emsg.join("\n"));
        return false;
    }

    return true;
}

// search
$('input[name=search]').change(function() {
    $('ul.tree.sortable *').removeClass('match');
    $('ul.tree.sortable > li').show();
    $('ul.tree.sortable').sortable('option', 'disabled', true);
    if ($(this).val() == "") {
        $('ul.tree.sortable').sortable('option', 'disabled', false);
        return;
    }
    var re = new RegExp($(this).val(), "i");
    $('ul.tree.sortable > li').each(function() {
        var li = $(this);
        $('strong', li).each(function() {
            if (!li.hasClass('match') && $(this).text().search(re) >= 0) {
                li.addClass('match');
                $(this).parent().addClass('match');
            }
        });
    });
    $('ul.tree.sortable > li').not('.match').hide();
    $('ul.tree.sortable > li.match > ul').show();
});
--></script>
<?php } ?>
<?php camp_html_copyright_notice(); ?>
</body>
</html>
