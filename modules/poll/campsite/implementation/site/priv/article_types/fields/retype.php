<?php
camp_load_translation_strings("article_type_fields");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Topic.php");

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
    camp_html_display_error(getGS("You do not have the right to reassign a field type."));
    exit;
}

$articleTypeName = Input::Get('f_article_type');
$articleTypeFieldName = Input::Get('f_field_name');
$articleField =& new ArticleTypeField($articleTypeName, $articleTypeFieldName);

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Article type fields"), "/$ADMIN/article_types/fields/?f_article_type=".urlencode($articleTypeName));
$crumbs[] = array(getGS("Reassign a field type"), "");

echo camp_html_breadcrumbs($crumbs);
include_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/javascript_common.php");

// Verify the merge rules
// Text->Text = OK
// Text->Body = OK
// Body->Text = NO
// Body->Body = OK
// Text->Date = NO
// Text->Topic = NO
// Body->Date = NO
// Body->Topic = NO
// Date->Text = OK
// Date->Body = OK
// Date->Date = OK
// Date->Topic = NO
// Topic->Text = OK
// Topic->Body = OK
// Topic->Date = NO
// Topic->Topic = NO* (TODO)
$options = array();
if ($articleField->getType() == 'mediumblob') {
    $options = array();        
}
if ($articleField->getType() == 'date') {
    $options = array('datey' => getGS('Date'), 'text' => getGS('Single-line Text'), 'body' => getGS('Multi-line Text with WYSIWYG'));    
}
if ($articleField->getType() == 'varchar(255)') {
    $options = array('text' => getGS('Single-line Text'), 'body' => getGS('Multi-line Text with WYSIWYG'));    
}
if ($articleField->getType() == 'int(10) unsigned') {
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


<?php if (!count($options)) { ?>
<P>
You cannot reassign this type.
</P>
<?php camp_html_copyright_notice(); ?>
<?php } else { ?>


<P>
<FORM NAME="add_field_form" METHOD="POST" ACTION="do_retype.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<input type="hidden" name="f_field_name" value="<?php print $articleTypeFieldName; ?>">
<input type="hidden" name="is_topic" id="is_topic" value="false">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
    <TD ALIGN="RIGHT" ><?php  putGS("Type"); ?>:</TD>
    <TD>
    <SELECT NAME="f_article_field_type" class="input_select" onchange="UpdateArticleFieldContext()">
        <?php foreach ($options as $k => $v) { ?>
            <OPTION VALUE="<?php print $k; ?>"><?php putGS($v); ?></OPTION>
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
$lang =& new Language($TOL_Language);
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
<?php if ($articleField->getType() == 'int(10) unsigned') { ?>
<script>
UpdateArticleFieldContext();
</script>
<?php } ?>
<?php camp_html_copyright_notice(); ?>
<?php } ?>