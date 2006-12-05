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

<script type="text/javascript">
/**
 * This array is used to remember mark status of rows in browse mode
 */
var marked_row = new Array;
var default_class = new Array;

function checkAll()
{
	for (i = 0; i < <?php p($clipCount); ?>; i++) {
		document.getElementById("rw_"+i).className = 'list_row_click';
		document.getElementById("checkbox_"+i).checked = true;
        marked_row[i] = true;
	}
} // fn checkAll


function uncheckAll()
{
	for (i = 0; i < <?php p($clipCount); ?>; i++) {
		document.getElementById("rw_"+i).className = default_class[i];
		document.getElementById("checkbox_"+i).checked = false;
        marked_row[i] = false;
	}
} // fn uncheckAll

/**
 * Sets/unsets the pointer and marker in browse mode
 *
 * @param   object    the table row
 * @param   integer  the row number
 * @param   string    the action calling this script (over, out or click)
 * @param   string    the default class
 *
 * @return  boolean  whether pointer is set or not
 */
function setPointer(theRow, theRowNum, theAction)
{
	newClass = null;
    // 4. Defines the new class
    // 4.1 Current class is the default one
    if (theRow.className == default_class[theRowNum]) {
        if (theAction == 'over') {
            newClass = 'list_row_hover';
        }
    }
    // 4.1.2 Current color is the hover one
    else if (theRow.className == 'list_row_hover'
             && (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {
        if (theAction == 'out') {
            newClass = default_class[theRowNum];
        }
    }

    if (newClass != null) {
    	theRow.className = newClass;
    }
    return true;
} // end of the 'setPointer()' function

/**
 * Change the color of the row when the checkbox is selected.
 *
 * @param object  The checkbox object.
 * @param int     The row number.
 */
function checkboxClick(theCheckbox, theRowNum)
{
	if (theCheckbox.checked) {
        newClass = 'list_row_click';
        marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])
                              ? true
                              : null;
	} else {
        newClass = 'list_row_hover';
        marked_row[theRowNum] = false;
	}
   	row = document.getElementById("rw_"+theRowNum);
   	row.className = newClass;
} // fn checkboxClick

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

<TABLE class="table_actions">
<TR>
    <TD align="right">
        <INPUT type="button" class="button" value="<?php putGS("Select All"); ?>" onclick="checkAll();">
        <INPUT type="button" class="button" value="<?php putGS("Select None"); ?>" onclick="uncheckAll();">
    </TD>
</TR>
</TABLE>
<TABLE border="0" cellspacing="1" cellpadding="6" class="table_list">
<FORM method="POST" name="audioclip_list" action="do_link.php">
<INPUT type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>" />
<INPUT type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>" />
<INPUT type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>" />
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
    <TD colspan="2" nowrap>
    <?php putGS('$1 audioclips found', $clipCount); ?>
    </TD>
    <TD colspan="2" align="right">
        <INPUT type="button" class="button" onclick="attach_submit(this);" value="Attach" />
    </TD>
</TR>
</FORM>
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