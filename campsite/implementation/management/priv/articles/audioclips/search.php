<?php
camp_load_translation_strings("article_images");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Audioclip.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/SimplePager.php');


$f_order_by = camp_session_get('f_order_by', 'id');
$f_order_direction = camp_session_get('f_order_direction', 'ASC');
$f_image_offset = camp_session_get('f_image_offset', 0);
$f_search_string = camp_session_get('f_search_string', '');
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

// Gets all the available audioclips
$r = Audioclip::SearchAudioclips(0, 10);
$clipCount = $r[0];
$clips = $r[1];

/*
$TotalImages = Image::GetTotalImages();
$imageSearch =& new ImageSearch($f_search_string, $f_order_by, $f_order_direction, $f_image_offset, $f_items_per_page);
$imageSearch->run();
$imageData = $imageSearch->getImages();
$NumImagesFound = $imageSearch->getNumImagesFound();
*/

//$orderDirectionUrl = camp_html_article_url($articleObj, $f_language_id, 'images/popup.php');
if (count($clips) > 0) {
    $pagerUrl = camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php")."&";
    $pager =& new SimplePager($clipCount, $f_items_per_page, "f_audioclip_offset", $pagerUrl);

?>

<FORM method="POST" action="">
<TABLE cellspacing="1" cellpadding="2" class="table_list">
<TR>
    <TD>
        <SELECT name="f_metatag" class="input_select">
        <?php
        foreach ($metatagLabel as $tag => $value) {
            camp_html_select_option($tag, '', $value);
        }
        ?>
        </SELECT>
        <SELECT name="" class="input_select">
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
        <INPUT type="text" class="input_text" size="25" maxlength="255" />
        <INPUT type="button" class="button" value="<?php putGS("Add"); ?>" />
    </TD>
</TR>
<TR>
    <TD align="center">
        <?php putGS("Operator"); ?>:
        <SELECT name="" class="input_select">
        <?php
            camp_html_select_option('or', '', 'Or');
            camp_html_select_option('and', '', 'And');
        ?>
        </SELECT>
    </TD>
</TR>
<TR>
    <TD align="right">
        <INPUT type="button" name="" class="button" value="<?php putGS("Submit"); ?>" />
    </TD>
</TR>
</TABLE>
</FORM>
<TABLE border="0" cellspacing="1" cellpadding="6" class="table_list">
<TR class="table_list_header">
    <?php if ($articleObj->userCanModify($g_user)) { ?>
    <TD align="center" valign="top" style="padding: 3px;"></TD>
    <?php } ?>
    <TD align="left" valign="top">
    <?php putGS("Title"); ?>
    </TD>
    <TD align="left" valign="top">
    <?php putGS("Creator"); ?>
    </TD>
    <TD align="left" valign="top">
    <?php putGS("Duration"); ?>
    </TD>
</TR>
<?php
    require('cliplist.php');
?>
<TR>
    <TD colspan="4" nowrap>
    <?php putGS('$1 audioclips found', $clipCount); ?>
    </TD>
</TR>
</TABLE>
<TABLE class="action_buttons">
<TR>
    <TD>
    <?php echo $pager->render(); ?>
    </TD>
</TR>
</TABLE>
<?php
}
?>