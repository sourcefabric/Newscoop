<?php
camp_load_translation_strings("article_audioclips");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Audioclip.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/SimplePager.php');


$f_order_by = camp_session_get('f_order_by', 'id');
$f_order_direction = camp_session_get('f_order_direction', 'ASC');
$f_audioclip_offset = camp_session_get('f_audioclip_offset', 0);
$f_items_per_page = camp_session_get('f_items_per_page', 4);
if ($f_items_per_page < 8) {
    $f_items_per_page = 8;
}
$f_category_1_name = camp_session_get('f_category1_name', 'dc:type');
$f_category_1_value = camp_session_get('f_category1_value', null);
$f_category_2_name = camp_session_get('f_category2_name', 'dc:creator');
$f_category_2_value = camp_session_get('f_category2_value', null);
$f_category_3_name = camp_session_get('f_category3_name', 'dc:source');
$f_category_3_value = camp_session_get('f_category3_value', null);

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

// Gets all the available audioclips
$r = Audioclip::SearchAudioclips($f_audioclip_offset, $f_items_per_page);
$clipCount = $r[0];
$clips = $r[1];

if (count($clips) > 0) {
    $pagerUrl = camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php")."&";
    $pager =& new SimplePager($clipCount, $f_items_per_page, "f_audioclip_offset", $pagerUrl);
?>
<table>
	<tr>
		<td><?php putGS('Category'); ?>:</td>
		<td><?php putGS('Category'); ?>:</td>
		<td><?php putGS('Category'); ?>:</td>
	</tr>
	<tr>
		<td><?php putGS($metatagLabel[$f_category_1_name]); ?></td>
		<td><?php putGS($metatagLabel[$f_category_2_name]); ?></td>
		<td><?php putGS($metatagLabel[$f_category_3_name]); ?></td>
	</tr>
</table>
<?php
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