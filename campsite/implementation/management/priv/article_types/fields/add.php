<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_type_fields");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Topic.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to add article type fields."));
	exit;
}

$articleTypeName = Input::Get('AType');

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Article type fields"), "/$ADMIN/article_types/fields/?AType=".urlencode($articleTypeName));
$crumbs[] = array(getGS("Add new field"), "");

echo camp_html_breadcrumbs($crumbs);

?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>
<script>
function UpdateArticleFieldContext() {
	var my_form = document.forms["add_field_form"]
	var field_type = my_form.elements["cType"].value
	var is_topic = my_form.elements["is_topic"].value
	if ((is_topic == "false" && field_type == "topic")
			|| (is_topic == "true" && field_type != "topic")) {
		ToggleRowVisibility('topic_list')
		ToggleBoolValue('is_topic')
	}
}
</script>

<P>
<FORM NAME="add_field_form" METHOD="POST" ACTION="do_add.php" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<input type="hidden" name="is_topic" id="is_topic" value="false">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="20" MAXLENGTH="32" alt="blank" emsg="<?php putGS("You must complete the $1 field.", getGS("Name")) ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Type"); ?>:</TD>
	<TD>
	<SELECT NAME="cType" class="input_select" onchange="UpdateArticleFieldContext()">
		<OPTION VALUE="text"><?php  putGS('Text'); ?>
		<OPTION VALUE="date"><?php  putGS('Date'); ?>
		<OPTION VALUE="body"><?php  putGS('Article body'); ?>
		<OPTION VALUE="topic"><?php  putGS('Topic'); ?>
	</SELECT>
	</TD>
</TR>
<tr style="display: none;" id="topic_list">
	<td align="right"><?php putGS("Top element"); ?>:</td>
	<td>
		<select name="f_root_topic_id" class="input_select">
<?php
$TOL_Language = Input::Get('TOL_Language');
$currentLanguages = Language::GetLanguages(null, $TOL_Language);
$currentLanguageId = $currentLanguages[0]->getLanguageId();
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
	<INPUT TYPE="HIDDEN" NAME="AType" VALUE="<?php  print htmlspecialchars($articleTypeName); ?>">
	<INPUT TYPE="submit" class="button" NAME="OK" VALUE="<?php  putGS('Save'); ?>">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
