<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SimplePager.php');

$translator = \Zend_Registry::get('container')->getService('translator');
$f_order_by = camp_session_get('f_order_by', 'id');
$f_order_direction = camp_session_get('f_order_direction', 'ASC');
$f_image_offset = camp_session_get('f_image_offset', 0);
$f_search_string = camp_session_get('f_search_string', '');
$f_items_per_page = camp_session_get('f_items_per_page', 4);
$f_source_filter_out = !isset($_REQUEST['f_source_all']) ? 'newsfeed' : false;

if ($f_items_per_page < 4) {
	$f_items_per_page = 4;
}

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $_SERVER['REQUEST_URI'], true);
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
$imageSearch = new ImageSearch($f_search_string, $f_order_by, $f_order_direction, $f_image_offset, $f_items_per_page);
if ($f_source_filter_out) {
    $imageSearch->setFilter( "Source", $f_source_filter_out, true );
}
$imageSearch->run();
$imageData = $imageSearch->getImages();
$NumImagesFound = $imageSearch->getNumImagesFound();

//$orderDirectionUrl = camp_html_article_url($articleObj, $f_language_id, 'images/popup.php');

?>

    <form method="POST" action="/<?php echo $ADMIN; ?>/articles/images/popup.php" id="searchform">
<input type="hidden" name="f_order_direction" value="<?php echo $f_order_direction; ?>">
<input type="hidden" name="f_image_offset" value="0">
<input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
<input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
<input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="box_table">
<tr>
	<td><input type="submit" name="submit_button" value="Search" class="button"></td>
	<td><input type="text" name="f_search_string" value="<?php echo $f_search_string; ?>" class="input_text" style="width: 150px;"></td>
	<td>
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td>Order by:</td>
			<td>
				<select name="f_order_by" class="input_select" onchange="this.form.submit();">
				<?PHP
				camp_html_select_option('id', $f_order_by, $translator->trans("Most Recently Added"));
				camp_html_select_option('last_modified', $f_order_by, $translator->trans("Most Recently Modified"));
				camp_html_select_option('description', $f_order_by, $translator->trans("Description"));
				camp_html_select_option('photographer', $f_order_by, $translator->trans("Photographer"));
				camp_html_select_option('place', $f_order_by, $translator->trans("Place"));
				camp_html_select_option('date', $f_order_by, $translator->trans("Date"));
				camp_html_select_option('inuse', $f_order_by, $translator->trans("In use"));
				?>
				</select>
			</td>
			<td>
                <a href="/<?php echo $ADMIN; ?>/articles/images/popup.php?f_language_id=<?php p($f_language_id); ?>&f_language_selected=<?php p($f_language_selected); ?>&f_article_number=<?php p($f_article_number); ?>&f_order_direction=<?php p($ReverseOrderDirection); ?>"><?php p($OrderSign); ?></a>
			</td>
		</tr>
		</table>
	</td>
	<td><?php echo $translator->trans("Items per page"); ?>: <input type="text" name="f_items_per_page" value="<?php p($f_items_per_page); ?>" class="input_text" size="4"></td>
</tr>
<tr>
	<td colspan="4">
		<input name="f_source_all" id="f_source_all" value="newsfeed" type="checkbox"
			<?php if (!$f_source_filter_out) : ?>checked="checked"<?php endif; ?>
			onclick="console.log(this.checked); document.getElementById('searchform').submit()" />
	    <label for="f_source_all"><?php echo $translator->trans('Show all', array(), 'article_images'); ?></label>
	</td>
</tr>
</table>
</form>

<?php
if (count($imageData) > 0) {
    $pagerUrl = camp_html_article_url($articleObj, $f_language_id, "images/popup.php")."&".
        (!$f_source_filter_out ? "f_source_all=newsfeed&" : "");
    $pager = new SimplePager($NumImagesFound, $f_items_per_page, "f_image_offset", $pagerUrl);

?>
<table class="action_buttons">
<tr><td><?php     echo $pager->render(); ?></td></tr>
</table>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="6" class="table_list">
<TR class="table_list_header">
    <?php if ($articleObj->userCanModify($g_user)) { ?>
    <TD ALIGN="center" VALIGN="top" style="padding: 3px;"><B><?php p($translator->trans("Attach")); ?></B></TD>
	<?php } ?>
    <TD ALIGN="LEFT" VALIGN="TOP">
      <?php echo $translator->trans("Thumbnail"); ?>
    </TD>
    <TD ALIGN="LEFT" VALIGN="TOP">
      <?php echo $translator->trans("Description"); ?>
    </TD>
    <TD ALIGN="LEFT" VALIGN="TOP">
      <?php echo $translator->trans("Photographer"); ?>
    </TD>
    <TD ALIGN="LEFT" VALIGN="TOP">
      <?php echo $translator->trans("Place"); ?>
    </TD>
    <TD ALIGN="LEFT" VALIGN="TOP">
      <?php echo $translator->trans("Date").'<BR><SMALL>(yyyy-mm-dd)</SMALL>'; ?>
    </TD>
    <TD ALIGN="center" VALIGN="top" style="padding: 3px;" nowrap>
      <?php echo $translator->trans("In use"); ?>
    </TD>
</TR>
<?php
$color = 0;
foreach ($imageData as $image) {
    ?>
    <TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
        <?php
        if ($articleObj->userCanModify($g_user)) { ?>
            <TD ALIGN="CENTER">
            <form method="POST" action="/<?php echo $ADMIN; ?>/articles/images/do_link.php">
			<?php echo SecurityToken::FormParameter(); ?>
			<input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>">
			<input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>">
			<input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>">
    		<input type="hidden" name="f_image_id" value="<?php echo $image['id']; ?>">
				<input type="image" src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png">
            </form>
          	</TD>
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
        <TD style="padding-left: 5px;">
            <?php echo htmlspecialchars($image['place']); ?>&nbsp;
        </TD>
        <TD style="padding-left: 5px;">
            <?php echo htmlspecialchars($image['date']); ?>&nbsp;
        </TD>
        <TD align="center">
            <?php echo htmlspecialchars($image['in_use']); ?>&nbsp;
        </TD>
    </TR>
<?php
}

?>
<tr>
	<td colspan="5" nowrap>
	<?php echo $translator->trans('$1 images found', array('$1' => $NumImagesFound), 'article_images'); ?></TD>
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
