<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/topics/topic_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Topic.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTopic.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!$g_user->hasPermission("AttachTopicToArticle")) {
	$errorStr = $translator->trans('You do not have the right to attach topics to articles.', array(), 'article_topics');
	camp_html_display_error($errorStr, null, true);
	exit;
}

$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $_SERVER['REQUEST_URI'], true);
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
  <title><?php echo $translator->trans("Attach Topic To Article", array(), 'article_topics'); ?></title>

<?php include dirname(__FILE__) . '/../../html_head.php' ?>
</head>
<body class="pop-up">
<?php if (count($topics) > 0) { ?>
<form id="topicsForm" action="/<?php echo $ADMIN; ?>/articles/topics/do_edit.php" method="post">
<?php echo SecurityToken::FormParameter(); ?>
<input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>" />
<div class="fixed-top">
<h1><?php echo $translator->trans("Attach Topics", array(), 'article_topics'); ?></h1>

<fieldset class="buttons">
	<input type="submit" value="<?php echo $translator->trans("Save and Close"); ?>" class="button right-floated" />
        <input type="submit" value="<?php echo $translator->trans("Close"); ?>" class="button right-floated" onclick="parent.$.fancybox.close(); return false;" />
    <input type="text" name="search" class="autocomplete topics input_text" />
    <input type="button" class="default-button" value="<?php echo $translator->trans('Search'); ?>" />
    <input type="button" class="default-button" value="<?php echo $translator->trans('Show All', array(), 'article_topics'); ?>" id="show_all_topics" style="padding: 3px 0px;"/>
    <?php if ($g_user->hasPermission('ManageTopics')) { ?>
    <input type="button" class="default-button" value="<?php echo $translator->trans('Add new topic', array(), 'article_topics'); ?>" id="add_new_topic" style="padding: 3px 0px;"/>
    <div style="width:100%; margin-top:10px;display:none" id="new_topic_holder">
	    <?php echo $translator->trans('Select the parent of the topic', array(), 'article_topics'); ?>
	    <select name="f_topic_parent_id" id="f_topic_parent_id">
	    <option value="0"><?php echo $translator->trans('None', array(), 'article_topics');?></option>
	    <?php
	    $level = 0;
	    
	    foreach ($topics as $path) {
	        $topic_level = 0;
	        foreach ($path as $topicObj) {
	            $topic_level++;
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
	        ?>
	        <option value="<?php echo $topic_id; ?>"><?php echo str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $topic_level). $this->view->escape($name);?></option>
	        <?php
	        $level = $topic_level;
	    }
	    
	    ?>
	    </select>
        <select name="f_language_selected" id="f_language_selected">
            <?php
            $languages = Language::GetLanguages(null, null, null, array(), array(), true);
            foreach ($languages as $language) {
                if ($f_language_selected == $language->getLanguageId()) {
                    echo("<option value='". $language->getLanguageId() ."' selected='selected'>". $this->view->escape($language->getName()) ."</option>");
                }
                else {
                    echo("<option value='". $language->getLanguageId() ."'>". $this->view->escape($language->getName()) ."</option>");
                }
            }
            ?>
        </select>
	    <input type="text" name="f_topic_name" id="f_topic_name" value="" class="input_text" size="20" title="<?php echo $translator->trans('You must enter a name for the topic.', array(), 'article_topics'); ?>" style="width: 360px"/>
	    <input type="button" name="add" value="<?php echo $translator->trans("Add"); ?>" class="button" id='submit_new_topic'/>
    </div>
    <?php } else { ?>
    <input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>" />
    <?php } ?>
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

    if ($topic_level > $level) {
        echo empty($level) ? '<ul class="tree">' : '<ul>';
    } else {
        echo str_repeat('</li></ul>', $level - $topic_level), '</li>';
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
        <label for="f_topic_ids-<?php echo $topic_id; ?>"><?php echo $this->view->escape($name); ?></label>
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
    //add new topic
    $('#add_new_topic').click(function() {
        $('#new_topic_holder').toggle();
    });
});

<?php if ($g_user->hasPermission('ManageTopics')) { ?>
$('#submit_new_topic').click(function(){
    var f_topic_name = $('#f_topic_name').val();
    if (f_topic_name.length == 0) {
        flashMessage("<?php echo $translator->trans('You must enter a name for the topic.', array(), 'article_topics'); ?>", 'error');
        $('#f_topic_name').focus();
    } else {
        var f_topic_parent_id = $('#f_topic_parent_id').val();
        var f_language_selected = $('#f_language_selected').val();

        var topicParams = {};
        topicParams['f_topic_name'] = f_topic_name;
        topicParams['f_topic_parent_id'] = f_topic_parent_id;
        topicParams['f_language_selected'] = f_language_selected;
        callServer(['Topic', 'add'], [
                                              topicParams,
                                              ],
                                              function(json) {
            msg = eval( '(' + json + ')' );
            flashMessage(msg.message, msg.messageClass);
            $('#new_topic_holder').toggle();
            location.reload()
            });
    }
});
<?php } ?>
</script>

<?php } else { ?>
<div class="fixed-top">
<h1><?php echo $translator->trans("Attach Topics", array(), 'article_topics'); ?></h1>
</div>
<p><?php echo $translator->trans('No topics have been created yet.', array(), 'article_topics'); ?></p>

<?php } ?>

</body>
</html>
