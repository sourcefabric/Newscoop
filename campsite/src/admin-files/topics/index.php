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

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

camp_html_display_msgs("0.5em", 0);
?>
<script>
function checkAllLang()
{
	<?php foreach ($allLanguages as $tmpLanguage) { ?>
	document.getElementById("checkbox_<?php p($tmpLanguage->getLanguageId()); ?>").checked = true;
	<?php } ?>
} // fn checkAllLang


function uncheckAllLang()
{
	<?php foreach ($allLanguages as $tmpLanguage) { ?>
	document.getElementById("checkbox_<?php p($tmpLanguage->getLanguageId()); ?>").checked = false;
	<?php } ?>
} // fn uncheckAllLang
</script>

<P>
<FORM action="index.php" method="POST">
<table class="table_input">
<tr>
	<td>
		<table cellpadding="1" cellspacing="3"><tr>
		<td><b><?php putGS("Show languages:"); ?></b></td>
		<td><input type="button" value="<?php putGS("Select All"); ?>" onclick="checkAllLang();" class="button" style="font-size: smaller;"></td>
		<td><input type="button" value="<?php putGS("Select None"); ?>" onclick="uncheckAllLang();" class="button" style="font-size:smaller;"></td>
		</tr></table>
	</td>
</tr>
<tr>
	<td >
		<table cellpadding="0">
		<tr>
		<?php
		foreach ($allLanguages as $tmpLanguage) {
			?>
			<td style="padding-left: 5px;">
				<input type="checkbox" name="f_show_languages[]" value="<?php p($tmpLanguage->getLanguageId()); ?>" id="checkbox_<?php p($tmpLanguage->getLanguageId()); ?>" <?php if (in_array($tmpLanguage->getLanguageId(), $f_show_languages)) { echo "checked"; } ?>>
			</td>
			<td>
				<?php p(htmlspecialchars($tmpLanguage->getCode())); ?>
			</td>
			<?php
		}
		?>
			<td style="padding-left: 10px;">
				<input type="submit" name="f_show" value="<?php putGS("Show"); ?>" class="button">
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
</FORM>

<p>
<?php  if ($g_user->hasPermission("ManageTopics")) { ?>
<form method="POST" action="do_add.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<input type="hidden" name="f_topic_parent_id" value="0">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="box_table">
<TR>
	<TD ALIGN="LEFT">
		<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="1">
		<TR>
			<TD valign="middle"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></TD>
			<TD valign="middle"><B><?php  putGS("Add root topic:"); ?></B></TD>
			<td valign="middle">
				<SELECT NAME="f_topic_language_id" class="input_select" alt="select" emsg="<?php putGS("You must select a language."); ?>">
				<option value="0"><?php putGS("---Select language---"); ?></option>
				<?php
			 	foreach ($allLanguages as $tmpLanguage) {
			 		camp_html_select_option($tmpLanguage->getLanguageId(),
			 								$loginLanguageId,
			 								$tmpLanguage->getNativeName());
		        }
				?>
				</SELECT>
			</td>
			<td>
				<input type="text" name="f_topic_name" value="" class="input_text" size="20" alt="blank" emsg="<?php putGS('You must enter a name for the topic.'); ?>">
			</td>
			<td valign="middle">
				<input type="submit" name="add" value="<?php putGS("Add"); ?>" class="button">
			</td>
		</TR>
		</TABLE>
	</TD>
</TABLE>
</form>
<?php  } ?>

<p>
<?PHP
if (count($topics) == 0) { ?>
	<BLOCKQUOTE>
	<LI><?php  putGS('No topics'); ?></LI>
	</BLOCKQUOTE>
	<?php
} else {
?>
<script>
var topic_ids = new Array;
</script>

<form method="POST" action="do_order.php" onsubmit="return updateOrder(this);">
<?php echo SecurityToken::FormParameter(); ?>
<fieldset class="buttons">
    <input type="submit" name="Save" value="<?php putGS('Save order'); ?>" ?>
</fieldset>
</form>

<?php

$counter = 0;
$color= 0;
$isFirstTopic = true;
$aTopicOrder = array();
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

	$currentTopic = camp_array_peek($topicPath, false, -1);
	$parentId = $currentTopic->getParentId();

    echo '<li id="topic_', $currentTopic->getTopicId(), '">';
    echo '<input type="hidden" name="position[', $parentId, '][', $currentTopic->getTopicId(), ']" />';

	if (!isset($aTopicOrder[$parentId])) {
	    $sql = 'SELECT DISTINCT(TopicOrder) FROM Topics'
	        .' WHERE ParentId = '.$parentId
	        .' ORDER BY TopicOrder ASC, LanguageId ASC';
	    $aTopicOrder[$parentId] = $g_ado_db->GetCol($sql);
    }

	$isFirstTranslation = true;
    $topicTranslations = $currentTopic->getTranslations();
	foreach ($topicTranslations as $topicLanguageId => $topicName) {
		if (!in_array($topicLanguageId, $f_show_languages)) {
			continue;
		}

        $topicLanguage = new Language($topicLanguageId);
        echo '<div><h3>';
        echo '<span class="lang">', $topicLanguage->getCode(), '</span>';
        echo " <a href='/$ADMIN/topics/edit.php
            ?f_topic_edit_id=".$currentTopic->getTopicId()."
            &f_topic_language_id=$topicLanguageId'>
            ".htmlspecialchars($topicName)."</a>";
        echo '</h3>';
	?>
	    <div class="subtopic">
            <form method="POST" action="do_add.php" onsubmit="return validate(this);">
                <?php echo SecurityToken::FormParameter(); ?>
                <input type="hidden" name="f_topic_parent_id" value="<?php p($currentTopic->getTopicId()); ?>">
                <input type="hidden" name="f_topic_language_id" value="<?php p($topicLanguageId); ?>">

            <fieldset>
                <legend><?php putGS("Add subtopic:"); ?></legend>
                <label><?php p($topicLanguage->getNativeName()); ?></label>
                <input type="text" name="f_topic_name" value="" class="input_text" size="15" alt="blank" emsg="<?php putGS('You must enter a name for the topic.'); ?>" />
                <input type="submit" name="f_submit" value="<?php putGS("Add"); ?>" class="button" />
            </fieldset>

            </form>
        </div>

        <?php if ($isFirstTranslation) {
            $isFirstTranslation = false;
        ?>
        <div class="translate">
            <form method="POST" action="do_add.php" onsubmit="return validate(this);">
                <?php echo SecurityToken::FormParameter(); ?>
                <input type="hidden" name="f_topic_id" value="<?php p($currentTopic->getTopicId()); ?>">

            <fieldset>
                <legend><?php putGS("Add translation:"); ?></legend>
                <select name="f_topic_language_id" class="input_select" alt="select" emsg="<?php putGS("You must select a language."); ?>">
                    <option value="0"><?php putGS("---Select language---"); ?></option>
                    <?php foreach ($allLanguages as $tmpLanguage) {
                    camp_html_select_option($tmpLanguage->getLanguageId(),
                                            null, $tmpLanguage->getNativeName());
                    } ?>
                </select>
                <input type="text" name="f_topic_name" value="" class="input_text" size="15" alt="blank" emsg="<?php putGS('You must enter a name for the topic.'); ?>" />
                <input type="submit" name="f_submit" value="<?php putGS("Translate"); ?>" class="button" />
            </fieldset>
            
            </form>
        </div>
        <?php } ?>

		<a class="delete" href="<?php p("/$ADMIN/topics/do_del.php?f_topic_delete_id=".$currentTopic->getTopicId()."&f_topic_language_id=$topicLanguageId"); ?>&<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the topic $1?', htmlspecialchars($topicName)); ?>');" title="<?php putGS("Delete"); ?>"><?php putGS("Delete"); ?></a>
        </div>

    <?php
    $isFirstTopic = false;
    $counter++;
    $level = $topic_level;
    }
}
echo str_repeat('</li></ul>', $level);
?>

<form method="POST" action="do_order.php" onsubmit="return updateOrder(this);">
<?php echo SecurityToken::FormParameter(); ?>
<fieldset class="buttons">
    <input type="submit" name="Save" value="<?php putGS('Save order'); ?>" ?>
</fieldset>
</form>

<style>
@import url(<?php echo $Campsite['WEBSITE_URL']; ?>/css/adm/jquery-ui-1.8.5.custom.css);
</style>

<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery-ui-1.8.5.custom.min.js"></script>
<script type="text/javascript">
$('ul.tree.sortable  fieldset legend').show().click(function() {
    $(this).nextAll().toggle();
    $(this).parent().toggleClass('closed');
}).nextAll().hide().parent().addClass('closed');

$('ul.tree.sortable').sortable();
$('ul.tree.sortable ul').sortable();
$('ul.tree.sortable > li').dblclick(function() {
    $(this).children('ul').toggle('slow');
}).children('ul').each(function() {
    var childrens = $(this).children('li').length;
    $(this).hide();
    $('div > h3', $(this).parent()).first().append(' <span class="sub">' + childrens + ' <?php echo putGS('Subtopics'); ?></span>');
});

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
        emsg.push(select.first().attr('emsg'));
    }

    if (!input.val()) {
        emsg.push(input.attr('emsg'));
    }

    if (emsg.length > 0) {
        alert(emsg.join("\n"));
        return false;
    }

    return true;
}
</script>
<?php } ?>
<?php camp_html_copyright_notice(); ?>
