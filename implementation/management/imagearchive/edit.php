<?php  
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files();
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/imagearchive/include.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/CampsiteInterface.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header('Location: /priv/logout.php');
	exit;
}
$ImageId = isset($_REQUEST['image_id'])?$_REQUEST['image_id']:0;
$imageNav =& new ImageNav($_REQUEST, CAMPSITE_IMAGEARCHIVE_IMAGES_PER_PAGE, $_REQUEST['view']);
if (!is_numeric($ImageId) || ($ImageId <= 0)) {
	header('Location: index.php?'.$imageNav->getSearchLink());
	exit;	
}

// This file can only be accessed if the user has the right to change images.
if (!$User->hasPermission('ChangeImage')) {
	header('Location: /priv/logout.php');
	exit;		
}

$imageObj =& new Image($ImageId);
$articles =& ArticleImage::GetArticlesThatUseImage($ImageId);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Change image information"); ?></TITLE>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['website_url'] ?>/css/admin_stylesheet.css">
</HEAD>

<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
			<DIV STYLE="font-size: 12pt"><B><?php  putGS("Change image information"); ?></B></DIV>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR><TD ALIGN=RIGHT>
	  <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
		  <TD><A HREF="index.php?<?php echo $imageNav->getSearchLink() ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Image archive"); ?>"></A></TD><TD><A HREF="index.php?<?php echo $imageNav->getSearchLink(); ?>" ><B><?php  putGS("Image archive");  ?></B></A></TD>
		  <TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD><TD><A HREF="/priv/home.php" ><B><?php  putGS("Home");  ?></B></A></TD>
		  <TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD><TD><A HREF="/priv/logout.php" ><B><?php  putGS("Logout");  ?></B></A></TD>
		</TR>
	  </TABLE>
	</TD></TR>
</TABLE>

<CENTER><IMG SRC="<?php echo $imageObj->getImageUrl(); ?>" BORDER="0" ALT="<?php echo htmlspecialchars($imageObj->getDescription()); ?>"></CENTER>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php?<?php echo $imageNav->getSearchLink(); ?>" ENCTYPE="multipart/form-data">
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("Change image information"); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Description"); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="TEXT" NAME="cDescription" VALUE="<?php echo htmlspecialchars($imageObj->getDescription()); ?>" SIZE="32" MAXLENGTH="128" class="input_text">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Photographer"); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="TEXT" NAME="cPhotographer" VALUE="<?php echo htmlspecialchars($imageObj->getPhotographer());?>" SIZE="32" MAXLENGTH="64" class="input_text">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Place"); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="TEXT" NAME="cPlace" VALUE="<?php echo htmlspecialchars($imageObj->getPlace()); ?>" SIZE="32" MAXLENGTH="64" class="input_text">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Date"); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="TEXT" NAME="cDate" VALUE="<?php echo htmlspecialchars($imageObj->getDate()); ?>" SIZE="11" MAXLENGTH="10" class="input_text"> <?php putGS('YYYY-MM-DD'); ?>
		</TD>
	</TR>
    <?php
    if ($imageObj->getLocation() == 'remote') {
    ?>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("URL"); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="TEXT" NAME="cURL" VALUE="<?php echo htmlspecialchars($imageObj->getUrl()); ?>" SIZE="32" class="input_text">
		</TD>
	</TR>
    <?php
    } else {
    ?>
	<TR>
		<TD ALIGN="RIGHT"><?php  putGS("Image"); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="TEXT" NAME="cImage" SIZE="32" MAXLENGTH="64" VALUE="<?php echo basename($imageObj->getImageStorageLocation()); ?>" DISABLED class="input_text">
		</TD>
	</TR>
    <?php
    }
    ?>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="HIDDEN" NAME="image_id" VALUE="<?php echo $imageObj->getImageId(); ?>">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>" class="button">
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='index.php?<?php echo $imageNav->getSearchLink(); ?>'" class="button">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
<P>
<?php
if (count($articles) > 0) {
	// image is in use //////////////////////////////////////////////////////////////////
	?>
	<center>
	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" bgcolor="white" width="50%">
	  <tr><td bgcolor="#C0D0FF" colspan="2"><b><?php putGS('Previously used in Articles'); ?></b></td></tr>
	<?php
	$color = 0;
	foreach ($articles as $article) {
		echo '<tr ';
		if ($color) { 
			$color=0; 
			echo 'BGCOLOR="#D0D0B0"';
		} else { 
			$color=1; 
			echo 'BGCOLOR="#D0D0D0"';
		} 
		echo '><td>'.htmlspecialchars($article->getTitle()).'</td>
			  <td width="10%" align="center"><a href="/priv/pub/issues/sections/articles/edit.php?Pub='.htmlspecialchars($article->getPublicationId()).'&Issue='.$article->getIssueId().'&Section='.$article->getSectionId().'&Article='.$article->getArticleId().'&Language='.$article->getLanguageId().'&sLanguage='.$article->getLanguageId().'">'.getGS('Edit').'</a></td>
				   </tr>';
	}
	?>
	</table>
	</center>
<?php
}

CampsiteInterface::CopyrightNotice();
?>