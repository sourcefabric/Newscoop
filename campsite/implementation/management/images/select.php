<?php  include ("../../../../../lib_campsite.php");
    $globalfile=selectLanguageFile('../../../../..','globals');
    $localfile=selectLanguageFile('.','locals');
    @include ($globalfile);
    @include ($localfile);
    include ("../../../../../languages.php");   ?>
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

	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Images"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/logout.php">
<?php  }
    query ("SELECT * FROM Images WHERE 1=0", 'q_img');
?></HEAD>

<?php  if ($access) { 


   if (getVar($XPerm,'AddImage') == "Y")
	$aia=1;
    else 
	$aia=0;
    

   if (getVar($XPerm,'ChangeImage') == "Y")
	$cia=1;
    else 
	$cia=0;
    

   if (getVar($XPerm,'DeleteImage') == "Y")
	$dia=1;
    else 
	$dia=0;
    
?><STYLE>
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
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Article');
    todefnum('Language');
    todefnum('sLanguage');
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
		    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Images"); ?></B></DIV>
		    <HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR><TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Articles"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>" ><B><?php  putGS("Articles");  ?></B></A></TD>
<TD><A HREF="/priv/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Sections"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><B><?php  putGS("Sections");  ?></B></A></TD>
<TD><A HREF="/priv/pub/issues/?Pub=<?php  p($Pub); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Issues"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/?Pub=<?php  p($Pub); ?>" ><B><?php  putGS("Issues");  ?></B></A></TD>
<TD><A HREF="/priv/pub/" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD><TD><A HREF="/priv/pub/" ><B><?php  putGS("Publications");  ?></B></A></TD>
<TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD><TD><A HREF="/priv/home.php" ><B><?php  putGS("Home");  ?></B></A></TD>
<TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD><TD><A HREF="/priv/logout.php" ><B><?php  putGS("Logout");  ?></B></A></TD>
</TR></TABLE></TD></TR>
</TABLE>
<?php 
    query ("SELECT * FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article", 'q_art');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	    if ($NUM_ROWS) {
		query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
		if ($NUM_ROWS) {
		    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');

		    fetchRow($q_art);
		    fetchRow($q_sect);
		    fetchRow($q_iss);
		    fetchRow($q_pub);
		    fetchRow($q_lang);
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pgetHVar($q_pub,'Name'); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Issue"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Section"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Article"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pgetHVar($q_art,'Name'); ?></B></TD>

</TR></TABLE>

<?php 
	todef('sDescription');		$sDescription =  decURL($sDescription);
	todef('sPhotographer');	$sPhotographer  = decURL($sPhotographer);
	todef('sPlace');			$sPlace = decURL($sPlace);
	todef('cIssue');
	todefnum('ImgOffs');
	if ($ImgOffs < 0) $ImgOffs= 0;
	todefnum('lpp', 20);
?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="./?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="./?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>" ><B><?php  putGS("Back to current article"); ?></B></A></TD></TR></TABLE>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<TD></TD>
	<TD ALIGN="RIGHT">
	<FORM METHOD="GET" ACTION="select.php" NAME="">
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" BGCOLOR="#C0D0FF">
	<TR>
		<TD><?php  putGS('Issue'); ?></TD>
		<TD><INPUT TYPE="TEXT" NAME="cIssue" VALUE="<?php  p($cIssue); ?>" SIZE="4" MAXLENGTH="8"></TD>
		<TD><?php  putGS('Description'); ?></TD>
		<TD><INPUT TYPE="TEXT" NAME="sDescription" VALUE="<?php  pencHTML($sDescription); ?>" SIZE="16" MAXLENGTH="32"></TD>
		<TD><?php  putGS('Photographer'); ?></TD>
		<TD><INPUT TYPE="TEXT" NAME="sPhotographer" VALUE="<?php  pencHTML($sPhotographer); ?>" SIZE="8" MAXLENGTH="32"></TD>
		<TD><?php  putGS('Place'); ?></TD>
		<TD><INPUT TYPE="TEXT" NAME="sPlace" VALUE="<?php  pencHTML($sPlace); ?>" SIZE="16" MAXLENGTH="32"></TD>
		<TD><INPUT TYPE="submit" NAME="Search" VALUE="<?php  putGS('Search'); ?>"></TD>
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article);?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section);?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($sLanguage); ?>">
	</TR>
	</TABLE>
</FORM>
	</TD>
</TABLE>

<?php 
	$cIssue=trim($cIssue);
	$sDescription = trim($sDescription);
	$sPhotographer = trim($sPhotographer);
	$sPlace = trim($sPlace);
	
	if(($cIssue == 0) || ($cIssue == '') || !is_numeric($cIssue)) {
		// check if numeric !!!!
		query ("SELECT * FROM Images WHERE IdPublication=$Pub AND NrArticle != $Article AND Description LIKE '%$sDescription%' AND Photographer LIKE '%$sPhotographer%' AND Place LIKE '%$sPlace%'  ORDER BY IdPublication, NrIssue, NrSection, NrArticle, Number LIMIT $ImgOffs, ".($lpp+1), 'q_img');
	}
	else query ("SELECT * FROM Images WHERE IdPublication=$Pub AND NrIssue=$cIssue AND NrArticle != $Article AND Description LIKE '%$sDescription%' AND Photographer LIKE '%$sPhotographer%' AND Place LIKE '%$sPlace%'  ORDER BY IdPublication, NrIssue, NrSection, NrArticle, Number LIMIT $ImgOffs, ".($lpp+1), 'q_img');
	if ($NUM_ROWS) {
		$nr= $NUM_ROWS;
		$i=$lpp;
		$color= 0;
	?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
	<TR BGCOLOR="#C0D0FF">
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Image number"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Click to view image"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Photographer"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Place"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Date<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Add to current article"); ?></B></TD>
	</TR>
<?php 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_img);
	if ($i) { ?>	<TR <?php  if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
		<TD ALIGN="LEFT">
			<?php  pgetHVar($q_img,'IdPublication'); ?>_<?php  pgetHVar($q_img,'NrIssue'); ?>_<?php  pgetHVar($q_img,'NrSection'); ?>_<?php  pgetHVar($q_img,'NrArticle'); ?>_<?php  pgetHVar($q_img,'Number'); ?>
		</TD>
		<TD >
			<A HREF="/priv/pub/issues/sections/articles/images/viewsel.php?Pub=<?php  pgetUVar($q_img,'IdPublication'); ?>&Issue=<?php  pgetUVar($q_img,'NrIssue'); ?>&Section=<?php  pgetUVar($q_img,'NrSection'); ?>&Article=<?php  pgetUVar($q_img,'NrArticle'); ?>&Image=<?php  pgetUVar($q_img,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>"><?php  pgetHVar($q_img,'Description'); ?></A>
		</TD>
		<TD >
			<?php  pgetHVar($q_img,'Photographer'); ?>&nbsp;
		</TD>
		<TD >
			<?php  pgetHVar($q_img,'Place'); ?>&nbsp;
		</TD>
		<TD >
			<?php  pgetHVar($q_img,'Date'); ?>
		</TD>
		<TD >
			<A HREF="/priv/pub/issues/sections/articles/images/do_sel.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Image=<?php  pgetUVar($q_img,'Number'); ?>&Pub1=<?php  pgetUVar($q_img,'IdPublication'); ?>&Issue1=<?php  pgetUVar($q_img,'NrIssue'); ?>&Section1=<?php  pgetUVar($q_img,'NrSection'); ?>&Article1=<?php  pgetUVar($q_img,'NrArticle'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>"><?php  putGS('Add now'); ?></A>
		</TD>
	</TR>
<?php  
    $i--;
    }
}
?>	<TR><TD COLSPAN="2" NOWRAP>
		<?php 	print "(Publ. no._Issue no._Section no._Article_Image number)<br>"; ?>
<?php  if ($ImgOffs <= 0) { ?>		&lt;&lt; <?php  putGS('Previous'); ?>
<?php  } else { ?>		<B><A HREF="select.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>&sDescription=<?php  pencURL(encHTML($sDescription));?>&sPhotographer=<?php  pencURL(encHTML($sPhotographer));?>&sPlace=<?php  pencURL(encHTML($sPlace));?>&cIssue=<?php  p($cIssue); ?>&ImgOffs=<?php  p($ImgOffs - $lpp); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php  } ?><?php  if ($nr < $lpp+1) { ?>		 | <?php  putGS('Next'); ?> &gt;&gt;
<?php  } else { ?>		 | <B><A HREF="select.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>&sDescription=<?php  pencURL(encHTML($sDescription));?>&sPhotographer=<?php  pencURL(encHTML($sPhotographer));?>&sPlace=<?php  pencURL(encHTML($sPlace));?>&cIssue=<?php  p($cIssue); ?>&ImgOffs=<?php  p($ImgOffs + $lpp); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>	</TD></TR>
</TABLE>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No images.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No such section.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No such article.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<HR NOSHADE SIZE="1" COLOR="BLACK">
<a STYLE='font-size:8pt;color:#000000' href='http://www.campware.org' target='campware'>CAMPSITE  2.1.5 &copy 1999-2004 MDLF, maintained and distributed under GNU GPL by CAMPWARE</a>
</BODY>
<?php  } ?>

</HTML>

