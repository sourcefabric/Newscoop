<?php
camp_load_translation_strings("article_audioclips");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Audioclip.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SimplePager.php');

$f_order_by = Input::Get('f_order_by', 'string', 'dc:title', true);
$f_order_direction = Input::Get('f_order_direction', 'string', 0, true);
$f_audioclip_offset = camp_session_get('f_audioclip_offset', 0);
$f_operator = Input::Get('f_operator', 'string', 'and', true);
$f_items_per_page = camp_session_get('f_items_per_page', 10);

$row_1 = Input::Get('row_1', 'array', array(), true);
$row_2 = Input::Get('row_2', 'array', array(), true);
$row_3 = Input::Get('row_3', 'array', array(), true);
$row_4 = Input::Get('row_4', 'array', array(), true);
$row_5 = Input::Get('row_5', 'array', array(), true);

if ($f_items_per_page < 4) {
	$f_items_per_page = 4;
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

// Maximum number of criteria input allowed
$maxCriteria = 5;

// Gets all the criteria to search by
$conditions = array();
for ($c = 1, $counter = 0; $c <= $maxCriteria; $c++) {
    if (${'row_'.$c}['active'] == 1) {
        $conditions['row_'.$c] = array ('cat' => ${'row_'.$c}[0],
                                        'op' => ${'row_'.$c}[1],
                                        'val' => ${'row_'.$c}[2]
                                        );
        $counter++;
    }
}
// Set default values when criteria has not been submitted
if ($counter == 0) {
    $counter = 1;
    $row_1 = array('active' => 1,
                   0 => 'dc:title',
                   1 => 'partial'
                   );
}

$r = array();
if ($isCcOnline) {
    // Gets all the available audioclips
    if (sizeof($conditions) > 0) {
        $r = Audioclip::SearchAudioclips($f_audioclip_offset, $f_items_per_page, $conditions, $f_operator, $f_order_by, $f_order_direction);
    } else {
        $r = Audioclip::SearchAudioclips($f_audioclip_offset, $f_items_per_page, null, $f_operator, $f_order_by, $f_order_direction);
    }
}

if (PEAR::isError($r)) {
    camp_html_display_error(getGS('There was a problem trying to communicate to Campcaster'), null, true);
    exit;
}

// Sets clips amount and clips data from SearchAudioclips result
if (sizeof($r) > 0) {
    $clipCount = $r[0];
    $clips = $r[1];
}
// Array of comparison operators
$operators = array('partial',
                   'full',
                   'prefix',
                   '=',
                   '<',
                   '<=',
                   '>',
                   '>='
                   );
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

<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite-audiosearch.js"></script>

<form method="POST" name="search" action="popup.php">
<table cellspacing="1" cellpadding="2" class="table_list">
<?php
for ($c = 1; $c <= $maxCriteria; $c++) {
?>
<input type="hidden" name="row_<?php p($c); ?>[active]" value="<?php p(${'row_'.$c}['active']); ?>" />
<?php
}
?>
<input type="hidden" name="counter" value="<?php p($counter); ?>" />
<input type="hidden" name="max_rows" value="<?php p($maxCriteria); ?>" />
<tr>
    <td>
    <?php
    for ($i = 1; $i <= $maxCriteria; $i++) {
        $row_active = (array_key_exists('active', ${'row_'.$i})) ? ${'row_'.$i}['active'] : null;
        $row_cat = (array_key_exists('0', ${'row_'.$i})) ? ${'row_'.$i}[0] : null;
        $row_op = (array_key_exists('1', ${'row_'.$i})) ? ${'row_'.$i}[1] : null;
        $row_val = (array_key_exists('2', ${'row_'.$i})) ? ${'row_'.$i}[2] : null;
    ?>
        <script type="text/javascript">
            _hs_options['row_<?php p($i); ?>'] = [
            <?php
            echo '{ ';
            foreach ($metatagLabel as $tag => $value) {
                echo "'".$tag."': { ";
                foreach ($operators as $op) {
                    echo "'".$op."': '".$op;
                    echo ($op != '>=') ? "', " : "' ";
                }
                echo "}, ";
            }
            echo "} ]\n";
            ?>
            _hs_defaults['row_<?php p($i); ?>'] = ['<?php p($row_cat); ?>', '<?php p($row_op); ?>'];
        </script>
        <?php
        $rowStyle = ($row_active != 1) ? 'display:none' : '';
        ?>
        <div id="searchRow_<?php p($i); ?>" style="<?php p($rowStyle); ?>">
        <div class="audiosearch_container">
        <select name="row_<?php p($i); ?>[0]" class="input_select" onchange="_hs_swapOptions(this.form, 'row_<?php p($i); ?>', 0">
        <?php
        foreach ($metatagLabel as $tag => $value) {
            camp_html_select_option($tag, '', $value);
        }
        ?>
        </select>
        <select name="row_<?php p($i); ?>[1]" class="input_select">
        <?php
        foreach ($operators as $op) {
            camp_html_select_option($op, '', $op);
        }
        ?>
        </select>
        <input type="text" name="row_<?php p($i); ?>[2]" class="input_text" size="25" maxlength="255" value="<?php p($row_val); ?>" />
        <?php
        if ($i == 1) {
        ?>
        <input type="button" class="button" name="addRow" onclick="SearchForm_addRow('<?php putGS("Maximum reached"); ?>');" value="<?php putGS("Add"); ?>" />
        <?php
        } else {
        ?>
        <input type="button" class="button" name="dropRow_<?php p($i); ?>" onclick="SearchForm_dropRow('<?php p($i); ?>');" value="-" />
        <?php
        }
        ?>
        </div>
        </div id="searchRow_<?php p($i); ?>">
    <?php
    }
    ?>
    </td>
</tr>
<tr>
    <td align="center">
        <?php putGS("Operator"); ?>:
        <select name="f_operator" class="input_select">
        <?php
            camp_html_select_option('or', $f_operator, 'Or');
            camp_html_select_option('and', $f_operator, 'And');
        ?>
        </select>
    </td>
</tr>
<tr>
    <td align="right">
        <input type="button" class="button" onclick="this.form.reset();" value="<?php putGS("Reset Criteria"); ?>" />
        <input type="submit" class="button" value="<?php putGS("Submit"); ?>" />
    </td>
</tr>
</table>
<input type="hidden" name="f_publication_id" value="<?php p($f_publication_id); ?>" />
<input type="hidden" name="f_issue_number" value="<?php p($f_issue_number); ?>" />
<input type="hidden" name="f_section_number" value="<?php p($f_section_number); ?>" />
<input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>" />
<input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>" />
<input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>" />
<input type="hidden" name="BackLink" value="<?php p($_SERVER['REQUEST_URI']); ?>" />
<input type="hidden" name="f_audio_attach_mode" value="existing" />
<input type="hidden" name="f_audio_search_mode" value="search" />
<input type="hidden" name="f_order_by" value="" />
<input type="hidden" name="f_order_direction" value="" />
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