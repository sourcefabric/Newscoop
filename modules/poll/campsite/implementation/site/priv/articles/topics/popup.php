<?PHP
camp_load_translation_strings("article_images");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/topics/topic_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Topic.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleTopic.php');

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

?>
<html>
<head>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<title><?php putGS("Attach Topic To Article"); ?></title>
</head>
<body>
<br>
<div class="page_title" style="padding-left: 18px;">
<?php putGS("Attach Topics"); ?>
</div>
<p></p>

<?php if (count($topics) > 0) { ?>
<FORM action="<?php p("/$ADMIN/articles/topics/do_add.php"); ?>" method="POST">
<INPUT type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
<INPUT type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
<table class="table_list">
<?PHP
$color = 0;
foreach ($topics as $path) {
	$currentTopic = camp_array_peek($path, false, -1);
	$name = $currentTopic->getName($f_language_selected);
	if (empty($name)) {
		// Backwards compatibility
		$name = $currentTopic->getName(1);
		if (empty($name)) {
			continue;
		}
	}
	?>
	<tr <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<td><input type="checkbox" name="f_topic_ids[]" value="<?php p($currentTopic->getTopicId()); ?>"></td>
		<td style="padding-left: 3px; padding-right: 5px;" width="400px">
			<?php
			foreach ($path as $topicObj) {
				$name = $topicObj->getName($f_language_selected);
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
<INPUT type="submit" value="<?php putGS("Save and Close"); ?>" class="button">
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