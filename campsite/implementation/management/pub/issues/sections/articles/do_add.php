<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<?php  include ("../../../../lib_campsite.php");
    $globalfile=selectLanguageFile('../../../..','globals');
    $localfile=selectLanguageFile('.','locals');
    @include ($globalfile);
    @include ($localfile);
    include ("../../../../languages.php");   ?>
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
    


    <?php  if ($access) {
	query ("SELECT AddArticle FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'Perm');
	 if ($NUM_ROWS) {
		fetchRow($Perm);
		$access = (getVar($Perm,'AddArticle') == "Y");
	}
	else $access = 0;
    } ?>
    
 

<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Adding new article"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to add articles." )); ?>">
<?php  } ?></HEAD>

<?php  if ($access) { ?><STYLE>
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
    todefnum('Language');
?><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
		    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Adding new article"); ?></B></DIV>
		    <HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR><TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR>
<TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Articles"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>" ><B><?php  putGS("Articles");  ?></B></A></TD>
<TD><A HREF="/priv/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Sections"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><B><?php  putGS("Sections");  ?></B></A></TD>
<TD><A HREF="/priv/pub/issues/?Pub=<?php  p($Pub); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Issues"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/?Pub=<?php  p($Pub); ?>" ><B><?php  putGS("Issues");  ?></B></A></TD>
<TD><A HREF="/priv/pub/" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD><TD><A HREF="/priv/pub/" ><B><?php  putGS("Publications");  ?></B></A></TD>
<TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD><TD><A HREF="/priv/home.php" ><B><?php  putGS("Home");  ?></B></A></TD>
<TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD><TD><A HREF="/priv/logout.php" ><B><?php  putGS("Logout");  ?></B></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php 
    query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	    if ($NUM_ROWS) {
		query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		fetchRow($q_sect);
		fetchRow($q_iss);
		fetchRow($q_pub);
		fetchRow($q_lang);
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pgetHVar($q_pub,'Name'); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Issue"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Section"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B></TD>

</TR></TABLE>

<?php 
    todef('cName');
    todef('cFrontPage');
    todef('cSectionPage');
    todef('cType');
    todefnum('cLanguage');
    todef('cKeywords');

    if ($cFrontPage == "on")
	$cFrontPage= "Y";
    else
	$cFrontPage= "N";

    if ($cSectionPage == "on")
	$cSectionPage= "Y";
    else
	$cSectionPage= "N";

    $correct= 1;
    $created= 0;
?><P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Adding new article"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php 
    $cName=trim($cName);
    $cType=trim($cType);
    $cLanguage=trim($cLanguage);
    
    if ($cName == "" || $cName == " ") {
	$correct= 0; ?>	<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>' ); ?></LI>
    <?php  }
    
    if ($cType == "" || $cType == " ") {
	$correct= 0; ?>	<LI><?php  putGS('You must select an article type.'); ?></LI>
    <?php  }
    
    if ($cLanguage == "" || $cLanguage == "0") {
	$correct= 0; ?>	<LI><?php  putGS('You must select a language.'); ?></LI>
    <?php  }
    
	if ($correct) {
		query ("UPDATE AutoId SET ArticleId=LAST_INSERT_ID(ArticleId + 1)");
		query("select max(ArticleOrder) as ao from Articles where IdPublication = $Pub "
		      . "and NrIssue = $Issue and NrSection = $Section", 'art_max_ord');
		fetchRow($art_max_ord);
		$ao = 1 + getVar($art_max_ord, 'ao');
		$sql = "INSERT IGNORE INTO Articles SET IdPublication=$Pub, NrIssue=$Issue, NrSection = $Section, Number = LAST_INSERT_ID(), IdLanguage=$cLanguage, Type='$cType', Name='$cName', Keywords='$cKeywords', OnFrontPage='$cFrontPage', OnSection='$cSectionPage', UploadDate=NOW(), IdUser=".getVar($Usr,'Id').", Public='Y', ShortName=LAST_INSERT_ID(), ArticleOrder = $ao";
		query($sql);
		if ($AFFECTED_ROWS > 0) {

			## added by sebastian
			if (function_exists ("incModFile"))
				incModFile ();

			query ("INSERT IGNORE INTO X$cType SET NrArticle=LAST_INSERT_ID(), IdLanguage=$cLanguage");
			if ($AFFECTED_ROWS > 0) {
				query ("SELECT LAST_INSERT_ID()", 'lii');
				fetchRowNum($lii);
				$created= 1;
			}
		}
	}

	if ($correct) {
		if ($created) { ?>		<LI><?php  putGS('The article $1 has been created','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
		<?php  $logtext = getGS('Article $1 added to $2. $3 from $4. $5 of $6',$cName,getHVar($q_sect,'Number'),getHVar($q_sect,'Name'),getHVar($q_iss,'Number'),getHVar($q_iss,'Name'),getHVar($q_pub,'Name') ); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=31, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
<?php 		} else { ?>			<LI><?php  putGS('The article $1 could not be created','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
<?php 		}
	}
?>	</BLOCKQUOTE></TD>
	</TR>
<?php  if ($created) { ?>	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  putGS('Do you want to edit the article?'); ?></LI></BLOCKQUOTE></TD>
	</TR>
<?php  } ?>	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
<?php  
    if ($created) { ?>	<INPUT TYPE="button" NAME="Yes" VALUE="<?php  putGS('Yes'); ?>" ONCLICK="location.href='/priv/pub/issues/sections/articles/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetNumVar($lii,0); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pencURL($cLanguage); ?>'">
	<INPUT TYPE="button" NAME="No" VALUE="<?php  putGS('No'); ?>" ONCLICK="location.href='/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>'">
<?php  } else { ?>
	<INPUT TYPE="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/priv/pub/issues/sections/articles/add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetNumVar($lii,0); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pencURL($cLanguage); ?>&cName=<?php p($cName); ?>&cType=<?php p($cType); ?>&cLanguage=<?php p($cLanguage); ?>&cKeywords=<?php p($cKeywords); ?>'">
<?php 
}
?>		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
<P>

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
<HR NOSHADE SIZE="1" COLOR="BLACK">
<a STYLE='font-size:8pt;color:#000000' href='http://www.campware.org' target='campware'>CAMPSITE  2.1.5 &copy 1999-2004 MDLF, maintained and distributed under GNU GPL by CAMPWARE</a>
</BODY>
<?php  } ?>

</HTML>
