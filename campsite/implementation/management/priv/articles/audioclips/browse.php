<?php
camp_load_translation_strings("article_images");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/SimplePager.php');
require_once($_SERVER['DOCUMENT_ROOT']."/classes/XR_CcClient.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Audioclip.php");

/**** XML RPC WORKING EXAMPLES ****

echo "<pre>\n";

$sessid = camp_session_get('cc_sessid', '');
$criteria = array('filetype' => 'audioclip',
                  'operator' => 'and',
                  'limit' => 10,
                  'offset' => 0,
                  'conditions' => array()
                  );
echo "searchMetadata response:\n";
$r = Audioclip::SearchAudioclips(0, 10);
$clipCount = $r[0];
$clips = $r[1];

foreach ($clips as $clip) {
	echo "clip:\n";
	echo $clip->getMetatagValue('title') .', '.$clip->getMetatagValue('creator').', '.$clip->getMetatagValue('extent')."\n";
}

echo "</pre>\n";

**************************/

$f_order_by = camp_session_get('f_order_by', 'id');
$f_order_direction = camp_session_get('f_order_direction', 'ASC');
$f_audioclip_offset = camp_session_get('f_audioclip_offset', 0);
$f_search_string = camp_session_get('f_search_string', '');
$f_items_per_page = camp_session_get('f_items_per_page', 4);
if ($f_items_per_page < 4) {
    $f_items_per_page = 4;
}

if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
    exit;
}

// Gets all the available audioclips
$r = Audioclip::SearchAudioclips(0, 10);
$clipCount = $r[0];
$clips = $r[1];

// Build the links for ordering search results
$OrderSign = '';
if ($f_order_direction == 'DESC') {
    $ReverseOrderDirection = "ASC";
    $OrderSign = "<img src=\"".$Campsite["ADMIN_IMAGE_BASE_URL"]."/descending.png\" border=\"0\">";
} else {
    $ReverseOrderDirection = "DESC";
    $OrderSign = "<img src=\"".$Campsite["ADMIN_IMAGE_BASE_URL"]."/ascending.png\" border=\"0\">";
}

if (count($clips) > 0) {
    $pagerUrl = camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php")."&";
    $pager =& new SimplePager($clipCount, $f_items_per_page, "f_audioclip_offset", $pagerUrl);
?>
<TABLE class="action_buttons">
<TR>
    <TD>
    <?php echo $pager->render(); ?>
    </TD>
</TR>
</TABLE>
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