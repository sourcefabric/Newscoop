<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files();
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Section.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/CampsiteInterface.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/imagearchive/include.inc.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header('Location: /priv/logout.php');
	exit;
}

$OrderBy = Input::get('order_by', 'string', 'id', true);
$OrderDirection = Input::get('order_direction', 'string', 'ASC', true);
$view = Input::get('view', 'string', 'thumbnail', true);
$ImageOffset = Input::get('image_offset', 'int', 0, true);
$SearchDescription = Input::get('search_description', 'string', '', true);
$SearchPhotographer = Input::get('search_photographer', 'string', '', true);
$SearchPlace = Input::get('search_place', 'string', '', true);
$SearchDate = Input::get('search_date', 'string', '', true);
$SearchInUse = Input::get('search_inuse', 'string', '', true);
$SearchUploadedBy = Input::get('search_uploadedby', 'int', '', true);
	
$PublicationId = Input::get('Pub', 'int', 0);
$IssueId = Input::get('Issue', 'int', 0);
$SectionId = Input::get('Section', 'int', 0);
$InterfaceLanguageId = Input::get('Language', 'int', 0);
$ArticleLanguageId = Input::get('sLanguage', 'int', 0);
$ArticleId = Input::get('Article', 'int', 0);

if (!Input::isValid()) {
	header('Location: /priv/logout.php');
	exit;	
}

$imageNav =& new ImageNav($_REQUEST, CAMPSITE_IMAGEARCHIVE_IMAGES_PER_PAGE, $view);
$publicationObj =& new Publication($PublicationId);
$issueObj =& new Issue($PublicationId, $InterfaceLanguageId, $IssueId);
$sectionObj =& new Section($PublicationId, $IssueId, $InterfaceLanguageId, $SectionId);
$languageObj =& new Language($InterfaceLanguageId);
$articleObj =& new Article($PublicationId, $IssueId, $SectionId, $ArticleLanguageId, $ArticleId);

///////////////////////////////////////////////////////////////////////
$ImagesPerPage = 8;

// build the links for ordering (search results) //////////////////////
if ($OrderDirection == 'DESC') {
	$ReverseOrderDirection = "ASC";
	$OrderSign = '<img src="/priv/img/icon/search_order_direction_down.gif" border="0">';
} else {
	$ReverseOrderDirection = "DESC";
	$OrderSign = '<img src="/priv/img/icon/search_order_direction_up.gif" border="0">';
}

$IdHref  = 
	CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/search.php')
	.'&order_by=id'
	.$imageNav->getKeywordSearchLink();
$DescriptionHref  = 
	CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/search.php')
	.'&order_by=description'
	.$imageNav->getKeywordSearchLink();
$PhotographerHref  = 
	CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/search.php')
	.'&order_by=photographer'
	.$imageNav->getKeywordSearchLink();
$PlaceHref  = 
	CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/search.php')
	.'&order_by=place'
	.$imageNav->getKeywordSearchLink();
$DateHref  = 
	CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/search.php')
	.'&order_by=date'
	.$imageNav->getKeywordSearchLink();
$InUseHref = 
	CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/search.php')
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
$imageSearch =& new ImageSearch($_REQUEST, CAMPSITE_IMAGEARCHIVE_IMAGES_PER_PAGE);
$imageSearch->run();
$imageData =& $imageSearch->getImages();
$NumImagesFound = $imageSearch->getNumImagesFound();
$uploadedByUsers =& Image::GetUploadUsers();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS('Images'); ?></TITLE>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['website_url'] ?>/css/admin_stylesheet.css">
	<script type="text/javascript" src="<?php echo $Campsite["website_url"] ?>/javascript/fValidate/fValidate.config.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite["website_url"] ?>/javascript/fValidate/fValidate.core.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite["website_url"] ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite["website_url"] ?>/javascript/fValidate/fValidate.validators.js"></script>	
</HEAD>
<BODY BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
<TR>
	<!--<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>-->
	<TD style="padding-left: 10px; padding-top: 10px;">
		<DIV STYLE="font-size: 12pt"><B><?php putGS('Link Image to Article'); ?></B></DIV>
		<!--<HR NOSHADE SIZE="1" COLOR="BLACK">-->
	</TD>
<!--</TR>
<TR>
-->	<TD ALIGN="RIGHT" style="padding-right: 10px; padding-top: 10px;">
	 	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
			<TD><A HREF="/priv/pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Images"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>" ><B><?php  putGS("Images");  ?></B></A></TD>
			<TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Articles"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>" ><B><?php  putGS("Articles");  ?></B></A></TD>
			<TD><A HREF="/priv/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Sections"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><B><?php  putGS("Sections");  ?></B></A></TD>
			<TD><A HREF="/priv/pub/issues/?Pub=<?php  p($Pub); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Issues"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/issues/?Pub=<?php  p($Pub); ?>" ><B><?php  putGS("Issues");  ?></B></A></TD>
			<TD><A HREF="/priv/pub/" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/" ><B><?php  putGS("Publications");  ?></B></A></TD>
			<!--<TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS('Home'); ?>"></A></TD><TD><A HREF="/priv/home.php" ><B><?php putGS('Home');  ?></B></A></TD>
			<TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS('Logout'); ?>"></A></TD><TD><A HREF="/priv/logout.php" ><B><?php putGS('Logout');  ?></B></A></TD>-->
		</TR>
	</TABLE>
	</TD>
</TR>
</TABLE>
<HR NOSHADE SIZE="1" COLOR="BLACK">

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php echo htmlspecialchars($publicationObj->getName()); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Issue"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php echo $issueObj->getIssueId(); ?>. <?php  echo htmlspecialchars($issueObj->getName()); ?> (<?php echo $languageObj->getName(); ?>)</B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Section"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php echo $sectionObj->getSectionId(); ?>. <?php  echo htmlspecialchars($sectionObj->getName());; ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Article"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php echo htmlspecialchars($articleObj->getTitle()); ?></B></TD>
</TR></TABLE>


<table>
<tr>
    <td><?php echo CampsiteInterface::ArticleLink($articleObj, $InterfaceLanguageId, 'images/index.php') ?><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS("Back to Article Image List"); ?>"></a></td>
    <td><?php echo CampsiteInterface::ArticleLink($articleObj, $InterfaceLanguageId, 'images/index.php') ?><b><?php echo putGS('Back to Article Image List'); ?></b></a></td>
    <td><?php echo CampsiteInterface::ArticleLink($articleObj, $InterfaceLanguageId, 'edit.php') ?><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS("Back to article details"); ?>"></a></td>
    <td><?php echo CampsiteInterface::ArticleLink($articleObj, $InterfaceLanguageId, 'edit.php') ?><b><?php echo putGS('Back to article details'); ?></b></a></td>
</tr>
<tr>
    <td><?php echo CampsiteInterface::ArticleLink($articleObj, $InterfaceLanguageId, 'images/search.php') ?><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS("Reset search conditions"); ?>"></a></td>
    <td colspan="3"><?php echo CampsiteInterface::ArticleLink($articleObj, $InterfaceLanguageId, 'images/search.php') ?><b><?php echo putGS('Reset search conditions'); ?></b></a></td>
</tr>
</table>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" style="border: 1px solid #00008b; margin-bottom: 10px; margin-top: 5px;" align="center">
<form method="POST" action="search.php">
<input type="hidden" name="order_by" value="<?php echo $OrderBy; ?>">
<input type="hidden" name="order_direction" value="<?php echo $OrderDirection; ?>">
<input type="hidden" name="view" value="<?php echo $view; ?>">
<input type="hidden" name="image_offset" value="0">
<input type="hidden" name="Pub" value="<?php p($PublicationId); ?>">
<input type="hidden" name="Issue" value="<?php p($IssueId); ?>">
<input type="hidden" name="Section" value="<?php p($SectionId); ?>">
<input type="hidden" name="Language" value="<?php p($InterfaceLanguageId); ?>">
<input type="hidden" name="sLanguage" value="<?php p($ArticleLanguageId); ?>">
<input type="hidden" name="Article" value="<?php p($ArticleId); ?>">
<tr BGCOLOR="#C0D0FF">
	<td style="padding-left: 10px; color: #00008b;"><?php putGS('Description')?>:</td>
	<td><input type="text" name="search_description" value="<?php echo $SearchDescription; ?>" style="border: 1px solid #00008b; background-color: #f0f0ff; text-indent: 3px; width: 150px;"></td>
	<td style="color: #00008b;"><?php putGS('Photographer'); ?>:</td>
	<td><input type="text" name="search_photographer" value="<?php echo $SearchPhotographer; ?>"  style="border: 1px solid #00008b; background-color: #f0f0ff; text-indent: 3px; width: 100px;"></td>
	<td style="color: #00008b;"><?php putGS('Place'); ?>:</td>
	<td><input type="text" name="search_place" value="<?php echo $SearchPlace; ?>" style="border: 1px solid #00008b; background-color: #f0f0ff; text-indent: 3px; width: 100px;"></td>
	<td style="color: #00008b;"><?php putGS('Date'); ?>:</td>
	<td><input type="text" name="search_date" value="<?php echo $SearchDate; ?>" style="border: 1px solid #00008b; background-color: #f0f0ff; text-indent: 3px; width: 80px;"></td>
	<td style="color: #00008b;" nowrap>Uploaded by:</td>
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
<tr BGCOLOR="#C0D0FF">
	<td colspan="11" align="center" >
		Additional searches: &nbsp;
		<a href="<?php echo CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/search.php').'&'.$imageNav->getSearchLink(); ?>&order_by=time_created" style="font-size: 9pt; font-weight: bold; text-decoration: underline;"><?php putGS('Most Recently Added'); ?></a><?php if ($OrderBy == "time_created") { echo "*"; } ?>
		&nbsp;
		<a href="<?php echo CampsiteInterface::ArticleUrl($articleObj, $InterfaceLanguageId, 'images/search.php').'&'.$imageNav->getSearchLink(); ?>&order_by=last_modified" style="font-size: 9pt; font-weight: bold; text-decoration: underline;"><?php putGS('Most Recently Modified'); ?></a><?php if ($OrderBy == "last_modified") { echo "*"; } ?>
	</td>
	
</tr>
</form>
</table>

<?php
if (count($imageData) > 0) {
   	include('view_thumbnail.inc.php');
}

CampsiteInterface::CopyrightNotice(); ?>