<?php
camp_load_translation_strings("article_audioclips");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Audioclip.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/SimplePager.php');


$f_order_by = camp_session_get('f_order_by', 'id');
$f_order_direction = camp_session_get('f_order_direction', 'ASC');
$f_audioclip_offset = camp_session_get('f_image_offset', 0);
$f_category = Input::Get('f_category', 'string', null, true);
$f_condition = Input::Get('f_condition', 'string', null, true);
$f_search_string = Input::Get('f_search_string', 'string', null, true);
$f_operator = Input::Get('f_operator', 'string', null, true);
$f_items_per_page = camp_session_get('f_items_per_page', 4);


if ($f_items_per_page < 4) {
	$f_items_per_page = 4;
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
	exit;
}

// Build the links for ordering search results
$OrderSign = '';
if ($f_order_direction == 'DESC') {
	$ReverseOrderDirection = "ASC";
	$OrderSign = "<img src=\"".$Campsite["ADMIN_IMAGE_BASE_URL"]."/descending.png\" border=\"0\">";
} else {
	$ReverseOrderDirection = "DESC";
	$OrderSign = "<img src=\"".$Campsite["ADMIN_IMAGE_BASE_URL"]."/ascending.png\" border=\"0\">";
}

$conditions = array('row_1' => array('cat' => $f_category,
                                     'op' => $f_condition,
                                     'val' => $f_search_string)
                    );

// Gets all the available audioclips
if (sizeof($conditions) > 0 && !empty($conditions['row_1']['cat'])
        && !empty($conditions['row_1']['op'])
        && !empty($conditions['row_1']['val'])) {
    $r = Audioclip::SearchAudioclips(0, 10, $conditions);

} else {
    $r = Audioclip::SearchAudioclips(0, 10);
}

$clipCount = $r[0];
$clips = $r[1];

?>

<TABLE cellspacing="1" cellpadding="2" class="table_list">
<FORM method="POST" action="popup.php">
<TR>
    <TD>
        <DIV id="row_1">
        <SELECT name="f_category" class="input_select">
        <?php
        foreach ($metatagLabel as $tag => $value) {
            camp_html_select_option($tag, '', $value);
        }
        ?>
        </SELECT>
        <SELECT name="f_condition" class="input_select">
        <?php
            camp_html_select_option('partial', '', 'partial');
            camp_html_select_option('full', '', 'full');
            camp_html_select_option('prefix', '', 'prefix');
            camp_html_select_option('=', '', '=');
            camp_html_select_option('&lt;', '', '<');
            camp_html_select_option('&lt;=', '', '<=');
            camp_html_select_option('&gt;', '', '>');
            camp_html_select_option('&gt;=', '', '>=');
        ?>
        </SELECT>
        <INPUT type="text" name="f_search_string" class="input_text" size="25" maxlength="255" />
        <INPUT type="button" class="button" value="<?php putGS("Add"); ?>" />
        </DIV>
    </TD>
</TR>
<TR>
    <TD align="center">
        <?php putGS("Operator"); ?>:
        <SELECT name="f_operator" class="input_select">
        <?php
            camp_html_select_option('or', '', 'Or');
            camp_html_select_option('and', '', 'And');
        ?>
        </SELECT>
    </TD>
</TR>
<TR>
    <TD align="right">
        <INPUT type="submit" name="" class="button" value="<?php putGS("Submit"); ?>" />
    </TD>
</TR>
<INPUT type="hidden" name="f_publication_id" value="<?php p($f_publication_id); ?>">
<INPUT type="hidden" name="f_issue_number" value="<?php p($f_issue_number); ?>">
<INPUT type="hidden" name="f_section_number" value="<?php p($f_section_number); ?>">
<INPUT type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
<INPUT type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
<INPUT type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
<INPUT type="hidden" name="BackLink" value="<?php p($_SERVER['REQUEST_URI']); ?>">
<INPUT type="hidden" name="f_audio_attach_mode" value="existing" />
<INPUT type="hidden" name="f_audio_search_mode" value="search" />
</FORM>
</TABLE>
<?php
if (count($clips) > 0) {
    $pagerUrl = camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php")."&";
    $pager =& new SimplePager($clipCount, $f_items_per_page, "f_audioclip_offset", $pagerUrl);
    require('cliplist.php');
} else {
?>
<TABLE border="0" cellspacing="1" cellpadding="6" class="table_list">
<TR>
    <TD>
        <?php putGS("No audioclips found"); ?>
    </TD>
</TR>
</TABLE>
<?php
}
?>