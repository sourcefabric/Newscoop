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
$isSearch = isset($_REQUEST['is_search'])?$_REQUEST['is_search']:0;
$OrderBy = isset($_REQUEST['order_by'])?$_REQUEST['order_by']:'id';
$OrderDirection = isset($_REQUEST['order_direction'])?$_REQUEST['order_direction']:'ASC';
$view = isset($_REQUEST['view'])?$_REQUEST['view']:'thumbnail';
$ImageOffset = isset($_REQUEST['image_offset'])?$_REQUEST['image_offset']:0;
$ImagesPerPage = 5;

$SearchDescription = isset($_REQUEST['search_description'])?$_REQUEST['search_description']:null;
$SearchPhotographer = isset($_REQUEST['search_photographer'])?$_REQUEST['search_photographer']:null;
$SearchDate = isset($_REQUEST['search_date'])?$_REQUEST['search_date']:null;
$SearchInUse = isset($_REQUEST['search_inuse'])?$_REQUEST['search_inuse']:null;

$searchKeywords = array('search_description' => $SearchDescription,
						'search_photographer' => $SearchPhotographer,
						'search_date' => $SearchDate,
						'search_inuse' => $SearchInUse);
						
$Link = cImgLink($searchKeywords, $OrderBy, $OrderDirection, $ImageOffset, $ImagesPerPage, $view);
//$Link =& new ImageLink($_REQUEST);

// SQL conditions for a search query /////////////////////////
$WhereAdd = '';
if ($isSearch && ($SearchDescription || $SearchPhotographer || $SearchDate || $SearchInUse)) {
	if ($SearchDescription) {
		$WhereAdd .= " AND i.Description LIKE '%$SearchDescription%'";
	}
	if ($SearchPhotographer) {
		$WhereAdd .= " AND i.Photographer LIKE '%$SearchPhotographer%'";
	}
	if ($SearchDate) {
		$WhereAdd .= " AND i.Date LIKE '%$SearchDate%'";
	}
	if ($SearchInUse) {
		if ($SearchInUse) {
            $not = "NOT";
        }
        $WhereAdd .= " AND a.IdImage IS $not NULL";
	}
}
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
	.$Link['search']
	.'&id=0';
$DescriptionHref  = CAMPSITE_IMAGEARCHIVE_DIR
	.'?order_by=description'
	.$Link['search']
	.'&description=0';
$PhotographerHref  = CAMPSITE_IMAGEARCHIVE_DIR
	.'?order_by=photographer'
	.$Link['search']
	.'&photographer=0';
$DateHref  = CAMPSITE_IMAGEARCHIVE_DIR
	.'?order_by=date'
	.$Link['search']
	.'&date=0';
$InUseHref = CAMPSITE_IMAGEARCHIVE_DIR
	.'?order_by=inuse'
	.$Link['search']
	.'&inuse=0';
///////////////////////////////////////////////////////////////////////

switch ($OrderBy) {
case 'description':
	$Order .= 'ORDER BY i.Description '.$OrderDirection;
	$DesciptionOrderIcon = $OrderSign;
	$DescriptionHref = CAMPSITE_IMAGEARCHIVE_DIR
		.'?order_by=description'
		.'&order_direction='.$ReverseOrderDirection
		.$Link['search']
		.'&description=0';
	break;

case 'photographer':
	$Order = 'ORDER BY i.Photographer '.$OrderDirection;
	$PhotographerOrderIcon = $OrderSign;
	$PhotographerHref = CAMPSITE_IMAGEARCHIVE_DIR
		.'?order_by=photographer'
		.'&order_direction='.$ReverseOrderDirection
		.$Link['search']
		.'&photographer=0';
	break;

case 'date':
	$Order = 'ORDER BY i.Date '.$OrderDirection;
	$DateOrderIcon = $OrderSign;
	$DateHref = CAMPSITE_IMAGEARCHIVE_DIR
		.'?order_by=date'
		.'&order_direction='.$ReverseOrderDirection
		.$Link['search']
		.'&date=0';
	break;

case 'inuse':
	$Order = 'ORDER BY inUse '.$OrderDirection;
	$InUseOrderIcon = $OrderSign;
	$InUseHref = CAMPSITE_IMAGEARCHIVE_DIR
		.'?order_by=inuse'
		.'&order_direction='.$ReverseOrderDirection
		.$Link['search']
		.'&inuse=0';
	break;

case 'id':
default:
	$Order = 'ORDER BY i.Id '.$OrderDirection;
	$IdOrderIcon = $OrderSign;
	$IdHref = CAMPSITE_IMAGEARCHIVE_DIR
		.'?order_by=id'
		.'&order_direction='.$ReverseOrderDirection
		.$Link['search'].'&id=0';
	break;
}
///////////////////////////////////////////////////////////////////////

$queryStr = "SELECT i.Id, i.Description, i.Photographer, i.Place, i.ContentType, i.Date, COUNT(a.IdImage) AS inUse"
		  	." FROM Images AS i"
		  	." LEFT JOIN ArticleImages AS a On i.Id=a.IdImage"
		  	." WHERE 1 $WhereAdd"
		    ." GROUP BY i.Id"
		    ." $Order LIMIT $ImageOffset, ".$ImagesPerPage;
//echo $queryStr;
//exit;
$query = $Campsite['db']->Execute($queryStr);
$TotalImages = Image::GetTotalImages();

// Create image templates
$imageData = array();
while ($row = $query->FetchRow()) {
	$tmpImage =& new Image();
	$tmpImage->fetch($row);
	$template = $tmpImage->toTemplate();
	$template["in_use"] = $row["inUse"];
	$imageData[] = $template;
}
//echo "<pre>";
//print_r($imageData);
//echo "</pre>";
//exit;
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
    <td><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>add.php?v=<?php echo $v; ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS('Add new image'); ?>"></A></TD><TD><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>add.php?v=<?php echo $v; ?>"><B><?php  putGS('Add new image'); ?></B></A></TD>
<?php } ?>
    <td><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>searchform.php?<?php echo $Link['search'].$Link['order_by']; ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS('Search for images'); ?>"></A></TD><TD><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>searchform.php?<?php echo $Link['search'].$Link['order_by']; ?>" ><B><?php putGS('Search for images'); ?></B></A></TD>
    
    <td><a href="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?view=<?php echo $view; ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS("Reset search conditions"); ?>"></a></td><td><a href="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?view=<?php echo $view; ?>"><b><?php echo putGS('Reset search conditions'); ?></b></a></td>

    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td><B><?php putGS('View', 'View'); ?>:</b></td>
    <td><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?<?php echo $Link['search'].$Link['order_by']; ?>&view=thumbnail"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS('Thumbnail'); ?>"></A></TD><TD><A HREF="index.php?<?php echo $Link['search'].$Link['order_by']; ?>&view=thumbnail"><B><?php putGS('Thumbnail'); ?></B></A></TD>
    <td><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?<?php echo $Link['search'].$Link['order_by']; ?>&view=gallery"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS('Gallery'); ?>"></A></TD><TD><A HREF="index.php?<?php echo $Link['search'].$Link['order']; ?>&view=gallery"><B><?php putGS('Gallery'); ?></B></A></TD>
    <td><A HREF="<?php echo CAMPSITE_IMAGEARCHIVE_DIR; ?>index.php?<?php echo $Link['search'].$Link['order_by']; ?>&view=flat"><IMG SRC="/priv/img/tol.gif" BORDER="0" alt="<?php  putGS('Text only'); ?>"></A></TD><TD><A HREF="index.php?<?php echo $Link['search'].$Link['order']; ?>&v=f"><B><?php  putGS('Text only'); ?></B></A></TD>
  </tr>
</table>

<table>
<tr>
	<td>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
	<tr BGCOLOR="#C0D0FF"><td colspan="2"><b><?php putGS('Search conditions'); ?></b></td></tr>
	<?php
		if (!is_null($SearchDescription)) {
			?>
			<tr <?php trColor(); ?>><td><b><?php putGS('Description');?>:</b></td><td><?php echo $SearchDescription; ?></td></tr>
			<?php
		}
		if (!is_null($SearchPhotographer)) {
			?>
			<tr <?php trColor(); ?>><td><b><?php putGS('Photographer'); ?>:</b></td><td><?php echo $SearchPhotographer; ?></td></tr>
			<?php
		}
		if (!is_null($SearchDate)) {
			?>
			<tr <?php trColor(); ?>><td><b><?php putGS('Date') ?>:</b></td><td><?php echo $SearchDate; ?></td></tr>
			<?php
		}
		if (!is_null($SearchInUse)) {
			?>
			<tr <?php trColor() ?>><td><b><?php putGS('In use') ?>:</b></td><td><?php echo $SearchInUse; ?></td></tr>
			<?php
		}
	?>
	</table>
	</tr>
	</td>
</tr>
</table>
<P>

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

