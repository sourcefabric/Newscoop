<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
$globalfile=selectLanguageFile($Campsite['HTML_DIR'] . "/$ADMIN_DIR",'globals');
$localfile=selectLanguageFile("$ADMIN_DIR/templates","locals");
@include_once($globalfile);
@include_once($localfile);
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");

todefnum('Issue');
todefnum('Pub');
todefnum('What');
todefnum('Language');
todef('REQUEST_URI', $_SERVER[REQUEST_URI]);
todefnum('TOL_UserId');
todefnum('TOL_UserKey');

query ("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'Usr');
$access=($NUM_ROWS != 0);
if ($NUM_ROWS) {
	fetchRow($Usr);
	query ("SELECT * FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'XPerm');
	if ($NUM_ROWS){
		fetchRow($XPerm);
	} else
		$access = 0; //added lately; a non-admin can enter the administration area;
		             // he exists but doesn't have ANY rights
	$xpermrows= $NUM_ROWS;
} else {
	query ("SELECT * FROM UserPerm WHERE 1=0", 'XPerm');
}

if ($What != 0) {
	if ($access) {
		query ("SELECT ManageTempl FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'Perm');
		if ($NUM_ROWS) {
			fetchRow($Perm);
			$access = (getVar($Perm,'ManageTempl') == "Y");
		} else
			$access = 0;
    }
}
?>
<HEAD>
	<META HTTP-EQUIV="Expires" CONTENT="now">
<?php
if ($What) {
?>
	<TITLE><?php  putGS("Select template"); ?></TITLE>
<?php
} else {
?>
	<TITLE><?php  putGS("Templates management"); ?></TITLE>
<?php
}
if ($access == 0) {
	if ($What) {
?>
		<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to change default templates." )); ?>">
<?php
	} else {
?>
		<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/logout.php">
<?php
	}
}
?>
</HEAD>

<?php

$dotpos=strrpos($REQUEST_URI,"?");
$dotpos = $dotpos ? $dotpos: strlen($REQUEST_URI);
$myurl=substr ($REQUEST_URI,0,$dotpos);
$myurl1=substr ($REQUEST_URI,$dotpos+1);

if (strncmp($myurl, "/look/", 6) != 0) {
    $access = FALSE;
?>

<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <font color="red"><?php  putGS("Access denied"); ?> </font></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><font color=red><li><?php  putGS("You do no have access to the $1 directory!" , $myurl); ?></li></font></BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<A HREF="/admin/"><IMG SRC="/admin/img/button/ok.gif" BORDER="0" ALT="OK"></A>
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
<?php
}
if ($access) {
	if (getVar($XPerm,'ManageTempl') == "Y")
		$mta=1;
	else 
		$mta=0;

	if (getVar($XPerm,'DeleteTempl') == "Y")
		$dta=1;
	else 
		$dta=0;
?>
<STYLE>
	BODY { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
	SMALL { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 8pt; }
	FORM { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
	TH { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
	TD { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
	BLOCKQUOTE { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
	UL { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
	LI { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; }
	A  { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 10pt; text-decoration: none; color: darkblue; }
	ADDRESS { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 8pt; }
</STYLE>
<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">

<?php
	if ($What) {
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/admin/img/sign_big.gif" BORDER="0"></TD>
		<TD>
		    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Select template"); ?></B></DIV>
		    <HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<?php } else { ?>
 <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/admin/img/sign_big.gif" BORDER="0"></TD>
		<TD>
		    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Templates"); ?></B></DIV>
		    <HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
    <?php  } ?>
	<TR><TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><?php  if ($What) { ?><TD><A HREF="/admin/pub/issues/?Pub=<?php  pencURL($Pub); ?>" ><IMG SRC="/admin/img/tol.gif" BORDER="0" ALT="<?php  putGS("Issues"); ?>"></A></TD><TD><A HREF="/admin/pub/issues/?Pub=<?php  pencURL($Pub); ?>" ><B><?php  putGS("Issues");  ?></B></A></TD>
<TD><A HREF="/admin/pub/" ><IMG SRC="/admin/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD><TD><A HREF="/admin/pub/" ><B><?php  putGS("Publications");  ?></B></A></TD>
<?php  } ?><TD><A HREF="/admin/home.php" ><IMG SRC="/admin/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD><TD><A HREF="/admin/home.php" ><B><?php  putGS("Home");  ?></B></A></TD>
<TD><A HREF="/admin/logout.php" ><IMG SRC="/admin/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD><TD><A HREF="/admin/logout.php" ><B><?php  putGS("Logout");  ?></B></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php
    $NUM_ROWS=0;
    if ($What)
 query ("SELECT Name, FrontPage, SingleArticle FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS != 0 || $What == 0) {
 if ($What)
     query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
 if (($NUM_ROWS != 0) || ($What == 0)) {
   ?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%"><TR>
<?php  if ($What) { ?><TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  fetchRow($q_pub); pgetHVar($q_pub,'Name'); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Issue"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pencURL($Issue); ?>. <?php  fetchRow($q_iss); pgetHVar($q_iss,'Name'); ?> (<?php
    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_language');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
 fetchRowNum($q_language);
 pencHTML( getNumVar($q_language,0));
    }
?>)</B></TD>

<?php  } ?>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Path"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pencHTML(decURL($myurl)); ?></B></TD>

</TR></TABLE>
<P>
<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="0">
<TR>


<?php
    if ($myurl != "/look/") {
 if ($What) { ?><TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="../?What=<?php  pencURL($What); ?>&Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Language=<?php  pencURL($Language); ?>" ><IMG SRC="/admin/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="../?What=<?php  pencURL($What); ?>&Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Language=<?php  pencURL($Language); ?>" ><B><?php  putGS("Go up"); ?></B></A></TD></TR></TABLE></TD>
<?php  } else { ?><TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF=".." ><IMG SRC="/admin/img/tol.gif" BORDER="0"></A></TD><TD><A HREF=".." ><B><?php  putGS("Go up"); ?></B></A></TD></TR></TABLE></TD>
<?php  }
}

 if ($What == 0) {
  if ($mta != 0) { ?>
   <TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/admin/templates/new_dir.php?Path=<?php  pencURL($myurl); ?>" ><IMG SRC="/admin/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/admin/templates/new_dir.php?Path=<?php  pencURL($myurl); ?>" ><B><?php  putGS("Create new folder"); ?></B></A></TD></TR></TABLE></TD>
   <TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/admin/templates/upload_templ.php?Path=<?php  pencURL($myurl); ?>" ><IMG SRC="/admin/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/admin/templates/upload_templ.php?Path=<?php  pencURL($myurl); ?>" ><B><?php  putGS("Upload template"); ?></B></A></TD></TR></TABLE></TD>
   <TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/admin/templates/new_template.php?Path=<?php  pencURL($myurl); ?>" ><IMG SRC="/admin/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/admin/templates/new_template.php?Path=<?php  pencURL($myurl); ?>" ><B><?php  putGS("Create new template"); ?></B></A></TD></TR></TABLE></TD>
   <TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/admin/templates/refresh.php?Path=<?php  pencURL($myurl); ?>" ><IMG SRC="/admin/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/admin/templates/refresh.php?Path=<?php  pencURL($myurl); ?>" ><B><?php  putGS("Refresh templates directory"); ?></B></A></TD></TR></TABLE></TD>
  <?php  }
 } else {?><TD>
<?php  if ($What == 1) { ?> <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
 <TR>
  <TD><IMG SRC="/admin/img/tol.gif" BORDER="0"></TD>
  <TD><?php  putGS('Select the template for displaying the front page.'); ?></TD>
 </TR>
 </TABLE>
<?php  } else { ?> <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
 <TR>
  <TD><IMG SRC="/admin/img/tol.gif" BORDER="0"></TD>
  <TD><?php  putGS('Select the template for displaying a single article.'); ?></TD>
 </TR>
 </TABLE>
<?php  } ?> </TD>
<?php  } ?></TABLE>
<P>
<?php
    // 'What' at this level selects the usage of templates:
    // 0 - you are in the templates management module (create, delete, edit, upload, duplicate etc)
    // 1, 2 - select a template for viewing with it the font page (1) and an independent article (2)

    if ($What) {

 $listbasedir=$myurl;
 $params=$myurl1;
 include ('./stempl_dir.php');
    }
    else {
     //dSystem( "$scriptBase/list '$myurl' $mta $dta $DOCUMENT_ROOT");
 $listbasedir=$myurl;
 include ('./list_dir.php');
    }

} else {
?><BLOCKQUOTE>
 <LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  }
} else { ?><BLOCKQUOTE>
 <LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<HR NOSHADE SIZE="1" COLOR="BLACK">
<a STYLE='font-size:8pt;color:#000000' href='http://www.campware.org' target='campware'>CAMPSITE 2.2 &copy 1999-2004 MDLF, maintained and distributed under GNU GPL by CAMPWARE</a>
</BODY>
<?php  } ?>

</HTML>
