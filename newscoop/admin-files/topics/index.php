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

<form action="" method="post">
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
    <input type="text" name="search" class="autocomplete topics" /> <input type="submit" name="search_submit" value="<?php putGS('Search'); ?>" />
</fieldset>

<?php  if ($g_user->hasPermission('ManageTopics')) { ?>
<form method="post" action="/<?php echo $ADMIN; ?>/topics/do_add.php" onsubmit="return validate(this);">
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
	<p><?php putGS('No topics'); ?></p>
</blockquote>

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

        <div class="item"><div>
            <a class="icon delete" href="<?php p("/$ADMIN/topics/do_del.php?f_topic_delete_id=".$currentTopic->getTopicId()."&amp;f_topic_language_id=$topicLanguageId"); ?>&amp;<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the topic $1?', htmlspecialchars($topicName)); ?>');" title="<?php putGS("Delete"); ?>"><span></span>x</a>
            <a class="edit" title="<?php putGS('Edit'); ?>"><?php putGS('Edit'); ?></a>

            <span class="open" title="<?php putGS('Click to edit'); ?>">
                <span><?php echo $topicLanguage->getCode(); ?></span>
                <strong><?php echo htmlspecialchars($topicName); ?></strong>
            </span>

            <form method="post" action="/<?php echo $ADMIN; ?>/topics/do_edit.php" onsubmit="return validate(this);">
                <?php echo SecurityToken::FormParameter(); ?>
	            <input type="hidden" name="f_topic_edit_id" value="<?php echo $topicId; ?>" />
	            <input type="hidden" name="f_topic_language_id" value="<?php  echo $topicLanguageId; ?>" />

            <fieldset class="name">
                <legend><?php  putGS("Change topic name"); ?></legend>
                <input type="text" class="input_text" name="f_name" value="<?php echo htmlspecialchars($topicName); ?>" size="32" maxlength="255"  title="<?php putGS('You must fill in the $1 field.',getGS('Name')); ?>" />
	            <input type="submit" class="button" name="Save" value="<?php  putGS('Save'); ?>" />
            </fieldset>
            </form>

            <form method="post" action="/<?php echo $ADMIN; ?>/topics/do_add.php" onsubmit="return validate(this);">
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
                <form method="post" action="/<?php echo $ADMIN; ?>/topics/do_add.php" onsubmit="return validate(this);">
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
        <input type="hidden" name="position[<?php echo $currentTopic->getTopicId(); ?>]" />

    <?php
    } // foreach
}
echo str_repeat('</li></ul>', $level);
?>

    <form method="post" action="/<?php echo $ADMIN; ?>/topics/do_order.php">
    <?php echo SecurityToken::FormParameter(); ?>
    <input type="hidden" name="languages" value="<?php echo implode('_', $f_show_languages); ?>" />
<fieldset class="buttons">
    <input type="submit" name="Save" value="<?php putGS('Save order'); ?>" />
    <input type="reset" name="Reset" value="<?php putGS('Reset order'); ?>" />
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

// show/hide interaction
$('ul.tree.sortable .item').each(function() {
    var fieldsets = $('fieldset', $(this));
    fieldsets.hide();

    $('.edit', $(this)).click(function() {
        if (sorting) {
            return; // ignore
        }
        fieldsets.toggle();

        // blank space workaround
        var li = fieldsets.closest('li').first();
        $('> ul', li).detach().appendTo(li);

        return false;
    });

    var subtopics = $(this).nextAll('ul');
    subtopics.hide();

    if (subtopics.length == 0) {
        return;
    }

    $('.open', $(this)).click(function() {
        subtopics.toggle();
        $('> .item .open', $(this).closest('li'))
            .toggleClass('closed')
            .toggleClass('opened');
    }).addClass('closed');
});

// make tree sortable
var orderChanges = {};
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
    },
    update: function(event, ui) {
        $('fieldset.buttons').addClass('active');
        var parentId = ui.item.closest('ul').closest('li').attr('id');
        if (!parentId) {
            parentId = 'topic_0';
        }
        orderChanges[parentId] = ui.item.closest('ul').sortable('toArray');
    },
});

// reset
$('input:reset').click(function() {
    window.location.reload();
});

// save
$('form[action*=do_order]').submit(function(e) {
    e.preventDefault();
    callServer(['Topic', 'UpdateOrder'], [
        orderChanges,
        ], function(json) {
            $('fieldset.buttons').removeClass('active');
            flashMessage('Order saved');
        });
    return false;
});

// check for changes before reload
$('ul.sortable input:submit, ul.sortable a.delete').click(function() {
    if ($('fieldset.buttons').hasClass('active')) {
        return confirm('<?php putGS('Order changes will be lost. Are you sure you want to continue?'); ?>');
    }
});

}); // /document.ready

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
        flashMessage(emsg.join("<br />\n"), 'error');
        return false;
    }

    return true;
}

--></script>
<?php } ?>
<?php camp_html_copyright_notice(); ?>
</body>
</html>
