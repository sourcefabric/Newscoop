<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("imagearchive");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/SimplePager.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
camp_load_language("api");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

// Initialize input variables ///////////////////////////////////////////////////
$f_order_by = camp_session_get('f_order_by', 'id');
$f_order_direction = camp_session_get('f_order_direction', 'ASC');
$f_image_offset = camp_session_get('f_image_offset', 0);
$f_search_string = camp_session_get('f_search_string', '');
$f_items_per_page = camp_session_get('f_items_per_page', 8);

// Build the links for ordering search results
$OrderSign = '';
if ($f_order_direction == 'DESC') {
	$ReverseOrderDirection = "ASC";
	$OrderSign = "<img src=\"".$Campsite["ADMIN_IMAGE_BASE_URL"]."/descending.png\" border=\"0\">";
} else {
	$ReverseOrderDirection = "DESC";
	$OrderSign = "<img src=\"".$Campsite["ADMIN_IMAGE_BASE_URL"]."/ascending.png\" border=\"0\">";
}
$orderDirectionUrl = "/$ADMIN/imagearchive/index.php?&f_order_direction=$ReverseOrderDirection";

$TotalImages = Image::GetTotalImages();
$imageSearch =& new ImageSearch($f_search_string, $f_order_by, $f_order_direction, $f_image_offset, $f_items_per_page);
$imageSearch->run();
$imageData = $imageSearch->getImages();
$NumImagesFound = $imageSearch->getNumImagesFound();
$uploadedByUsers = Image::GetUploadUsers();

$crumbs = array();
$crumbs[] = array(getGS('Content'), "");
$crumbs[] = array(getGS('Image Archive'), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS('Images'); ?></TITLE>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
</HEAD>
<BODY>

<?php echo $breadcrumbs; ?>
<p>
<table cellpadding="0" cellspacing="0" class="action_buttons" style="padding-bottom: 5px;">
<tr>
<?php
if ($User->hasPermission('AddImage')) { ?>
    <td>
    	<A HREF="add.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0" alt="<?php  putGS('Add new image'); ?>"></A>
    </TD>
    <TD style="padding-left: 3px;">
    	<A HREF="add.php"><B><?php  putGS('Add new image'); ?></B></A>
    </TD>
<?php } ?>

</tr>
</table>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" class="table_input" style="margin-bottom: 10px; margin-top: 5px;" align="center">
<form method="POST" action="index.php">
<input type="hidden" name="f_order_direction" value="<?php echo $f_order_direction; ?>">
<input type="hidden" name="f_image_offset" value="0">
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
				camp_html_select_option('id', $f_order_by, getGS("Most Recently Added"));
				camp_html_select_option('last_modified', $f_order_by, getGS("Most Recently Modified"));
				camp_html_select_option('description', $f_order_by, getGS("Description"));
				camp_html_select_option('photographer', $f_order_by, getGS("Photographer"));
				camp_html_select_option('place', $f_order_by, getGS("Place"));
				camp_html_select_option('date', $f_order_by, getGS("Date"));
				camp_html_select_option('inuse', $f_order_by, getGS("In use"));
				?>
				</select>
			</td>
			<td>
				<a href="<?php p($orderDirectionUrl); ?>"><?php p($OrderSign); ?></a>
			</td>
		</tr>
		</table>
	</td>
	<td><?php putGS("Items per page"); ?>: <input type="text" name="f_items_per_page" value="<?php p($f_items_per_page); ?>" class="input_text" size="4"></td>
</tr>
</form>
</table>

<?php
if (count($imageData) > 0) {
  	$pagerUrl = "/$ADMIN/imagearchive/index.php?";
    $pager =& new SimplePager($NumImagesFound, $f_items_per_page, "f_image_offset", $pagerUrl);
?>
<table class="action_buttons">
<TR>
    <TD>
    <?php
    echo $pager->render();
    ?></td>
</TR>
</TABLE>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="6" class="table_list">
<TR class="table_list_header">
    <TD ALIGN="LEFT" VALIGN="TOP">
   		<?php  putGS("Thumbnail"); ?>
    </TD>
    <TD ALIGN="LEFT" VALIGN="TOP">
   		<?php  putGS("Description <SMALL>(Click to view details)</SMALL>"); ?>
    </TD>
    <TD ALIGN="LEFT" VALIGN="TOP">
   		<?php  putGS("Photographer"); ?>
    </TD>
    <TD ALIGN="LEFT" VALIGN="TOP">
   		<?php  putGS("Place"); ?>
    </TD>
    <TD ALIGN="LEFT" VALIGN="TOP">
   		<?php  putGS("Date <SMALL>(yyyy-mm-dd)</SMALL>"); ?>
    </TD>
    <TD ALIGN="LEFT" VALIGN="TOP">
   		<?php  putGS("In use"); ?>
    </TD>
    <?php
    if ($User->hasPermission('DeleteImage')) { ?>
    <TD ALIGN="center" VALIGN="TOP" style="padding: 3px;"><?php  putGS("Delete"); ?></TD>
<?php  } ?>
</TR>
<?php
$color = 0;
foreach ($imageData as $image) {
    ?>
    <TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
        <TD ALIGN="center">
            <A HREF="edit.php?f_image_id=<?php  echo $image['id']; ?>">
              <img src="<?php echo $image['thumbnail_url']; ?>" border="0">
            </a>
        </TD>
        <TD style="padding-left: 5px;">
            <A HREF="edit.php?f_image_id=<?php  echo $image['id']; ?>"><?php echo htmlspecialchars($image['description']); ?></A>
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
            <?php echo $image['in_use']; ?>&nbsp;
        </TD>
        <?php
        if ($User->hasPermission('DeleteImage')) {
        	if (!$image['in_use']) { ?>
            	<TD ALIGN="CENTER">
                <A HREF="do_del.php?f_image_id=<?php echo $image['id']; ?>" onclick="return confirm('<?php putGS("Are you sure you want to delete the image \\'$1\\'?", camp_javascriptspecialchars($image['description'])); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php putGS('Delete image $1',htmlspecialchars($image['description'])); ?>"></A>
              	</TD>
            	<?php
         	}
         	else {
         		?>
            	<TD ALIGN="CENTER">&nbsp;</TD>
         		<?php
         	}
        }
        ?>
    </TR>
<?php
}

?>
<td colspan="3"><?php putGS('$1 images found', $NumImagesFound); ?></TD>
</TR>
</TABLE>
<table class="action_buttons">
<TR>
    <TD>
    <?php
    echo $pager->render();
    ?></td>
</TR>
</TABLE>
<?php
} else {
    ?><BLOCKQUOTE><LI><?php  putGS('No images.'); ?></LI></BLOCKQUOTE>
<?php
}
?>
<?php camp_html_copyright_notice(); ?>