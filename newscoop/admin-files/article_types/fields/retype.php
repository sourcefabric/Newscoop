<?php
camp_load_translation_strings("article_type_fields");
camp_load_translation_strings("api");
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');
require_once($GLOBALS['g_campsiteDir']."/classes/Topic.php");

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to reassign a field type."));
	exit;
}

$articleTypeName = Input::Get('f_article_type');
$articleTypeFieldName = Input::Get('f_field_name');
$articleField = new ArticleTypeField($articleTypeName, $articleTypeFieldName);

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array($articleTypeName, '');
$crumbs[] = array(getGS("Article type fields"), "/$ADMIN/article_types/fields/?f_article_type=".urlencode($articleTypeName));
$crumbs[] = array(getGS("Reassign a field type"), "");

echo camp_html_breadcrumbs($crumbs);
include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

$lang = camp_session_get('LoginLanguageId', 1);
$languageObj = new Language($lang);

// Verify the merge rules
$options = array();
$convertibleFromTypes = $articleField->getConvertibleToTypes();
foreach ($convertibleFromTypes as $type) {
	$options[$type] = ArticleTypeField::VerboseTypeName($type, $languageObj->getLanguageId());
}
?>
<script>
function UpdateArticleFieldContext() {
	var my_form = document.forms["add_field_form"]
	var field_type = my_form.elements["f_article_field_type"].value
	var is_topic = my_form.elements["is_topic"].value
	if ((is_topic == "false" && field_type == "topic")
			|| (is_topic == "true" && field_type != "topic")) {
		ToggleRowVisibility('topic_list')
		ToggleBoolValue('is_topic')
	}
}
</script>


<?php if (count($options) < 1) { ?>
<P>
You cannot reassign this type.
</P>
<?php camp_html_copyright_notice(); ?>
<?php } else { ?>


<P>
<FORM NAME="add_field_form" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/article_types/fields/do_retype.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<input type="hidden" name="f_field_name" value="<?php print $articleTypeFieldName; ?>">
<input type="hidden" name="is_topic" id="is_topic" value="false">
<TABLE BORDER="" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Type"); ?>:</TD>
	<TD>
	<SELECT NAME="f_article_field_type" class="input_select" onchange="UpdateArticleFieldContext()">
        <?php foreach ($options as $k => $v) { ?>
        	<OPTION VALUE="<?php print $k; ?>"><?php echo $v; ?></OPTION>
        <?php } ?>
    </SELECT>


	</TD>
</TR>
<tr style="display: none;" id="topic_list">
	<td align="right"><?php putGS("Top element"); ?>:</td>
	<td>
		<select name="f_root_topic_id" class="input_select">
<?php
$TOL_Language = camp_session_get('LoginLanguageId', 1);
$lang = new Language($TOL_Language);
$currentLanguageId = $lang->getLanguageId();
//$currentLanguages = Language::GetLanguages(null, $TOL_Language);
//$currentLanguageId = $currentLanguages[0]->getLanguageId();
$topics = Topic::GetTree();
foreach ($topics as $topicPath) {
	$printTopic = array();
	foreach ($topicPath as $topicId => $topic) {
		$translations = $topic->getTranslations();
		if (array_key_exists($currentLanguageId, $translations)) {
			$currentTopic = $translations[$currentLanguageId];
		} elseif ($currentLanguageId != 1 && array_key_exists(1, $translations)) {
			$currentTopic = $translations[1];
		} else {
			$currentTopic = end($translations);
		}
		$printTopic[] = $currentTopic;
	}
	echo '<option value="' . $topic->getTopicId() . '">'
		. htmlspecialchars(implode(" / ", $printTopic)) . "</option>\n";
}
?>
		</select>
	</td>
</tr>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="HIDDEN" NAME="f_article_type" VALUE="<?php  print htmlspecialchars($articleTypeName); ?>">
	<INPUT TYPE="submit" class="button" NAME="OK" VALUE="<?php  putGS('Save'); ?>">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<?php if ($articleField->getType() == ArticleTypeField::TYPE_TOPIC) { ?>
<script>
UpdateArticleFieldContext();
</script>
<?php } ?>
<?php camp_html_copyright_notice(); ?>
<?php } ?>
