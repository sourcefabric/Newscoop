<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files();
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/CampsiteInterface.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/imagearchive/include.inc.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header('Location: /priv/logout.php');
	exit;
}
if (!$User->hasPermission('AddImage')) {
	header('Location: /priv/logout.php');
	exit;	
}
$view = isset($_REQUEST['view'])?$_REQUEST['view']:'thumbnail';
$imageNav =& new ImageNav($_REQUEST, CAMPSITE_IMAGEARCHIVE_IMAGES_PER_PAGE, $view);
query ("SELECT LEFT(NOW(), 10)", 'q_now');
fetchRowNum($q_now);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php putGS("Add new image"); ?></TITLE>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['website_url'] ?>/css/admin_stylesheet.css">	
</HEAD>

<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
		    <DIV STYLE="font-size: 12pt"><B><?php  putGS('Add new image'); ?></B></DIV>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR><TD ALIGN=RIGHT>
	  <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
		  <TD><A HREF="index.php?<?php echo $imageNav->getSearchLink(); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS('Image archive'); ?>"></A></TD><TD><A HREF="index.php?<?php echo $imageNav->getSearchLink(); ?>" ><B><?php  putGS('Image archive');  ?></B></A></TD>
		  <TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS('Home'); ?>"></A></TD><TD><A HREF="/priv/home.php" ><B><?php  putGS('Home');  ?></B></A></TD>
		  <TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS('Logout'); ?>"></A></TD><TD><A HREF="/priv/logout.php" ><B><?php  putGS('Logout');  ?></B></A></TD>
		</TR>
	  </TABLE>
	</TD></TR>
</TABLE>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php?<?php echo $imageNav->getSearchLink(); ?>" ENCTYPE="multipart/form-data">
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php putGS('Add new image'); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php putGS('Description'); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="TEXT" NAME="cDescription" VALUE="Image <?php echo Image::GetMaxId(); ?>" SIZE="32" MAXLENGTH="128" class="input_text">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php putGS('Photographer'); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="TEXT" NAME="cPhotographer" VALUE="<?php echo htmlspecialchars($User->getName()); ?>" SIZE="32" MAXLENGTH="64" class="input_text">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php putGS('Place'); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="TEXT" NAME="cPlace" SIZE="32" MAXLENGTH="64" class="input_text">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php putGS('Date'); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="TEXT" NAME="cDate" VALUE="<?php  pgetNumVar($q_now,0); ?>" SIZE="11" MAXLENGTH="10" class="input_text"> <?php  putGS('YYYY-MM-DD'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php putGS('URL'); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="TEXT" NAME="cURL" SIZE="32" class="input_text">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php putGS('Image'); ?>:</TD>
		<TD align="left">
		<INPUT TYPE="FILE" NAME="cImage" SIZE="32" class="input_file">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
        <input type="hidden" name="view" value="<?php echo $view ?>"> 
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>" class="button">
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='index.php?<?php echo $imageNav->getSearchLink(); ?>'" class="button">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
<P>

<?php CampsiteInterface::CopyrightNotice(); ?>