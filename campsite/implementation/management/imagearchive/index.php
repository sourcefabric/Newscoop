<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files();
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/CampsiteInterface.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/imagearchive/include.inc.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header('Location: /priv/logout.php');
	exit;
}

// Initialize input variables ///////////////////////////////////////////////////
$OrderBy = array_get_value($_REQUEST, 'order_by', 'id');
$OrderDirection = array_get_value($_REQUEST, 'order_direction', 'ASC');
$view = array_get_value($_REQUEST, 'view', 'thumbnail');
$ImageOffset = array_get_value($_REQUEST, 'image_offset', 0);
$SearchDescription = array_get_value($_REQUEST, 'search_description', '');
$SearchPhotographer = array_get_value($_REQUEST, 'search_photographer', '');
$SearchPlace = array_get_value($_REQUEST, 'search_place', '');
$SearchDate = array_get_value($_REQUEST, 'search_date', '');
$SearchInUse = array_get_value($_REQUEST, 'search_inuse', '');

$searchKeywords = array('search_description' => $SearchDescription,
						'search_photographer' => $SearchPhotographer,
						'search_place' => $SearchPlace,
						'search_date' => $SearchDate,
						'search_inuse' => $SearchInUse);
						
$Link = CreateImageLinks($searchKeywords, $OrderBy, $OrderDirection, $ImageOffset, $ImagesPerPage, $view);

///////////////////////////////////////////////////////////////////////

// build the links for ordering (search results) //////////////////////
if ($OrderDirection == 'DESC') {
	$ReverseOrderDirection = "ASC";
	$OrderSign = '<img src="/priv/img/down.png" border="0">';
} else {
	$ReverseOrderDirection = "DESC";
	$OrderSign = '<img src="/priv/img/up.png" border="0">';
}

$IdHref  = CAMPSITE_IMAGEARCHIVE_DIR
	.'?order_by=id'
	.$Link['search'];
$DescriptionHref  = CAMPSITE_IMAGEARCHIVE_DIR
	.'?order_by=description'
	.$Link['search'];
$PhotographerHref  = CAMPSITE_IMAGEARCHIVE_DIR
	.'?order_by=photographer'
	.$Link['search'];
$PlaceHref  = CAMPSITE_IMAGEARCHIVE_DIR
	.'?order_by=place'
	.$Link['search'];
$DateHref  = CAMPSITE_IMAGEARCHIVE_DIR
	.'?order_by=date'
	.$Link['search'];
$InUseHref = CAMPSITE_IMAGEARCHIVE_DIR
	.'?order_by=inuse'
	.$Link['search'];
///////////////////////////////////////////////////////////////////////
$DescriptionOrderIcon = '';
$PhotographerOrderIcon = '';
$PlaceOrderIcon = '';
$DateOrderIcon = '';
$InUseOrderIcon = '';
$IdOrderIcon = '';
switch ($OrderBy) {
case 'description':
	$DescriptionOrderIcon = $OrderSign;
	$DescriptionHref = CAMPSITE_IMAGEARCHIVE_DIR
		.'?order_by=description'
		.'&order_direction='.$ReverseOrderDirection
		.$Link['search'];
	break;
case 'photographer':
	$PhotographerOrderIcon = $OrderSign;
	$PhotographerHref = CAMPSITE_IMAGEARCHIVE_DIR
		.'?order_by=photographer'
		.'&order_direction='.$ReverseOrderDirection
		.$Link['search'];
	break;
case 'place':
	$PlaceOrderIcon = $OrderSign;
	$PlaceHref = CAMPSITE_IMAGEARCHIVE_DIR
		.'?order_by=place'
		.'&order_direction='.$ReverseOrderDirection
		.$Link['search'];
	break;
case 'date':
	$DateOrderIcon = $OrderSign;
	$DateHref = CAMPSITE_IMAGEARCHIVE_DIR
		.'?order_by=date'
		.'&order_direction='.$ReverseOrderDirection
		.$Link['search'];
	break;
case 'inuse':
	$InUseOrderIcon = $OrderSign;
	$InUseHref = CAMPSITE_IMAGEARCHIVE_DIR
		.'?order_by=inuse'
		.'&order_direction='.$ReverseOrderDirection
		.$Link['search'];
	break;
case 'id':
default:
	$IdOrderIcon = $OrderSign;
	$IdHref = CAMPSITE_IMAGEARCHIVE_DIR
		.'?order_by=id'
		.'&order_direction='.$ReverseOrderDirection
		.$Link['search'];
	break;
}
///////////////////////////////////////////////////////////////////////

$TotalImages = Image::GetTotalImages();
$imageSearch =& new ImageSearch($_REQUEST);
$imageSearch->run();
$imageData =& $imageSearch->getImages();
$NumImagesFound = $imageSearch->getNumImagesFound();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS('Images'); ?></TITLE>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['website_url'] ?>/css/admin_stylesheet.css">
</HEAD>
<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
			<DIV STYLE="font-size: 12pt"><B><?php putGS('Image Archive'); ?></B></DIV>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR><TD ALIGN=RIGHT>
	  <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
			<TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS('Home'); ?>"></A></TD><TD><A HREF="/priv/home.php" ><B><?php putGS('Home');  ?></B></A></TD>
			<TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS('Logout'); ?>"></A></TD><TD><A HREF="/priv/logout.php" ><B><?php putGS('Logout');  ?></B></A></TD>
		</TR>
	</TABLE>
  </TD></TR>
</TABLE>

<table>
  <tr>
<?php
if ($User->hasPermission('AddImage')) { ?>
    <td><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>add.php?<?php echo Image_GetSearchUrl($_REQUEST); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS('Add new image'); ?>"></A></TD><TD><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>add.php?<?php echo Image_GetSearchUrl($_REQUEST); ?>"><B><?php  putGS('Add new image'); ?></B></A></TD>
<?php } ?>
    
    <td><a href="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?view=<?php echo $view; ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS("Reset search conditions"); ?>"></a></td><td><a href="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?view=<?php echo $view; ?>"><b><?php echo putGS('Reset search conditions'); ?></b></a></td>

    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td><B><?php putGS('View', 'View'); ?>:</b></td>
    <td><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?<?php echo $Link['search'].$Link['order_by']; ?>&view=thumbnail"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS('Thumbnail'); ?>"></A></TD><TD><A HREF="index.php?<?php echo $Link['search'].$Link['order_by']; ?>&view=thumbnail"><B><?php putGS('Thumbnail'); ?></B></A></TD>
    <td><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?<?php echo $Link['search'].$Link['order_by']; ?>&view=gallery"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS('Gallery'); ?>"></A></TD><TD><A HREF="index.php?<?php echo $Link['search'].$Link['order_by']; ?>&view=gallery"><B><?php putGS('Gallery'); ?></B></A></TD>
    <td><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?<?php echo $Link['search'].$Link['order_by']; ?>&view=flat"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS('Text only'); ?>"></A></TD><TD><A HREF="index.php?<?php echo $Link['search'].$Link['order_by']; ?>&view=flat"><B><?php  putGS('Text only'); ?></B></A></TD>
  </tr>
</table>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" style="border: 1px solid #00008b; margin-bottom: 10px; margin-top: 5px;" align="center">
<form method="GET" action="index.php">
<input type="hidden" name="order_by" value="<?php echo $OrderBy; ?>">
<input type="hidden" name="order_direction" value="<?php echo $OrderDirection; ?>">
<input type="hidden" name="view" value="<?php echo $view; ?>">
<input type="hidden" name="image_offset" value="0">
<tr BGCOLOR="#C0D0FF">
	<td style="padding-left: 10px; color: #00008b;"><?php putGS('Description')?>:</td>
	<td><input type="text" name="search_description" value="<?php echo $SearchDescription; ?>" style="border: 1px solid #00008b; background-color: #f0f0ff; text-indent: 3px; width: 150px;"></td>
	<td style=" color: #00008b;"><?php putGS('Photographer'); ?>:</td>
	<td><input type="text" name="search_photographer" value="<?php echo $SearchPhotographer; ?>"  style="border: 1px solid #00008b; background-color: #f0f0ff; text-indent: 3px; width: 100px;"></td>
	<td style=" color: #00008b;"><?php putGS('Place'); ?>:</td>
	<td><input type="text" name="search_place" value="<?php echo $SearchPlace; ?>" style="border: 1px solid #00008b; background-color: #f0f0ff; text-indent: 3px; width: 100px;"></td>
	<td style=" color: #00008b;"><?php putGS('Date'); ?>:</td>
	<td><input type="text" name="search_date" value="<?php echo $SearchDate; ?>" style="border: 1px solid #00008b; background-color: #f0f0ff; text-indent: 3px; width: 80px;"></td>
	<td><input type="submit" name="submit_button" value="Search" class="button"></td>
</tr>
</table>

<?php
if (count($imageData) > 0) {
    switch ($view) {
    case 'flat':
    	include('view_flat.inc.php');
    	break;
    case 'gallery':
    	include('view_gallery.inc.php');
    	break;
    case 'thumbnail':
    default:
    	include('view_thumbnail.inc.php');
        break;
    }

} else {
    ?><BLOCKQUOTE><LI><?php  putGS('No images.'); ?></LI></BLOCKQUOTE>
<?php
}
?>
<?php CampsiteInterface::CopyrightNotice(); ?>