<?php
camp_load_translation_strings("article_images");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/SimplePager.php');
require_once($_SERVER['DOCUMENT_ROOT']."/classes/XR_CcClient.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Audioclip.php");

/**** XML RPC WORKING EXAMPLES ****/

$xrc =& XR_CcClient::Factory($mdefs);

echo "<pre>\n";

/**** search metadata ****/
$sessid = camp_session_get('cc_sessid', '');
$criteria = array('filetype' => 'audioclip',
                  'operator' => 'and',
                  'limit' => 10,
                  'offset' => 0,
                  'conditions' => array()
                  );
echo "searchMetadata response:\n";
$r = Audioclip::SearchAudioclips(0, 10);
foreach ($r as $clip) {
	print_r($clip->m_metaData);
	echo "available meta tags:\n";
	print_r($clip->getAvailableMetaTags());
}

echo "</pre>\n";
exit;

/**************************/

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

$TotalImages = Image::GetTotalImages();
$imageSearch =& new ImageSearch($f_search_string, $f_order_by, $f_order_direction, $f_image_offset, $f_items_per_page);
$imageSearch->run();
$imageData = $imageSearch->getImages();
$NumImagesFound = $imageSearch->getNumImagesFound();

//$orderDirectionUrl = camp_html_article_url($articleObj, $f_language_id, 'images/popup.php');

if (count($imageData) > 0) {
    $pagerUrl = camp_html_article_url($articleObj, $f_language_id, "images/popup.php")."&";
    $pager =& new SimplePager($NumImagesFound, $f_items_per_page, "f_image_offset", $pagerUrl);

?>
<table class="action_buttons">
<tr><td><?php     echo $pager->render(); ?></td></tr>
</table>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="6" class="table_list">
<TR class="table_list_header">
    <?php if ($articleObj->userCanModify($g_user)) { ?>
    <TD ALIGN="center" VALIGN="top" style="padding: 3px;"></TD>
	<?php } ?>
    <TD ALIGN="LEFT" VALIGN="TOP">
      <?php  putGS("Title"); ?>
    </TD>
    <TD ALIGN="LEFT" VALIGN="TOP">
      <?php  putGS("Creator"); ?>
    </TD>
    <TD ALIGN="LEFT" VALIGN="TOP">
      <?php  putGS("Duration"); ?>
    </TD>
</TR>
<?php
$color = 0;
foreach ($imageData as $image) {
    ?>
    <TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
        <?php
        if ($articleObj->userCanModify($g_user)) { ?>
    		<form method="POST" action="do_link.php">
			<input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
			<input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
			<input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
    		<input type="hidden" name="f_image_id" value="<?php echo $image['id']; ?>">
        	<TD ALIGN="CENTER">
        		<input type="checkbox" value="143_1" name="f_article_code[]" id="checkbox_0" class="input_checkbox" onclick="checkboxClick(this, 0);">
          	</TD>
       		</form>
        	<?php
     	}
     	else {
     		?>
        	<TD ALIGN="CENTER">&nbsp;</TD>
     		<?php
     	}
        ?>
        <TD ALIGN="center">
            <A HREF="<?php echo
            camp_html_article_url($articleObj, $f_language_id, "images/view.php", camp_html_article_url($articleObj, $f_language_id, "images/popup.php"))
            .'&f_image_id='.$image['id']; ?>">
              <img src="<?php echo $image['thumbnail_url']; ?>" border="0"><br>
              <?php echo $image['width'].'x'.$image['height']; ?>
            </a>
        </TD>
        <TD style="padding-left: 5px;">
            <A HREF="<?php echo camp_html_article_url($articleObj, $f_language_id, "images/view.php", camp_html_article_url($articleObj, $f_language_id, "images/popup.php"))
            .'&f_image_id='.$image['id']; ?>"><?php echo htmlspecialchars($image['description']); ?></A>
        </TD>
        <TD style="padding-left: 5px;">
            <?php echo htmlspecialchars($image['photographer']); ?>&nbsp;
        </TD>
    </TR>
<?php
}

?>
<tr>
	<td colspan="5" nowrap>
	<?php putGS('$1 images found', $NumImagesFound); ?></TD>
</tr>
</table>
<table class="action_buttons">
<TR>
    <TD>
    <?php
    echo $pager->render();
    ?></td>
</TR>
</TABLE>
<?php
}

//camp_html_copyright_notice(); ?>
