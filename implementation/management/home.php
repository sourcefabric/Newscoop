<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<?php  include ("./lib_campsite.php");
    $globalfile=selectLanguageFile('.','globals');
    $localfile=selectLanguageFile('.','locals');
    @include ($globalfile);
    @include ($localfile);
    include ("./languages.php");   ?>
<?php  require_once("$DOCUMENT_ROOT/db_connect.php"); ?>


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
    


<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">

<script>
<!--
/*
A slightly modified version of "Break-out-of-frames script"
By JavaScript Kit (http://javascriptkit.com)
*/

if (window != top.fmain && window != top) {
	if (top.fmenu)
		top.fmain.location.href=location.href
	else
		top.location.href=location.href
}
// -->
</script>
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Home"); ?></TITLE>
<?php  if ($access==0) { ?>		<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/logout.php">
<?php  } ?>
</HEAD>
<?php 
    query ("SELECT * FROM Articles WHERE 1=0", 'q_art');
    if ($access) {

   if (getVar($XPerm,'AddArticle') == "Y")
	$aaa=1;
    else 
	$aaa=0;
    

   if (getVar($XPerm,'ManagePub') == "Y")
	$mpa=1;
    else 
	$mpa=0;
    

   if (getVar($XPerm,'ManageUserTypes') == "Y")
	$muta=1;
    else 
	$muta=0;
    

   if (getVar($XPerm,'ManageDictionary') == "Y")
	$mda=1;
    else 
	$mda=0;
    

   if (getVar($XPerm,'ManageClasses') == "Y")
	$mca=1;
    else 
	$mca=0;
    

   if (getVar($XPerm,'ManageCountries') == "Y")
	$mcoa=1;
    else 
	$mcoa=0;
    

   if (getVar($XPerm,'ManageArticleTypes') == "Y")
	$mata=1;
    else 
	$mata=0;
    

   if (getVar($XPerm,'ManageUsers') == "Y")
	$mua=1;
    else 
	$mua=0;
    

   if (getVar($XPerm,'ManageLanguages') == "Y")
	$mla=1;
    else 
	$mla=0;
    

   if (getVar($XPerm,'ManageTempl') == "Y")
	$mta=1;
    else 
	$mta=0;
    

   if (getVar($XPerm,'ViewLogs') == "Y")
	$vla=1;
    else 
	$vla=0;
    

   if (getVar($XPerm,'ChangeArticle') == "Y")
	$caa=1;
    else 
	$caa=0;
    
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
    if ($caa)
	todefnum('What',0);
    else
	todefnum('What',1);

?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
		    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Home"); ?></B></DIV>
		    <HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR><TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD><TD><A HREF="/priv/logout.php" ><B><?php  putGS("Logout");  ?></B></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="4" CELLPADDING="2" WIDTH="100%">
<TR><TD COLSPAN="2" BGCOLOR=#D0D0B0><?php  putGS('Welcome $1!','<B>'.getHVar($Usr,'Name').'</B>'); ?></TD></TR>
<TR>
    <TD VALIGN="TOP">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
<?php  if ($aaa != 0) { ?>	<TR><TD ALIGN="RIGHT"><A HREF="pub/add_article.php"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new article"); ?>"></A></TD><TD NOWRAP><A HREF="pub/add_article.php"><?php  putGS("Add new article"); ?></A></TD></TR>
<?php  } ?><?php  if ($mpa != 0) { ?>	<TR><TD ALIGN="RIGHT"><A HREF="pub/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new publication"); ?>"></A></TD><TD NOWRAP><A HREF="pub/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new publication"); ?></A></TD></TR>
<?php  } ?><?php  if ($mta != 0) { ?>	<TR><TD ALIGN="RIGHT"><A HREF="templates/upload_templ.php?Path=/look/&Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Upload new template"); ?>"></A></TD><TD NOWRAP><A HREF="templates/upload_templ.php?Path=/look/&Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Upload new template"); ?></A></TD></TR>
<?php  } ?><?php  if ($mua != 0) { ?>	<TR><TD ALIGN="RIGHT"><A HREF="users/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new user account"); ?>"></A></TD><TD NOWRAP><A HREF="users/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new user account"); ?></A></TD></TR>
<?php  } ?><?php  if ($muta != 0) { ?>	<TR><TD ALIGN="RIGHT"><A HREF="u_types/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new user type"); ?>"></A></TD><TD NOWRAP><A HREF="u_types/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new user type"); ?></A></TD></TR>
<?php  } ?><?php  if ($mata != 0) { ?>	<TR><TD ALIGN="RIGHT"><A HREF="a_types/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new article type"); ?>"></A></TD><TD NOWRAP><A HREF="a_types/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new article type"); ?></A></TD></TR>
<?php  } ?><?php  if ($mcoa != 0) { ?>	<TR><TD ALIGN="RIGHT"><A HREF="country/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new country"); ?>"></A></TD><TD NOWRAP><A HREF="country/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new country"); ?></A></TD></TR>
<?php  } ?><?php  if ($mla != 0) { ?>	<TR><TD ALIGN="RIGHT"><A HREF="languages/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Add new language"); ?>"></A></TD><TD NOWRAP><A HREF="languages/add.php?Back=<?php  print encURL ($REQUEST_URI); ?>"><?php  putGS("Add new language"); ?></A></TD></TR>
<?php  } ?><?php  if ($vla != 0) { ?>	<TR><TD ALIGN="RIGHT"><A HREF="logs/"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("View logs"); ?>"></A></TD><TD NOWRAP><A HREF="logs/"><?php  putGS("View logs"); ?></A></TD></TR>
<?php  } ?>	<TR><TD ALIGN="RIGHT"><A HREF="users/chpwd.php"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Change your password"); ?>"></A></TD><TD NOWRAP><A HREF="users/chpwd.php"><?php  putGS("Change your password"); ?></A></TD></TR>
</TABLE>
	</TD>
	<TD VALIGN="TOP">

<?php  if ($What) { ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><IMG SRC="/priv/img/tol.gif" BORDER="0"></TD><TD><?php  putGS("Your articles"); ?></TD></TR></TABLE>

<?php 
    todefnum('ArtOffs');
    if ($ArtOffs < 0) $ArtOffs=0;
    $lpp=20;
    query ("SELECT * FROM Articles WHERE Iduser=".getVar($Usr,'Id')." ORDER BY Number DESC, IdLanguage LIMIT $ArtOffs, ".($lpp+1), 'q_art');
    $nr=$NUM_ROWS;
    $i=$lpp;
    if ($nr < $lpp) $i = $nr;
    $color=0;
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
	<TR BGCOLOR="#C0D0FF">
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Name<BR><SMALL>(click to edit article)</SMALL>"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="10%" ><B><?php  putGS("Language"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="10%" ><B><?php  putGS("Status"); ?></B></TD>
	</TR>

<?php 
    for($loop=0;$loop<$i;$loop++) {

	    fetchRow($q_art);
	    query ("SELECT IdLanguage FROM Sections WHERE IdPublication=".getVar($q_art,'IdPublication')." AND NrIssue=".getVar($q_art,'NrIssue')." AND IdLanguage=".getVar($q_art,'IdLanguage'), 'q_sect');
	    if ($NUM_ROWS == 0)
		query ("SELECT IdLanguage FROM Sections WHERE IdPublication=".getVar($q_art,'IdPublication')." AND NrIssue=".getVar($q_art,'NrIssue')." LIMIT 1", 'q_sect');
	    fetchRow($q_sect);
 ?>
	<TR <?php  if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
		<TD >
			<A HREF="/priv/pub/issues/sections/articles/edit.php?Pub=<?php  pgetUVar($q_art,'IdPublication');?>&Issue=<?php  pgetUVar($q_art,'NrIssue');?>&Section=<?php  pgetUVar($q_art,'NrSection');?>&Article=<?php  pgetUVar($q_art,'Number');?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage');?>&Language=<?php  pgetUVar($q_sect,'IdLanguage');?>"><?php  print pgetHVar($q_art,'Name');?></A>
		</TD>
<?php  query ("SELECT Name FROM Languages WHERE Id=".getVar($q_art,'IdLanguage'), 'q_lang'); ?>		<TD >

			<?php  fetchRow ($q_lang); pgetHVar($q_lang,'Name'); ?>
		</TD>
		<TD >
<?php  if (getVar($q_art,'Published') == "Y") { ?>			<A HREF="/priv/pub/issues/sections/articles/status.php?Pub=<?php  pgetUVar($q_art,'IdPublication'); ?>&Issue=<?php  pgetUVar($q_art,'NrIssue'); ?>&Section=<?php  pgetUVar($q_art,'NrSection'); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  pgetUVar($q_sect,'IdLanguage'); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>&Back=<?php  print encURL($REQUEST_URI); ?>"><?php  putGS('Published'); ?></A>
<?php  } elseif (getVar($q_art,'Published') == "N") { ?>			<A HREF="/priv/pub/issues/sections/articles/status.php?Pub=<?php  pgetUVar($q_art,'IdPublication'); ?>&Issue=<?php  pgetUVar($q_art,'NrIssue'); ?>&Section=<?php  pgetUVar($q_art,'NrSection'); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  pgetUVar($q_sect,'IdLanguage'); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>&Back=<?php  print encURL($REQUEST_URI); ?>"><?php  putGS('New'); ?></A>
<?php  } else { ?>			<A HREF="/priv/pub/issues/sections/articles/status.php?Pub=<?php  pgetUVar($q_art,'IdPublication'); ?>&Issue=<?php  pgetUVar($q_art,'NrIssue'); ?>&Section=<?php  pgetUVar($q_art,'NrSection'); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  pgetUVar($q_sect,'IdLanguage'); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>&Back=<?php  print encURL($REQUEST_URI); ?>"><?php  putGS('Submitted'); ?></A>
<?php  } ?>		</TD>
	</TR>
<?php 

}
    ?>
	<TR><TD COLSPAN="2" NOWRAP>
<?php  if ($ArtOffs<=0) { ?>		&lt;&lt; <?php  putGS('Previous'); ?>
<?php  } else { ?>		<B><A HREF="home.php?ArtOffs=<?php print ($ArtOffs - $lpp); ?>&What=1">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php  } ?>
<?php  if ($nr<$lpp+1) { ?>		 | <?php  putGS('Next'); ?> &gt;&gt;
<?php  } else { ?>		 | <B><A HREF="home.php?ArtOffs=<?php  print ($ArtOffs + $lpp); ?>&What=1"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>	</TD></TR>
</TABLE>

<?php  } else { ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><IMG SRC="/priv/img/tol.gif" BORDER="0"></TD><TD><?php  putGS("Submitted articles"); ?></TD></TR></TABLE>
<?php 
    todefnum('NArtOffs');
    if ($NArtOffs<0) $NArtOffs=0;
    $lpp=20;
    query ("SELECT * FROM Articles WHERE Published = 'S' ORDER BY Number DESC, IdLanguage LIMIT $NArtOffs, ".($lpp+1), 'q_art');
    $nr=$NUM_ROWS;
    $i=$lpp;
    if ($nr < $lpp) $i = $nr;
    $color=0;
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
	<TR BGCOLOR="#C0D0FF">
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Name<BR><SMALL>(click to edit article)</SMALL>"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="10%" ><B><?php  putGS("Language"); ?></B></TD>
	</TR>
<?php 
    for($loop=0;$loop<$i; $loop++) {
	fetchRow($q_art);

	    query ("SELECT IdLanguage FROM Sections WHERE IdPublication=".getVar($q_art,'IdPublication')." AND NrIssue=".getVar($q_art,'NrIssue')." AND IdLanguage=".getVar($q_art,'IdLanguage'), 'q_sect');
	if ($NUM_ROWS == 0) {
		query ("SELECT IdLanguage FROM Sections WHERE IdPublication=".getVar($q_art,'IdPublication')." AND NrIssue=".getVar($q_art,'NrIssue')." LIMIT 1", 'q_sect');
	}
	fetchRow($q_sect);
?>	<TR <?php  if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
		<TD >
			<A HREF="/priv/pub/issues/sections/articles/edit.php?Pub=<?php  pgetUVar($q_art,'IdPublication'); ?>&Issue=<?php  pgetUVar($q_art,'NrIssue'); ?>&Section=<?php  pgetUVar($q_art,'NrSection'); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>&Language=<?php  pgetUVar($q_sect,'IdLanguage'); ?>"><?php  pgetHVar($q_art,'Name'); ?></A>
		</TD>
<?php  query ("SELECT Name FROM Languages WHERE Id=".getVar($q_art,'IdLanguage'), 'q_lang');?>		<TD >
			<?php  fetchRow($q_lang); pgetHVar($q_lang,'Name'); ?>
		</TD>
	</TR>
<?php 

} ?>	<TR><TD COLSPAN="2" NOWRAP>
<?php  if ($NArtOffs <= 0) { ?>		&lt;&lt; <?php  putGS('Previous'); ?>
<?php  } else { ?>		<B><A HREF="home.php?NArtOffs=<?php  print ($NArtOffs - $lpp); ?>&What=0">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php  
    }
    if ($nr < $lpp+1) { ?>		 | <?php  putGS('Next'); ?> &gt;&gt;
<?php  } else { ?>		 | <B><A HREF="home.php?NArtOffs=<?php  print ($NArtOffs + $lpp); ?>&What=0"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>	</TD></TR>
</TABLE>

<?php  } ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
<TR>
<?php 
    if ($What) {
	if ($caa) { ?>	<TD>
		<TR><TD ALIGN="RIGHT"><A HREF="home.php?What=0"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Submitted articles"); ?>"></A></TD><TD NOWRAP><A HREF="home.php?What=0"><?php  putGS("Submitted articles"); ?></A></TD></TR>
	</TD>
<?php  } 
    }    
 else { ?>	<TD>
		<TR><TD ALIGN="RIGHT"><A HREF="home.php?What=1"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Your articles"); ?>"></A></TD><TD NOWRAP><A HREF="home.php?What=1"><?php  putGS("Your articles"); ?></A></TD></TR>
	</TD>
<?php  } ?></TR>
</TABLE>

    </TD>
</TR>
</TABLE>

<HR NOSHADE SIZE="1" COLOR="BLACK">
<a STYLE='font-size:8pt;color:#000000' href='http://www.campware.org' target='campware'>CAMPSITE  2.1.5 &copy 1999-2004 MDLF, maintained and distributed under GNU GPL by CAMPWARE</a>
</BODY>
<?php  } ?>

</HTML>

