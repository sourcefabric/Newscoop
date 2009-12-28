<?PHP
camp_load_translation_strings("plugin_blog");
$f_mode = Input::Get('f_mode', 'string');


// User role depend on path to this file. Tricky: moderator folder is just symlink to admin files!
if (strpos($call_script, '/blog/admin/') !== false && $g_user->hasPermission('plugin_blog_admin')) {
    $is_admin = true;   
}
if (strpos($call_script, '/blog/moderator/') !== false && $g_user->hasPermission('plugin_blog_moderator')) {
    $is_moderator = true;
}

// Check permissions
if (!$is_admin && !$is_moderator) {
    camp_html_display_error(getGS('You do not have the right to manage blogs.'));
    exit;
}

switch ($f_mode) {
    case 'blog_topic':// Check permissions
        $f_blog_id = Input::Get('f_blog_id', 'int');
        $topics = Blog::GetTopicTree();
        $Blog = new Blog($f_blog_id);
        $language_id = $Blog->getLanguageId();
        $object = 'BlogTopic';
        $object_id = $f_blog_id;
    break;
    
    case 'entry_topic':
        $f_blogentry_id = Input::Get('f_blogentry_id', 'int');
        $topics = Blog::GetTopicTree();
        $BlogEntry = new BlogEntry($f_blogentry_id);
        $language_id = $BlogEntry->getLanguageId();
        $object = 'BlogentryTopic';
        $object_id = $f_blogentry_id;
    break;   
    
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
	exit;
}
?>
<html>
<head>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<title><?php putGS("Topics"); ?></title>
</head>
<body>
<br>
<div class="page_title" style="padding-left: 18px;">
<?php putGS("Topics"); ?>
</div>
<p></p>

<?php if (count($topics) > 0) { ?>

<FORM action="do_edit.php" method="POST">
<INPUT type="hidden" name="f_mode" value="<?php p($f_mode); ?>">
<INPUT type="hidden" name="f_blog_id" value="<?php p($f_blog_id); ?>">
<INPUT type="hidden" name="f_blogentry_id" value="<?php p($f_blogentry_id); ?>">

<table class="table_list">
<?PHP
$color = 0;
foreach ($topics as $path) {
	$currentTopic = camp_array_peek($path, false, -1);
	$name = $currentTopic->getName($language_id);
	if (empty($name)) {
		// Backwards compatibility
		$name = $currentTopic->getName(1);
		if (empty($name)) {
			continue;
		}
	}
	
	$$object = new $object($object_id, $currentTopic->getTopicId());
	$checked = $$object->exists() ? 'checked="checked"' : '';	
	?>
	<tr <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<td><input type="checkbox" name="f_topic_ids[]" value="<?php p($currentTopic->getTopicId()); ?>" <?php p($checked); p($is_admin ? '' : ' disabled'); ?>></td>
		<td style="padding-left: 3px; padding-right: 5px;" width="400px">
			<?php
			foreach ($path as $topicObj) {
				$name = $topicObj->getName($language_id);
				if (empty($name)) {
					$name = $topicObj->getName(1);
					if (empty($name)) {
						$name = "-----";
					}
				}
				echo " / ".htmlspecialchars($name);
			}
			?>
		</td>
	</tr>
	<?PHP
}
?>
</table>
<p></p>


<DIV class="action_buttons" align="center">
<?php if ($is_admin) { ?>
<INPUT type="submit" value="<?php putGS("Save and Close"); ?>" class="button">
<?php } ?>
&nbsp;&nbsp;&nbsp;<INPUT type="submit" value="<?php putGS("Cancel"); ?>" class="button" onclick="window.close();">
</div>
</FORM>


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