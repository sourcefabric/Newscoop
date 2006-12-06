<?php
camp_load_translation_strings("article_audioclips");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Audioclip.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/SimplePager.php');


$f_order_by = camp_session_get('f_order_by', 'id');
$f_order_direction = camp_session_get('f_order_direction', 'ASC');
$f_audioclip_offset = camp_session_get('f_image_offset', 0);
$f_operator = Input::Get('f_operator', 'string', 'and', true);
$f_items_per_page = camp_session_get('f_items_per_page', 4);

$row_1 = Input::Get('row_1', 'array', array(), true);
$row_2 = Input::Get('row_2', 'array', array(), true);
$row_3 = Input::Get('row_3', 'array', array(), true);
$row_4 = Input::Get('row_4', 'array', array(), true);
$row_5 = Input::Get('row_5', 'array', array(), true);

$maxCriteria = 5;

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

$conditions = array();
for ($c = 1, $counter = 0; $c <= $maxCriteria; $c++) {
    if (${'row_'.$c}['active'] == 1) {
        $conditions['row_'.$c] = array ('cat' => ${'row_'.$c}[0],
                                        'op' => ${'row_'.$c}[1],
                                        'val' => ${'row_'.$c}[2]);
        $counter++;
    }
}

// Gets all the available audioclips
if (sizeof($conditions) > 0) {
    $r = Audioclip::SearchAudioclips(0, 10, $conditions, $f_operator);
} else {
    $r = Audioclip::SearchAudioclips(0, 10);
}

$clipCount = $r[0];
$clips = $r[1];
$operators = array('partial',
                   'full',
                   'prefix',
                   '=',
                   '<',
                   '<=',
                   '>',
                   '>='
             );

?>

<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite-audiosearch.js"></script>

<TABLE cellspacing="1" cellpadding="2" class="table_list">
<FORM method="POST" name="search" action="popup.php">
<INPUT type="hidden" name="row_1[active]" value="1" />
<?php
for ($c = 2; $c <= $maxCriteria; $c++) {
?>
<INPUT type="hidden" name="row_<?php p($c); ?>[active]" value="<?php p(${'row_'.$c}['active']); ?>" />
<?php
}
?>
<INPUT type="hidden" name="counter" value="<?php p($counter); ?>" />
<INPUT type="hidden" name="max_rows" value="<?php p($maxCriteria); ?>" />
<TR>
    <TD>
    <?php
    for ($i = 1; $i <= $maxCriteria; $i++) {
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
            _hs_defaults['row_<?php p($i); ?>'] = ['<?php p(${'row_'.$i}[0]); ?>', '<?php p(${'row_'.$i}[1]); ?>'];
        </script>
        <?php
        $rowStyle = (${'row_'.$i}['active'] != 1) ? 'display:none' : '';
        if ($i == 1) $rowStyle = '';
        ?>
        <DIV id="searchRow_<?php p($i); ?>" style="<?php p($rowStyle); ?>">
        <DIV class="audiosearch_container">
        <SELECT name="row_<?php p($i); ?>[0]" class="input_select" onchange="_hs_swapOptions(this.form, 'row_<?php p($i); ?>', 0">
        <?php
        foreach ($metatagLabel as $tag => $value) {
            camp_html_select_option($tag, '', $value);
        }
        ?>
        </SELECT>
        <SELECT name="row_<?php p($i); ?>[1]" class="input_select">
        <?php
            camp_html_select_option('partial', '', 'partial');
            camp_html_select_option('full', '', 'full');
            camp_html_select_option('prefix', '', 'prefix');
            camp_html_select_option('=', '', '=');
            camp_html_select_option('<', '', '<');
            camp_html_select_option('<=', '', '<=');
            camp_html_select_option('>', '', '>');
            camp_html_select_option('>=', '', '>=');
        ?>
        </SELECT>
        <INPUT type="text" name="row_<?php p($i); ?>[2]" class="input_text" size="25" maxlength="255" value="<?php p(${'row_'.$i}[2]); ?>" />
        <?php
        if ($i == 1) {
        ?>
        <INPUT type="button" class="button" name="addRow" onclick="SearchForm_addRow('<?php putGS("Maximum reached"); ?>');" value="<?php putGS("Add"); ?>" />
        <?php
        } else {
        ?>
        <INPUT type="button" class="button" name="dropRow_<?php p($i); ?>" onclick="SearchForm_dropRow('<?php p($i); ?>');" value="-" />
        <?php
        }
        ?>
        </DIV>
        </DIV id="searchRow_<?php p($i); ?>">
    <?php
    }
    ?>
    </TD>
</TR>
<TR>
    <TD align="center">
        <?php putGS("Operator"); ?>:
        <SELECT name="f_operator" class="input_select">
        <?php
            camp_html_select_option('or', $f_operator, 'Or');
            camp_html_select_option('and', $f_operator, 'And');
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