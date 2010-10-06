<?PHP
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
<!DOCTYPE html>
<html>
<head>
	<title><?php putGS("Attach Topic To Article"); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="Expires" CONTENT="now">
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery-1.4.2.min.js"></script>
</head>
<body>
<br>
<div class="page_title" style="padding-left: 18px;">
<?php putGS("Attach Topics"); ?>
</div>
<p></p>

<?php if (count($topics) > 0) { ?>
<form action="<?php p("/$ADMIN/articles/topics/do_edit.php"); ?>" method="POST">
<?php echo SecurityToken::FormParameter(); ?>
<input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
<input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">

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
<DIV class="action_buttons" align="center">
<INPUT type="submit" value="<?php putGS("Save and Close"); ?>" class="button">
&nbsp;&nbsp;&nbsp;<INPUT type="submit" value="<?php putGS("Cancel"); ?>" class="button" onclick="window.close();">
</div>
</FORM>
<p></p>

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
	<BLOCKQUOTE>
	<LI><?php  putGS('No topics.'); ?></LI>
	</BLOCKQUOTE>
	<DIV align="center" style="padding-top: 20px;">
	<INPUT type="submit" value="<?php putGS("Close"); ?>" class="button" onclick="window.close();">
	</div>
<?php } ?>
</body>
</html>
