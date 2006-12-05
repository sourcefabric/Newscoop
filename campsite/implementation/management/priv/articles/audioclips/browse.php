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

if (count($clips) > 0) {
    $pagerUrl = camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php")."&";
    $pager =& new SimplePager($clipCount, $f_items_per_page, "f_audioclip_offset", $pagerUrl);
?>

<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite-checkbox.js"></script>

<script type="text/javascript">
function attach_submit(buttonElement)
{
    // Verify that at least one checkbox has been selected.
    checkboxes = document.forms.audioclip_list["f_audioclip_code[]"];
    if (checkboxes) {
        isValid = false;
        numCheckboxesChecked = 0;
        // Special case for single checkbox
        // (when there is only one article in the section).
        if (!checkboxes.length) {
            isValid = checkboxes.checked;
            numCheckboxesChecked = isValid ? 1 : 0;
        } else {
            // Multiple checkboxes
            for (var index = 0; index < checkboxes.length; index++) {
                if (checkboxes[index].checked) {
                    isValid = true;
                    numCheckboxesChecked++;
                }
            }
        }
        if (!isValid) {
            alert("<?php putGS("You must select at least one audioclip to attach."); ?>");
            return;
        }
    } else {
        return;
    }
    buttonElement.form.submit();
} // fn attach_submit
</script>

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