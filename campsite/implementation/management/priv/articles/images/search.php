<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_images");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/imagearchive/include.inc.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$OrderBy = Input::Get('order_by', 'string', 'id', true);
$OrderDirection = Input::Get('order_direction', 'string', 'ASC', true);
$view = Input::Get('view', 'string', 'thumbnail', true);
$ImageOffset = Input::Get('image_offset', 'int', 0, true);
$SearchDescription = Input::Get('search_description', 'string', '', true);
$SearchPhotographer = Input::Get('search_photographer', 'string', '', true);
$SearchPlace = Input::Get('search_place', 'string', '', true);
$SearchDate = Input::Get('search_date', 'string', '', true);
$SearchInUse = Input::Get('search_inuse', 'string', '', true);
$SearchUploadedBy = Input::Get('search_uploadedby', 'int', '', true);
	
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}

$imageNav =& new ImageNav(CAMPSITE_IMAGEARCHIVE_IMAGES_PER_PAGE, $view);
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);

///////////////////////////////////////////////////////////////////////
$ImagesPerPage = 8;

// build the links for ordering (search results) //////////////////////
if ($OrderDirection == 'DESC') {
	$ReverseOrderDirection = "ASC";
	$OrderSign = "<img src=\"".$Campsite["ADMIN_IMAGE_BASE_URL"]."/search_order_direction_down.png\" border=\"0\">";
} else {
	$ReverseOrderDirection = "DESC";
	$OrderSign = "<img src=\"".$Campsite["ADMIN_IMAGE_BASE_URL"]."/search_order_direction_up.png\" border=\"0\">";
}

$IdHref  = 
	camp_html_article_url($articleObj, $Language, 'images/search.php')
	.'&order_by=id'
	.$imageNav->getKeywordSearchLink();
$DescriptionHref  = 
	camp_html_article_url($articleObj, $Language, 'images/search.php')
	.'&order_by=description'
	.$imageNav->getKeywordSearchLink();
$PhotographerHref  = 
	camp_html_article_url($articleObj, $Language, 'images/search.php')
	.'&order_by=photographer'
	.$imageNav->getKeywordSearchLink();
$PlaceHref  = 
	camp_html_article_url($articleObj, $Language, 'images/search.php')
	.'&order_by=place'
	.$imageNav->getKeywordSearchLink();
$DateHref  = 
	camp_html_article_url($articleObj, $Language, 'images/search.php')
	.'&order_by=date'
	.$imageNav->getKeywordSearchLink();
$InUseHref = 
	camp_html_article_url($articleObj, $Language, 'images/search.php')
	.'&order_by=inuse'
	.$imageNav->getKeywordSearchLink();
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
	$DescriptionHref .= '&order_direction='.$ReverseOrderDirection;
	break;
case 'photographer':
	$PhotographerOrderIcon = $OrderSign;
	$PhotographerHref .= '&order_direction='.$ReverseOrderDirection;
	break;
case 'place':
	$PlaceOrderIcon = $OrderSign;
	$PlaceHref .= '&order_direction='.$ReverseOrderDirection;
	break;
case 'date':
	$DateOrderIcon = $OrderSign;
	$DateHref .= '&order_direction='.$ReverseOrderDirection;
	break;
case 'inuse':
	$InUseOrderIcon = $OrderSign;
	$InUseHref .= '&order_direction='.$ReverseOrderDirection;
	break;
case 'id':
	$IdOrderIcon = $OrderSign;
	$IdHref .= '&order_direction='.$ReverseOrderDirection;
	break;
}
///////////////////////////////////////////////////////////////////////

$TotalImages = Image::GetTotalImages();
$imageSearch =& new ImageSearch(CAMPSITE_IMAGEARCHIVE_IMAGES_PER_PAGE);
$imageSearch->run();
$imageData = $imageSearch->getImages();
$NumImagesFound = $imageSearch->getNumImagesFound();
$uploadedByUsers = Image::GetUploadUsers();

// Add extra breadcrumb for image list.
$extraCrumbs = array(getGS("Images")=>"/$ADMIN/articles/images/?Pub=$Pub&Issue=$Issue&Language=$Language&Section=$Section&Article=$Article&sLanguage=$sLanguage");
$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
				  'Section' => $sectionObj, 'Article'=>$articleObj);
camp_html_content_top(getGS('Link Image to Article'), $topArray, true, true, $extraCrumbs);
?>

<table>
<tr>
    <td><?php echo camp_html_article_link($articleObj, $Language, 'images/index.php') ?><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/back.png" BORDER="0" ALT="<?php putGS("Back to Article Image List"); ?>"></a></td>
    <td><?php echo camp_html_article_link($articleObj, $Language, 'images/index.php') ?><b><?php echo putGS('Back to Article Image List'); ?></b></a></td>
    <td><?php echo camp_html_article_link($articleObj, $Language, 'edit.php') ?><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/back.png" BORDER="0" ALT="<?php putGS("Back to article details"); ?>"></a></td>
    <td><?php echo camp_html_article_link($articleObj, $Language, 'edit.php') ?><b><?php echo putGS('Back to article details'); ?></b></a></td>
</tr>
<tr>
    <td><?php echo camp_html_article_link($articleObj, $Language, 'images/search.php') ?><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/reset.png" BORDER="0" ALT="<?php putGS("Reset search conditions"); ?>"></a></td>
    <td colspan="3"><?php echo camp_html_article_link($articleObj, $Language, 'images/search.php') ?><b><?php echo putGS('Reset search conditions'); ?></b></a></td>
</tr>
</table>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" class="table_input" style="margin-bottom: 10px; margin-top: 5px;" align="center">
<form method="POST" action="search.php">
<input type="hidden" name="order_by" value="<?php echo $OrderBy; ?>">
<input type="hidden" name="order_direction" value="<?php echo $OrderDirection; ?>">
<input type="hidden" name="view" value="<?php echo $view; ?>">
<input type="hidden" name="image_offset" value="0">
<input type="hidden" name="Pub" value="<?php p($Pub); ?>">
<input type="hidden" name="Issue" value="<?php p($Issue); ?>">
<input type="hidden" name="Section" value="<?php p($Section); ?>">
<input type="hidden" name="Language" value="<?php p($Language); ?>">
<input type="hidden" name="sLanguage" value="<?php p($sLanguage); ?>">
<input type="hidden" name="Article" value="<?php p($Article); ?>">
<tr>
	<td style="padding-left: 10px;"><?php putGS('Description')?>:</td>
	<td><input type="text" name="search_description" value="<?php echo $SearchDescription; ?>" class="input_text" style="width: 150px;"></td>
	<td><?php putGS('Photographer'); ?>:</td>
	<td><input type="text" name="search_photographer" value="<?php echo $SearchPhotographer; ?>" class="input_text" style="width: 100px;"></td>
	<td><?php putGS('Place'); ?>:</td>
	<td><input type="text" name="search_place" value="<?php echo $SearchPlace; ?>" class="input_text" style="width: 100px;"></td>
	<td ><?php putGS('Date'); ?>:</td>
	<td><input type="text" name="search_date" value="<?php echo $SearchDate; ?>" class="input_text" style="width: 80px;"></td>
	<td nowrap>Uploaded by:</td>
	<td>
		<select name="search_uploadedby" class="input_select" style="width: 100px;">
		<option value="0"></option>
		<?php 
		foreach ($uploadedByUsers as $tmpUser) {
			?>
			<option value="<?php echo $tmpUser->getId(); ?>" <?php if ($tmpUser->getId() == $SearchUploadedBy)  { echo "selected"; } ?>><?php echo htmlspecialchars($tmpUser->getName()); ?></option>
			<?php
		}
		?>
		</select>
	</td>
	<td><input type="submit" name="submit_button" value="Search" class="button"></td>
</tr>
<tr>
	<td colspan="11" align="center" >
		Additional searches: &nbsp;
		<a href="<?php echo camp_html_article_url($articleObj, $Language, 'images/search.php').'&'.$imageNav->getSearchLink(); ?>&order_by=time_created" style="font-size: 9pt; font-weight: bold; text-decoration: underline;"><?php putGS('Most Recently Added'); ?></a><?php if ($OrderBy == "time_created") { echo "*"; } ?>
		&nbsp;
		<a href="<?php echo camp_html_article_url($articleObj, $Language, 'images/search.php').'&'.$imageNav->getSearchLink(); ?>&order_by=last_modified" style="font-size: 9pt; font-weight: bold; text-decoration: underline;"><?php putGS('Most Recently Modified'); ?></a><?php if ($OrderBy == "last_modified") { echo "*"; } ?>
	</td>
	
</tr>
</form>
</table>

<?php
if (count($imageData) > 0) {
   	include('view_thumbnail.inc.php');
}

camp_html_copyright_notice(); ?>