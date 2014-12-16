<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');
require_once($GLOBALS['g_campsiteDir']."/classes/Topic.php");

$translator = \Zend_Registry::get('container')->getService('translator');
// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
    camp_html_display_error($translator->trans('You do not have the right to add article types.', array(), 'article_type_fields'));
    exit;
}

$articleTypeName = Input::Get('f_article_type');
$lang = camp_session_get('LoginLanguageId', 1);
$langObj = new Language($lang);
$currentLanguageId = $langObj->getLanguageId();

$em = \Zend_Registry::get('container')->getService('em');
$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
$topicService = \Zend_Registry::get('container')->getService('newscoop_newscoop.topic_service');
$topicsCount = $topicService->countBy();
$cacheKey = $cacheService->getCacheKey(array('topics_add_article_type', $topicsCount), 'topic');
$repository = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic');
if ($cacheService->contains($cacheKey)) {
    $topics = $cacheService->fetch($cacheKey);
} else {
    $topicsQuery = $repository->getTranslatableTopics($langObj->getCode());
    $topics = $topicsQuery->getResult();
    $cacheService->save($cacheKey, $topics);
}

$crumbs = array();
$crumbs[] = array($translator->trans('Configure'), "");
$crumbs[] = array($translator->trans('Article Types'), "/$ADMIN/article_types/");
$crumbs[] = array($articleTypeName, '');
$crumbs[] = array($translator->trans("Article type fields", array(), 'article_type_fields'), "/$ADMIN/article_types/fields/?f_article_type=".urlencode($articleTypeName));
$crumbs[] = array($translator->trans("Add new field", array(), 'article_type_fields'), "");

echo camp_html_breadcrumbs($crumbs);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

foreach (ArticleTypeField::DatabaseTypes() as $type=>$sqlDesc) {
    $options[$type] = ArticleTypeField::VerboseTypeName($type, $currentLanguageId);
}

?>
<script>
function UpdateArticleFieldContext()
{
    var my_form = document.forms["add_field_form"]
    var field_type = my_form.elements["f_article_field_type"].value
    var is_topic = my_form.elements["is_topic"].value
    if ((is_topic == "false" && field_type == "topic")
            || (is_topic == "true" && field_type != "topic")) {
        ToggleRowVisibility('topic_list')
        ToggleBoolValue('is_topic')
    }

    var show_is_content = my_form.elements["show_is_content"].value
    if ((show_is_content == "true" && field_type != "body")
            || (show_is_content == "false" && field_type == "body")) {
        ToggleRowVisibility('is_content');
        ToggleBoolValue('show_is_content');
    }

    var show_editor_size = my_form.elements["show_editor_size"].value
    if ((show_editor_size == "true" && field_type != "body")
            || (show_editor_size == "false" && field_type == "body")) {
        ToggleRowVisibility('editor_size');
        ToggleBoolValue('show_editor_size');
    }

    var show_precision = my_form.elements["show_precision"].value
    if ((show_precision == "true" && field_type != "numeric")
            || (show_precision == "false" && field_type == "numeric")) {
        ToggleRowVisibility('precision');
        ToggleBoolValue('show_precision');
    }

    var show_maxsize = my_form.elements["show_maxsize"].value
    if ((show_maxsize == "true" && field_type != "text")
            || (show_maxsize == "false" && field_type == "text")) {
        ToggleRowVisibility('maxsize');
        ToggleBoolValue('show_maxsize');
    }

    if (field_type == "complex_date") {
        $('#event_color_part').removeClass('color_sel_hidden');
    } else {
        $('#event_color_part').addClass('color_sel_hidden');
    }
}
</script>

<P>
<FORM NAME="add_field_form" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/article_types/fields/do_add.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<input type="hidden" name="is_topic" id="is_topic" value="false">
<input type="hidden" name="show_is_content" id="show_is_content" value="false">
<input type="hidden" name="show_editor_size" id="show_editor_size" value="false">
<input type="hidden" name="show_precision" id="show_precision" value="false">
<input type="hidden" name="show_maxsize" id="show_maxsize" value="true">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR><TD COLSPAN="2"><?php echo $translator->trans('The template name may only contain letters and the underscore (_) character.', array(), 'article_type_fields'); ?></TD></TR>
<TR>
    <TD ALIGN="RIGHT" ><?php echo $translator->trans("Template Field Name", array(), 'article_type_fields'); ?>:</TD>
    <TD>
    <INPUT TYPE="TEXT" class="input_text" NAME="f_field_name" SIZE="20" MAXLENGTH="32" alt="alnum|1|A|false|false|_" emsg="<?php echo $translator->trans("The template name may only contain letters and the underscore (_) character.", array(), 'article_type_fields'); ?>">
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT" ><?php echo $translator->trans("Type"); ?>:</TD>
    <TD>
    <SELECT NAME="f_article_field_type" class="input_select" onchange="UpdateArticleFieldContext()">
        <?php foreach ($options as $k => $v) {
            if ($k == ArticleTypeField::TYPE_TOPIC && count($topics) == 0) {
                continue;
            }
        ?>
            <OPTION VALUE="<?php print $k; ?>"><?php echo $v; ?></OPTION>
        <?php } ?>
    </SELECT>
    </TD>
</TR>
<tr style="display: none;" id="topic_list">
    <td align="right"><?php echo $translator->trans("Top element", array(), 'article_type_fields'); ?>:</td>
    <td>
        <select name="f_root_topic_id" class="input_select">
<?php

foreach ($topics as $topic) {
    echo '<option value="' . $topic->getTopicId() . '">'
        . htmlspecialchars($topicService->getReadablePath($topic)) . "</option>\n";
}
?>
        </select>
    </td>
</tr>

<style type="text/css">
.color_sel_hidden {
    display: none;
}
.color_sel_visible {
    margin-top: -8px;
    border-color: #c0c0c0;
    border-width: 8px;
    border-style: solid;
    margin-left: 25px;
    position: absolute;
}
.color_one_current {
    border-color: #404040;
    border-width: 1px;
    border-style: solid;

    float:left;
    width:14px;
    height:14px;
    cursor:pointer
}
.color_one_list {
    border-color: #404040;
    border-width: 1px;
    border-style: solid;

    float:right;
    width:14px;
    height:14px;
    cursor:pointer
}

</style>

<script type="text/javascript">
window.set_field_color = function (color) {
    $('#f_event_color').val(color);
}
</script>

<tr class="color_sel_hidden" id="event_color_part">
    <td align="right"><?php echo $translator->trans('Event Color', array(), 'article_type_fields'); ?>:</td><td>
<?php

$color_list = array(
'#ff4040',
'#ff4080',
'#ff8040',
'#ff8080',

'#ff40ff',

'#40ff40',
'#80ff40',
'#40ff80',
'#80ff80',

'#ffff40',

'#4040ff',
'#8040ff',
'#4080ff',
'#8080ff',

'#40ffff',

'#808080',
);

        $cur_color = ArticleTypeField::getDefaultColor();

        $row_rank = 0;
        $color_div = '';

        $color_div .= '<div id="color_sel_' . $row_rank . '" class="color_sel_hidden color_sel_visible">';
        foreach ($color_list as $one_color) {
            $color_div .= '<div class="color_one_list" style="background:' . $one_color . ';" onClick="$(\'#color_val_' . $row_rank . '\').css(\'backgroundColor\', \'' . $one_color . '\'); $(\'#color_sel_' . $row_rank . '\').addClass(\'color_sel_hidden\'); window.set_field_color(\'' . $one_color . '\'); return false;";></div>';
        }
        $color_div .= '</div>';
        $color_div .= '<div class="color_one_current" id="color_val_' . $row_rank . '" style="background-color:' . $cur_color . ';" href="#" onClick="$(\'#color_sel_' . $row_rank . '\').toggleClass(\'color_sel_hidden\')"; return false;"></div>';
        echo $color_div;
?>

    <input type="text" style="display: none" id="f_event_color" name="f_event_color" value="<?php echo $cur_color; ?>">
</td>

</tr>
<tr style="display: none;" id="is_content">
    <td align="right"><?php echo $translator->trans('Is Content', array(), 'article_type_fields'); ?>:</td>
    <td><input type="checkbox" name="f_is_content"></td>
</tr>
<tr style="display: none;" id="editor_size">
    <td align="right"><?php echo $translator->trans('Editor size'); ?>:</td>
    <td>
        <select name="f_editor_size" onChange="if (this.value == 'custom') document.getElementById('editor_size_custom').style.display = 'inline'; else document.getElementById('editor_size_custom').style.display = 'none';">
            <option value="small"><?php echo $translator->trans('Small ($1 pixels)', array('$1' => ArticleTypeField::BODY_ROWS_SMALL), 'article_type_fields'); ?></option>
            <option value="medium"><?php echo $translator->trans('Medium ($1 pixels)', array('$1' => ArticleTypeField::BODY_ROWS_MEDIUM), 'article_type_fields'); ?></option>
            <option value="large"><?php echo $translator->trans('Large ($1 pixels)', array('$1' => ArticleTypeField::BODY_ROWS_LARGE), 'article_type_fields');?></option>
            <option value="custom"><?php echo $translator->trans('Custom', array(), 'article_type_fields'); ?></option>
        </select>
        &nbsp;
        <input type="text" name="f_editor_size_custom" class="input_text" value="160" id="editor_size_custom" size="3" style="display: none;">
    </td>
</tr>
<tr style="display: none;" id="precision">
    <td align="right"><?php echo $translator->trans('Precision', array(), 'article_type_fields'); ?>:</td>
    <td><input type="text" class="input_text" size="2" maxlength="2" name="f_precision" emsg="<?php echo $translator->trans('You must input a number greater than $1 and less than $2 into the $3 field.', array('$1' => 0, '$2' => 99, '$3' => $translator->trans('Precision', array(), 'article_type_fields')), 'article_type_fields'); ?>" alt="number|0|0|99|bok" ></td>
</tr>
<tr style="display: table-row;" id="maxsize">
    <td align="right"><?php echo $translator->trans('Characters limit', array(), 'article_type_fields'); ?>:</td>
    <td>
        <input type="text" class="input_text" size="3" maxlength="3" name="f_maxsize" emsg="<?php echo $translator->trans('You must input a number greater than $1 and less than $2 into the $3 field.', array('$1' => 0, '$2' => 999, '$3' => $translator->trans('Characters limit', array(), 'article_type_fields')), 'article_type_fields'); ?>" alt="number|0|1|999|bok" >
    </td>
</tr>
<TR>
    <TD COLSPAN="2">
    <DIV ALIGN="CENTER">
    <INPUT TYPE="HIDDEN" NAME="f_article_type" VALUE="<?php  print htmlspecialchars($articleTypeName); ?>">
    <INPUT TYPE="submit" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('Save'); ?>">
    </DIV>
    </TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
