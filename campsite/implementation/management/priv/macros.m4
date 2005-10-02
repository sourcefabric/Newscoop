changecom`'dnl
changequote(<*, *>)dnl
define(<*X_COPYRIGHT*>, <*<?php camp_html_copyright_notice(); ?>*>)dnl
define(<*INCLUDE_PHP_LIB*>, <*<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
$globalfile=selectLanguageFile('globals');
$localfile=selectLanguageFile("$1");
@include_once($globalfile);
@include_once($localfile);
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
?>*>)dnl
define(<*X_ROOT*>, <*/admin*>)dnl
define(<*X_TMP*>, <*/tmp*>)dnl
define(<*LOOK_PATH*>, <*/look*>)dnl
define(<*X_MYSQL_HOST*>, <*localhost*>)dnl
define(<*X_MYSQL_USER*>, <*root*>)dnl
define(<*X_MYSQL_PASSWORD*>, <**>)dnl
define(<*X_MYSQL_DB*>, <*campsite*>)dnl
define(<*B_DATABASE*>, <*<?php
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");
?>*>)dnl
define(<*E_DATABASE*>, <**>)dnl
dnl
dnl *** General ***
dnl
define(<*B_HTML*>, <*<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>*>)dnl
define(<*E_HTML*>, <*</HTML>*>)dnl
define(<*TMP_HEAD*>, <*<HEAD>
    <META http-equiv="Content-Type" content="text/html" charset="<?php  p($languages[$TOL_Language]['charset']); ?>">
*>)dnl
define(<*B_HEAD*>, <*<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
*>)dnl
define(<*E_HEAD*>, <*</HEAD>*>)dnl
define(<*X_TITLE*>, <*<TITLE><?php  putGS("$1"); ?></TITLE>*>)dnl
define(<*X_COOKIE*>, <*<META HTTP-EQUIV="Set-Cookie" CONTENT="$1; path=/">*>)dnl
define(<*X_EXPIRES*>, <*<META HTTP-EQUIV="Expires" CONTENT="now">*>)dnl
define(<*X_REFRESH*>, <*<META HTTP-EQUIV="Refresh" CONTENT="$1">*>)dnl
define(<*X_LOGOUT*>, <*X_REFRESH(0; URL=X_ROOT/logout.php)*>)dnl
define(<*X_AD*>, <*X_REFRESH(0; URL=X_ROOT/ad.php?ADReason=<?php  print encURL(getGS("$1" ifelse(<*$2*>, <**>, <**>, <*,$2*>))); ?>)*>)dnl
define(<*B_STYLE*>, <* *>)dnl
define(<*E_STYLE*>, <* *>)dnl
define(<*B_BODY*>, <*<BODY $1>*>)dnl
define(<*B_BODY_SPECIAL*>, <*<BODY ONUNLOAD="check()">*>)dnl
define(<*E_BODY*>, <*</BODY>*>)dnl
define(<*B_MBODY*>, <*<BODY BGCOLOR="#D0D0B0" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">*>)dnl
define(<*E_MBODY*>, <*</BODY>*>)dnl
define(<*X_HR*>, <*<HR NOSHADE SIZE="1" COLOR="BLACK">*>)dnl
define(<*X_NEW_BUTTON*>, <*<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="$2" ifelse(<*$3*>, <**>, <**>, <*ONCLICK="$3"*>)><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD><TD><A HREF="$2" ifelse(<*$3*>, <**>, <**>, <*ONCLICK="$3"*>)><B><?php  putGS("$1"); ?></B></A></TD></TR></TABLE>*>)dnl
define(<*X_BACK_BUTTON*>, <*<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="$2" ifelse(<*$3*>, <**>, <**>, <*ONCLICK="$3"*>)><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/back.png" BORDER="0"></A></TD><TD><A HREF="$2" ifelse(<*$3*>, <**>, <**>, <*ONCLICK="$3"*>)><B><?php  putGS("$1"); ?></B></A></TD></TR></TABLE>*>)dnl
define(<*X_TOL_BUTTON*>, <*<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="$2" ifelse(<*$3*>, <**>, <**>, <*ONCLICK="$3"*>)><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/tol.gif" BORDER="0"></A></TD><TD><A HREF="$2" ifelse(<*$3*>, <**>, <**>, <*ONCLICK="$3"*>)><B><?php  putGS("$1"); ?></B></A></TD></TR></TABLE>*>)dnl
define(<*X_BULLET*>, <*<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/tol.gif" BORDER="0"></TD><TD><?php  putGS("$1"); ?></TD></TR></TABLE>*>)dnl
define(<*X_NEXT_I*>, <* | <?php  putGS('Next'); ?> &gt;&gt;*>)dnl
define(<*X_NEXT_A*>, <* | <B><A HREF="$1"><?php  putGS('Next'); ?> &gt;&gt</A></B>*>)dnl
define(<*X_PREV_I*>, <*&lt;&lt; <?php  putGS('Previous'); ?>*>)dnl
define(<*X_PREV_A*>, <*<B><A HREF="$1">&lt;&lt; <?php  putGS('Previous'); ?></A></B>*>)dnl
define(<*X_BUTTON*>, <*<A HREF="X_ROOT/$3"><IMG SRC="X_ROOT/img/$2" BORDER="0" ALT="$1" TITLE="$1" $4></A>*>)dnl
dnl
dnl *** Header ****
dnl
define(<*B_HEADER*>, <*<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("$1"); ?>
		</TD>
*>)dnl
define(<*X_HEADER_NO_BUTTONS*>, <*	<TR><TD>&nbsp;</TD></TR>*>)dnl
define(<*B_HEADER_BUTTONS*>, <*	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR>dnl*>)dnl
define(<*X_HBUTTON*>, <*<TD><A HREF="X_ROOT/$2" class="breadcrumb" ifelse(<*$3*>, <**>, <**>, <*TARGET="$3"*>)>ifelse(<*$4*>, <**>, <*<?php  putGS("$1");  ?>*>, <*<?php echo getGS("$1") . " $4"; ?>*>)</A></TD>*>)dnl
define(<*E_HEADER_BUTTONS*>, <*</TR></TABLE></TD></TR>*>)dnl
define(<*E_HEADER*>, <*</TABLE>*>)dnl
dnl
dnl *** Menu ***
dnl
define(<*B_MENU*>, <*<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">*>)dnl
dnl define(<*X_MENU_ITEM*>, <*<TR><TD ALIGN="RIGHT"><A HREF="$2" ONCLICK="$3" TARGET="fmain"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/tol.gif" BORDER="0" ALT="<?php  putGS("$1"); ?>"></A></TD><TD NOWRAP><A HREF="$2" ONCLICK="$3" TARGET="fmain"><?php  putGS("$1"); ?></A></TD></TR>*>)dnl
dnl define(<*X_HITEM*>, <*<TR><TD ALIGN="RIGHT"><A HREF="$1"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/tol.gif" BORDER="0" ALT="<?php  putGS("$2"); ?>"></A></TD><TD NOWRAP><A HREF="$1"><?php  putGS("$2"); ?></A></TD></TR>*>)dnl
define(<*X_MENU_BAR*>, <*<TR><TD COLSPAN="2">X_HR</TD></TR>*>)dnl
define(<*E_MENU*>, <*</TABLE>*>)dnl
dnl
dnl *** Home Page Menu ***
dnl
define(<*B_HOME_MENU*>, <*<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" ALIGN="CENTER" class="table_input">define(<*X_HOME_MENU_P*>, <*$1*>)*>)dnl
define(<*B_HOME_MENU_HEADER*>, <*<TR>*>)dnl
define(<*X_HOME_MENU_TH*>, <*<TD WIDTH="1%" VALIGN="TOP"><A HREF="$2"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/tol.gif" BORDER="0"></A></TD><TD WIDTH="X_HOME_MENU_P"><B><A HREF="$2">$1</A></B></TD>*>)dnl
define(<*X_HOME_MENU_TH_EMPTY*>, <*<TD COLSPAN="2">&nbsp;</TD>*>)dnl
define(<*E_HOME_MENU_HEADER*>, <*</TR>*>)dnl
define(<*B_HOME_MENU_BODY*>, <*<TR>*>)dnl
define(<*B_HOME_MENU_TD*>, <*<TD></TD><TD VALIGN="TOP">*>)dnl
define(<*X_HOME_MENU_ITEM*>, <*$1*>)dnl
define(<*E_HOME_MENU_TD*>, <*</TD>*>)dnl
define(<*X_HOME_MENU_TD_EMPTY*>, <*<TD COLSPAN="2">&nbsp;</TD>*>)dnl
define(<*E_HOME_MENU_BODY*>, <*</TR>*>)dnl
define(<*E_HOME_MENU*>, <*</TABLE>*>)dnl
dnl
dnl *** Current object **
dnl
define(<*B_CURRENT*>, <*<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>*>)dnl
define(<*X_CURRENT*>, <*<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("$1"); ?>:</TD><TD VALIGN="TOP" class="current_location_content">$2</TD>
*>)dnl
define(<*E_CURRENT*>, <*</TR></TABLE>*>)dnl
dnl
dnl *** List ***
dnl
define(<*B_LIST*>, <*<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">*>)dnl
define(<*B_LIST_HEADER*>, <*<TR class="table_list_header">*>)dnl
define(<*X_LIST_TH*>, <*<TD ALIGN="LEFT" VALIGN="TOP" ifelse(<*$2*>, <**>, <**>, <*WIDTH="$2"*>) ifelse(<*$3*>, <**>, <**>, <*$3*>)><B><?php  putGS("$1"); ?></B></TD>*>)dnl
define(<*E_LIST_HEADER*>, <*</TR>*>)dnl
define(<*B_LIST_TR*>, <*<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>*>)dnl
define(<*B_LIST_ITEM*>, <*<TD ifelse(<*$1*>, <**>, <**>, <*ALIGN="$1"*>)<**>ifelse(<*$2*>, <**>, <**>, <*COLSPAN="$2"*>)>*>)dnl
define(<*E_LIST_ITEM*>, <*</TD>*>)dnl
define(<*E_LIST_TR*>, <*</TR>*>)dnl
define(<*B_LIST_FOOTER*>, <*<TR><TD COLSPAN="2" NOWRAP>*>)dnl
define(<*E_LIST_FOOTER*>, <*</TD></TR>*>)dnl
define(<*E_LIST*>, <*</TABLE>*>)dnl
dnl
dnl *** Dialog ***
dnl
define(<*B_DIALOG*>, <*<FORM NAME="dialog" METHOD="$2" ACTION="$3" ifelse(<*$4*>, <**>, <**>, <*ENCTYPE="$4"*>) ifelse(<*$5*>, <**>, <**>, <*$5*>)>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("$1"); ?></B>
			X_HR
		</TD>
	</TR>*>)dnl
define(<*X_DIALOG_TEXT*>, <*<TR>
		<TD COLSPAN="2">$1</TD>
	</TR>*>)dnl
define(<*B_DIALOG_INPUT*>, <*<TR>
		<TD ALIGN="RIGHT" ifelse(<*$2*>, <**>, <**>, <*VALIGN="$2"*>)><?php  putGS("$1"); ?>:</TD>
		<TD>*>)dnl
define(<*B_X_DIALOG_INPUT*>, <*<TR>
		<TD ALIGN="RIGHT" ifelse(<*$2*>, <**>, <**>, <*VALIGN="$2"*>)>$1</TD>
		<TD>*>)dnl
define(<*B_DIALOG_PACKEDINPUT*>, <*<TR>
		<TD>&nbsp;</TD><TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
*>)dnl
define(<*E_DIALOG_PACKEDINPUT*>, <*	</TABLE>
	</TD>
	</TR>*>)dnl
define(<*E_DIALOG_INPUT*>, <*	</TD>
	</TR>*>)dnl
define(<*B_DIALOG_BUTTONS*>, <*<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">*>)dnl
define(<*E_DIALOG_BUTTONS*>, <*	</DIV>
		</TD>
	</TR>*>)dnl
define(<*E_DIALOG*>, <*</TABLE></CENTER>
</FORM>*>)dnl
define(<*SUBMIT*>, <*<INPUT TYPE="submit" class="button" NAME="$1" VALUE="<?php  putGS('$2'); ?>">*>)dnl
define(<*REDIRECT*>, <*<INPUT TYPE="button" class="button" NAME="$1" VALUE="<?php  putGS('$2'); ?>" ONCLICK="location.href='$3'">*>)dnl
dnl
dnl *** Message box ***
dnl
define(<*B_MSGBOX*>, <*<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" ifelse(<*$2*>, <**>, <*class="message_box"*>, <*BGCOLOR="$2"*>) ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> ifelse(<*$3*>, <**>, <**>, <*<font color="$3">*>)<?php  putGS("$1"); ?> ifelse(<*$3*>, <**>, <**>, <*</font>*>)</B>
			X_HR
		</TD>
	</TR>*>)dnl
define(<*X_MSGBOX_TEXT*>, <*<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>$1</BLOCKQUOTE></TD>
	</TR>*>)dnl
define(<*B_MSGBOX_BUTTONS*>, <*<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">*>)dnl
define(<*E_MSGBOX_BUTTONS*>, <*	</DIV>
		</TD>
	</TR>*>)dnl
define(<*E_MSGBOX*>, <*</TABLE></CENTER>*>)dnl
dnl
dnl *** SearchDialog ***
dnl
define(<*B_SEARCH_DIALOG*>, <*<FORM METHOD="$1" ACTION="$2" NAME="$3">
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="search_dialog">
	<TR>*>)dnl
define(<*E_SEARCH_DIALOG*>, <*</TR>
	</TABLE>
</FORM>*>)dnl
dnl
dnl *** Access ***
dnl
define(<*CHECK_BASIC_ACCESS*>,
    <*
<?php 
    todefnum('TOL_UserId');
    todefnum('TOL_UserKey');
    query ("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'Usr');
    $access=($NUM_ROWS != 0);
    if ($NUM_ROWS) {
	fetchRow($Usr);
	query ("SELECT * FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'XPerm');
	 if ($NUM_ROWS){
	 	fetchRow($XPerm);
	 }
	 else $access = 0;						//added lately; a non-admin can enter the administration area; he exists but doesn't have ANY rights
	 $xpermrows= $NUM_ROWS;
    }
    else {
	query ("SELECT * FROM UserPerm WHERE 1=0", 'XPerm');
    }
?>
    *>
)dnl
define(<*CHECK_ACCESS*>,
    <*
    <?php  if ($access) {
	query ("SELECT $1 FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'Perm');
	 if ($NUM_ROWS) {
		fetchRow($Perm);
		$access = (getVar($Perm,'$1') == "Y");
	}
	else $access = 0;
    } ?>
    *>
 )dnl
define(<*CHECK_XACCESS*>,
	<*
	<?php  if($xpermrows) {
		$xaccess=(getvar($XPerm,'$1') == "Y");
		if($xaccess =='') $xaccess = 0;
	}
	else $xaccess = 0;
	?>
	*>
)dnl
define(<*SET_ACCESS*>, <*
   if (getVar($XPerm,'$2') == "Y")
	$$1=1;
    else 
	$$1=0;
    *>)dnl
define(<*X_XAD*>, <*
<P>
B_MSGBOX(<*Access denied*>, <**>, <*red*>)
	X_MSGBOX_TEXT(<*<font color=red><li><?php  putGS("$1" ifelse(<*$3*>, <**>, <**>, <*, $3*>)); ?></li></font>*>)
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/$2"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
E_DIALOG
<P>
*>)dnl
dnl
dnl *** Logging ***
dnl
define(<*X_AUDIT*>, <*<?php  $logtext = $2; query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=$1, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>*>)dnl
dnl
dnl
dnl
define(<*PREVIEW_OPT*>, <*'resizable=yes, menubar=yes, toolbar=yes, width=680, height=560'*>)dnl
