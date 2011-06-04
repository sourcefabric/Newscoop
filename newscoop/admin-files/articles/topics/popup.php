<?php
camp_load_translation_strings("article_images");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/topics/topic_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Topic.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTopic.php');

if (!$g_user->hasPermission("AttachTopicToArticle")) {
	$errorStr = getGS('You do not have the right to attach topics to articles.');
	camp_html_display_error($errorStr, null, true);
	exit;
}

$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
	exit;
}

$topics = Topic::GetTree();
$articleTopics = ArticleTopic::GetArticleTopics($f_article_number);
$selectedIds = array();
foreach ($articleTopics as $topic) {
    $selectedIds[(int) $topic->getTopicId()] = TRUE;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Expires" content="now" />
  <title><?php putGS("Attach Topic To Article"); ?></title>
  
<?php include dirname(__FILE__) . '/../../html_head.php' ?>
</head>
<body class="pop-up">
<?php if (count($topics) > 0) { ?>
<form action="/<?php echo $ADMIN; ?>/articles/topics/do_edit.php" method="post">
<?php echo SecurityToken::FormParameter(); ?>
<input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>" />
<input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>" />
<div class="fixed-top">
<h1><?php putGS("Attach Topics"); ?></h1>

<fieldset class="buttons">
	<input type="submit" value="<?php putGS("Close"); ?>" class="button right-floated" onclick="parent.$.fancybox.close(); return false;" />
	<input type="submit" value="<?php putGS("Save and Close"); ?>" class="button right-floated" />
    <input type="text" name="search" class="autocomplete topics input_text" />
    <input type="submit" class="default-button" value="<?php putGS('Search'); ?>" />
</fieldset>
</div>

<?php
$color = FALSE;
$level = 0;
foreach ($topics as $path) {
    $topic_level = 0;
    foreach ($path as $topicObj) {
        $topic_level++;
    }

    if ($topic_level > $level) {
        echo empty($level) ? '<ul class="tree">' : '<ul>';
    } else {
        echo str_repeat('</li></ul>', $level - $topic_level), '</li>';
    }

	$currentTopic = camp_array_peek($path, false, -1);
    $topic_id = $currentTopic->getTopicId();
	$name = $currentTopic->getName($f_language_selected);
	if (empty($name)) {
		// Backwards compatibility
		$name = $currentTopic->getName(1);
		if (empty($name)) {
			continue;
		}
	}

    $color_class = $color && $topic_level == 1 ? ' class="odd"' : '';
    if ($topic_level == 1) {
        $color = !$color;
    }

    $checked_str = '';
    if (!empty($selectedIds[$currentTopic->getTopicId()])) {
        $checked_str = ' checked="checked"';
    }
?>

    <li<?php echo $color_class; ?>>
        <input id="f_topic_ids-<?php echo $topic_id; ?>" type="checkbox" name="f_topic_ids[]" value="<?php echo $topic_id; ?>"<?php echo $checked_str; ?> />
        <label for="f_topic_ids-<?php echo $topic_id; ?>"><?php echo $name; ?></label>
	<?php
    $level = $topic_level;
}
echo str_repeat('</li></ul>', $level);
?>

<p></p>
<p></p>

</form>

<script type="text/javascript">
$(document).ready(function() {
    $('ul.tree ul').hide(); // hide ul's
    $('ul.tree li').each(function() {
        if ($(this).children('ul').length > 0) {
            $(this).prepend('<a>+</a>');
        } else {
            $(this).prepend('<span>&nbsp;</span>');
        }
    });
    $('input[checked=checked]').each(function() {
        $(this).parents('ul').show();
        $(this).parents('ul').first().each(function() {
            $(this).parents('li').each(function() {
                $(this).children('a').first().text('-');
            });
        }); // but show witch checked inputs
    });
    $('ul.tree a').click(function() {
        if ($(this).nextAll('ul').length == 0) {
            return;
        }
        $(this).nextAll('ul').toggle('medium');
        if ($(this).text() == '+') {
            $(this).text('-');
        } else {
            $(this).text('+');
        }
    });
});
</script>

<?php } else { ?>
<div class="fixed-top">
<h1><?php putGS("Attach Topics"); ?></h1>
</div>
<p><?php putGS('No topics have been created yet.'); ?></p>

<?php } ?>

</body>
</html>
