<?php
camp_load_translation_strings("article_audioclips");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Audioclip.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SimplePager.php');

define('VALUE_ALL', '-1');

$f_order_by = Input::Get('f_order_by', 'string', 'dc:title', true);
$f_order_direction = Input::Get('f_order_direction', 'string', 0, true);
$f_audioclip_offset = camp_session_get('f_audioclip_offset', 0);
$f_items_per_page = camp_session_get('f_items_per_page', 10);
if ($f_items_per_page < 4) {
    $f_items_per_page = 4;
}

$f_category_3_name = camp_session_get('f_category_3_name', 'dc:source');
$f_category_3_name_prev = camp_session_get('f_category_3_name_prev', 'dc:source');
if ($f_category_3_name == $f_category_3_name_prev) {
	$f_category_3_value = Input::Get('f_category_3_value', 'array', array(VALUE_ALL), true);
} else {
	$f_category_3_value = array(VALUE_ALL);
	$f_audioclip_offset = 0;
}

$f_category_2_name = camp_session_get('f_category_2_name', 'dc:creator');
$f_category_2_name_prev = camp_session_get('f_category_2_name_prev', 'dc:creator');
$f_category_2_value_prev = Input::Get('f_category_2_value_prev', 'array', array(VALUE_ALL), true);
if ($f_category_2_name == $f_category_2_name_prev) {
	$f_category_2_value = Input::Get('f_category_2_value', 'array', array(VALUE_ALL), true);
	if ($f_category_2_value != $f_category_2_value_prev) {
		$f_category_3_value = array(VALUE_ALL);
	}
} else {
	$f_category_2_value = array(VALUE_ALL);
	$f_category_3_value = array(VALUE_ALL);
	$f_audioclip_offset = 0;
}

$f_category_1_name = camp_session_get('f_category_1_name', 'dc:type');
$f_category_1_name_prev = camp_session_get('f_category_1_name_prev', 'dc:type');
$f_category_1_value_prev = Input::Get('f_category_1_value_prev', 'array', array(VALUE_ALL), true);
if ($f_category_1_name == $f_category_1_name_prev) {
	$f_category_1_value = Input::Get('f_category_1_value', 'array', array(VALUE_ALL), true);
	if ($f_category_1_value != $f_category_1_value_prev) {
		$f_category_2_value = array(VALUE_ALL);
		$f_category_3_value = array(VALUE_ALL);
	}
} else {
	$f_category_1_value = array(VALUE_ALL);
	$f_category_2_value = array(VALUE_ALL);
	$f_category_3_value = array(VALUE_ALL);
	$f_audioclip_offset = 0;
}

if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
    exit;
}

// we check for the Campcaster session and show
// the login form if necessary
$isCcOnline = true;
$sessid = null;
$sessid = camp_session_get(CS_CAMPCASTER_SESSION_VAR_NAME, '');
if (empty($sessid)) {
    camp_html_goto_page('campcaster_login.php');
}

// ... is something wrong with either the sessid
// or the communication to Campcaster
$xrc = XR_CcClient::Factory($mdefs);
$resp = $xrc->ping($sessid);
if (PEAR::isError($resp)) {
    switch ($resp->getCode()) {
        case '805':
            camp_html_goto_page('campcaster_login.php');
            break;
        case '804':
        default:
            camp_html_add_msg(getGS("Unable to reach the Campcaster server."));
            camp_html_add_msg(getGS("Try again later."));
            $isCcOnline = false;
            break;
    }
}

$search_conditions = array();
for ($varIndex = 1; $varIndex <= 3; $varIndex++) {
	$f_curr_category_value =& ${'f_category_'.$varIndex.'_value'};
	$f_curr_category_name =& ${'f_category_'.$varIndex.'_name'};
	$category_conditions[$varIndex] = array();
	foreach ($f_curr_category_value as $categoryValue) {
		if ($categoryValue == '-1') {
			continue;
		}
		$condition = array('cat' => $f_curr_category_name,
						   'op' => '=',
						   'val' => $categoryValue);
		$category_conditions[$varIndex][] = $condition;
	}
	$search_conditions = array_merge($search_conditions, $category_conditions[$varIndex]);
}
$browse_category_2_conditions = $category_conditions[1];
$browse_category_3_conditions = array_merge($category_conditions[1], $category_conditions[2]);
$category_1_values = Audioclip::BrowseCategory($f_category_1_name);
if (PEAR::isError($category_1_values)) {
    camp_html_add_msg($category_1_values->getMessage());
	$category_1_values = array();
}
$category_2_values = Audioclip::BrowseCategory($f_category_2_name, 0, 0, $browse_category_2_conditions);
if (PEAR::isError($category_2_values)) {
    camp_html_add_msg($category_2_values->getMessage());
    $category_2_values = array();
}
$category_3_values = Audioclip::BrowseCategory($f_category_3_name, 0, 0, $browse_category_3_conditions);
if (PEAR::isError($category_3_values)) {
    camp_html_add_msg($category_3_values->getMessage());
    $category_3_values = array();
}

if ($isCcOnline) {
    $r = Audioclip::SearchAudioclips($f_audioclip_offset, $f_items_per_page, $search_conditions, null, $f_order_by, $f_order_direction);
    if (PEAR::isError($r)) {
    	$clipCount = 0;
    	$clips = array();
    	camp_html_add_msg($r->getMessage());
    } else {
    	$clipCount = $r[0];
    	$clips = $r[1];
    }
}

// Build the links for ordering search results
// 1 = DESC, 0 = ASC
$orderDirections = array('dc:title' => 0,
                         'dc:creator' => 0,
                         'dcterms:extent' => 0
                         );
if (array_key_exists($f_order_by, $orderDirections)) {
    $orderDirections[$f_order_by] = ($f_order_direction == 1) ? 0 : 1;
}

camp_html_display_msgs();

if ($isCcOnline) {
?>
<form name="browse" action="popup.php" method="POST">
<input type="hidden" name="f_publication_id" value="<?php p($f_publication_id); ?>">
<input type="hidden" name="f_issue_number" value="<?php p($f_issue_number); ?>">
<input type="hidden" name="f_section_number" value="<?php p($f_section_number); ?>">
<input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
<input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
<input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
<input type="hidden" name="f_audio_attach_mode" value="existing" />
<input type="hidden" name="f_audio_search_mode" value="browse" />
<input type="hidden" name="f_category_1_name_prev" value="<?php p($f_category_1_name); ?>">
<input type="hidden" name="f_category_2_name_prev" value="<?php p($f_category_2_name); ?>">
<input type="hidden" name="f_category_3_name_prev" value="<?php p($f_category_3_name); ?>">
<input type="hidden" name="f_order_by" value="" />
<input type="hidden" name="f_order_direction" value="" />
<?php
for ($catIndex = 1; $catIndex <= 2; $catIndex++) {
	foreach (${'f_category_'.$catIndex.'_value'} as $categoryValue) {
?>
<input type="hidden" name="f_category_<?php echo $catIndex; ?>_value_prev[]" value="<?php echo htmlspecialchars($categoryValue); ?>">
<?php
	}
}
?>
<table border="0" cellspacing="1" cellpadding="6" class="table_list" width="100%">
	<tr>
		<td><?php putGS('Category'); ?>:</td>
		<td><?php putGS('Category'); ?>:</td>
		<td><?php putGS('Category'); ?>:</td>
	</tr>
	<tr>
		<td>
			<select name="f_category_1_name" class="input_select" style="width: 180px;" onchange="document.forms.browse.submit();">
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
			<select name="f_category_2_name" class="input_select" style="width: 180px;" onchange="document.forms.browse.submit();">
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
			<select name="f_category_3_name" class="input_select" style="width: 180px;" onchange="document.forms.browse.submit();">
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
			<select name="f_category_1_value[]" class="input_select" style="width: 180px;" onchange="document.forms.browse.submit();">
			<option value="<?php echo htmlspecialchars(VALUE_ALL); ?>"></option>
			<?php
			if (isset($category_1_values['results'])) {
				foreach ($category_1_values['results'] as $index=>$value) {
					camp_html_select_option($value, $f_category_1_value, $value);
				}
			}
			?>
			</select>
		</td>
		<td>
			<select name="f_category_2_value[]" class="input_select" style="width: 180px;" onchange="document.forms.browse.submit();">
			<option value="<?php echo htmlspecialchars(VALUE_ALL); ?>"></option>
			<?php
            if (isset($category_2_values['results'])) {
            	foreach ($category_2_values['results'] as $index=>$value) {
            		camp_html_select_option($value, $f_category_2_value, $value);
            	}
            }
			?>
			</select>
		</td>
		<td>
			<select name="f_category_3_value[]" class="input_select" style="width: 180px;" onchange="document.forms.browse.submit();">
			<option value="<?php echo htmlspecialchars(VALUE_ALL); ?>"></option>
			<?php
            if (isset($category_3_values['results'])) {
            	foreach ($category_3_values['results'] as $index=>$value) {
            		camp_html_select_option($value, $f_category_3_value, $value);
            	}
            }
			?>
			</select>
		</td>
	</tr>
</table>
</form>
<?php
	if (count($clips) > 0) {
	    $pagerUrl = camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php")."&f_order_by=$f_order_by&f_order_direction=$f_order_direction&";
    	$pager = new SimplePager($clipCount, $f_items_per_page, "f_audioclip_offset", $pagerUrl);
    	require('cliplist.php');
    } else {
?>
<table border="0" cellspacing="1" cellpadding="6" class="table_list">
<tr>
    <td>
        <?php putGS("No audioclips found"); ?>
    </td>
</tr>
</table>
<?php
    }
} else { // if ($isCcOnline)
?>
<table border="0" width="100%" cellspacing="1" cellpadding="6" class="table_list">
<tr>
    <td align="center">
        <input type="button" name="close" class="button" value="<?php putGS("Close"); ?>" onclick="window.close();" />
    </td>
</tr>
</table>
<?php
}
?>