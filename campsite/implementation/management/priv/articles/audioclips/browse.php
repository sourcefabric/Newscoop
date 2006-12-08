<?php
camp_load_translation_strings("article_audioclips");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Audioclip.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/SimplePager.php');


$f_order_by = camp_session_get('f_order_by', 'id');
$f_order_direction = camp_session_get('f_order_direction', 'ASC');
$f_audioclip_offset = camp_session_get('f_audioclip_offset', 0);
$f_items_per_page = camp_session_get('f_items_per_page', 4);
if ($f_items_per_page < 4) {
    $f_items_per_page = 4;
}
$f_category_1_name = camp_session_get('f_category1_name', 'dc:type');
$f_category_1_value = Input::Get('f_category_1_value[]', 'array', array(), true);
$f_category_2_name = camp_session_get('f_category2_name', 'dc:creator');
$f_category_2_value = Input::Get('f_category_2_value[]', 'array', array(), true);
$f_category_3_name = camp_session_get('f_category3_name', 'dc:source');
$f_category_3_value = Input::Get('f_category_3_value[]', 'array', array(), true);

if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
    exit;
}

$category1_values = Audioclip::BrowseCategory($f_category_1_name);
$category2_values = Audioclip::BrowseCategory($f_category_2_name);
$category3_values = Audioclip::BrowseCategory($f_category_3_name);

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
$r = Audioclip::SearchAudioclips(0, 10);
$clipCount = $r[0];
$clips = $r[1];

if (count($clips) > 0) {
    $pagerUrl = camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php")."&";
    $pager =& new SimplePager($clipCount, $f_items_per_page, "f_audioclip_offset", $pagerUrl);
?>
<table border="0" cellspacing="1" cellpadding="6" class="table_list" width="100%">
	<tr>
		<td><?php putGS('Category'); ?>:</td>
		<td><?php putGS('Category'); ?>:</td>
		<td><?php putGS('Category'); ?>:</td>
	</tr>
	<tr>
		<td>
			<select name="f_category1_name" class="input_select" style="width: 180px;">
			<?php
			foreach ($metatagLabel as $tagName=>$tagDescription) {
				if (AudioclipMetadataEntry::GetTagNS($tagName) != 'dc') {
					continue;
				}
				camp_html_select_option($tagName, $f_category_1_name, getGS($tagDescription));
			}
			?>
			</select>
		</td>
		<td>
			<select name="f_category2_name" class="input_select" style="width: 180px;">
			<?php
			foreach ($metatagLabel as $tagName=>$tagDescription) {
				if (AudioclipMetadataEntry::GetTagNS($tagName) != 'dc') {
					continue;
				}
				camp_html_select_option($tagName, $f_category_2_name, getGS($tagDescription));
			}
			?>
			</select>
		</td>
		<td>
			<select name="f_category3_name" class="input_select" style="width: 180px;">
			<?php
			foreach ($metatagLabel as $tagName=>$tagDescription) {
				if (AudioclipMetadataEntry::GetTagNS($tagName) != 'dc') {
					continue;
				}
				camp_html_select_option($tagName, $f_category_3_name, getGS($tagDescription));
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td>
			<select name="f_category1_value[]" class="input_select" multiple size="4" style="width: 180px;">
			<option value="-1">---</option>
			<?php
			foreach ($category1_values['results'] as $index=>$value) {
				camp_html_select_option($index, $f_category_1_value, $value);
			}
			?>
			</select>
		</td>
		<td>
			<select name="f_category2_value[]" class="input_select" multiple size="4" style="width: 180px;">
			<option value="-1">---</option>
			<?php
			foreach ($category2_values['results'] as $index=>$value) {
				camp_html_select_option($index, $f_category_2_value, $value);
			}
			?>
			</select>
		</td>
		<td>
			<select name="f_category3_value[]" class="input_select" multiple size="4" style="width: 180px;">
			<option value="-1">---</option>
			<?php
			foreach ($category3_values['results'] as $index=>$value) {
				camp_html_select_option($index, $f_category_3_value, $value);
			}
			?>
			</select>
		</td>
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