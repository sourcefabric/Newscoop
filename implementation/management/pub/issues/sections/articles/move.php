<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<?php
	require_once ("../../../../lib_campsite.php");
	$globalfile=selectLanguageFile('../../../..','globals');
	$localfile=selectLanguageFile('.','locals');
	@include ($globalfile);
	@include ($localfile);
	require_once ("../../../../languages.php");
	require_once("$DOCUMENT_ROOT/db_connect.php");
	require_once ("../../../../CampsiteInterface.php");
	require_once("$DOCUMENT_ROOT/classes/config.php");
    
	todefnum('TOL_UserId');
	todefnum('TOL_UserKey');
	query ("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'Usr');
	$access=($NUM_ROWS != 0);
	if ($NUM_ROWS) {
		fetchRow($Usr);
		query ("SELECT * FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'XPerm');
		if ($NUM_ROWS)
			fetchRow($XPerm);
		else
			$access = 0; //added lately; a non-admin can enter the administration area; he exists but doesn't have ANY rights
		$xpermrows= $NUM_ROWS;
	} else {
		query ("SELECT * FROM UserPerm WHERE 1=0", 'XPerm');
	}
?>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<META HTTP-EQUIV="Expires" CONTENT="now">
<TITLE><?php  putGS("Articles"); ?></TITLE>
<?php if ($access == 0) { ?>
<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/logout.php">
<?php }
	query ("SELECT Id, Name FROM Languages WHERE 1=0", 'ls');
	query ("SELECT * FROM Articles WHERE 1=0", 'q_art');
?>
</HEAD>
<?php
if ($access) {

	if (getVar($XPerm,'AddArticle') == "Y")
		$aaa=1;
	else 
		$aaa=0;

	if (getVar($XPerm,'ChangeArticle') == "Y")
		$caa=1;
	else 
		$caa=0;

	if (getVar($XPerm,'DeleteArticle') == "Y")
		$daa=1;
	else
		$daa=0;

	if (getVar($XPerm,'Publish') == "Y")
		$pa=1;
	else 
		$pa=0;

	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('Article');
	todefnum('Language');
	todefnum('sLanguage');
	todefnum('ArtOffs');
	todef('move');
	todefnum('pos', 1);
	if ($pa & $move != "" && $Article > 0) {
		switch ($move) {
		case 'up_rel':
			move_article_rel($Pub, $Language, $Issue, $Section, $Article, 'up');
			break;
		case 'down_rel':
			move_article_rel($Pub, $Language, $Issue, $Section, $Article, 'down');
			break;
		case 'abs':
			move_article_abs($Pub, $Language, $Issue, $Section, $Article, $pos);
			break;
		default: ;
		}
	}
	$rlink = "/priv/pub/issues/sections/articles/?Pub=$Pub&Issue=$Issue"
	       . "&Section=$Section&Language=$Language&sLanguage=$sLanguage&ArtOffs=$ArtOffs";

?>
<META HTTP-EQUIV="Refresh" CONTENT="0; URL=<?php echo $rlink; ?>">
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
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
		    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Articles"); ?></B></DIV>
		    <HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR><TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/priv/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Sections"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><B><?php  putGS("Sections");  ?></B></A></TD>
<TD><A HREF="/priv/pub/issues/?Pub=<?php  p($Pub); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Issues"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/?Pub=<?php  p($Pub); ?>" ><B><?php  putGS("Issues");  ?></B></A></TD>
<TD><A HREF="/priv/pub/" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD><TD><A HREF="/priv/pub/" ><B><?php  putGS("Publications");  ?></B></A></TD>
<TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD><TD><A HREF="/priv/home.php" ><B><?php  putGS("Home");  ?></B></A></TD>
<TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD><TD><A HREF="/priv/logout.php" ><B><?php  putGS("Logout");  ?></B></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php 
    if ($sLanguage == "")
	$sLanguage= 0;

    query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	    if ($NUM_ROWS) {

		query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		fetchRow($q_pub);
		fetchRow($q_iss);
		fetchRow($q_sect);
		fetchRow($q_lang);

		$sql = "select Name from Articles where IdPublication=$Pub and NrIssue=$Issue and NrSection=$Section and Number=$Article and IdLanguage=$Language";
		query($sql, 'q_art');
		fetchRow($q_art);

?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pgetHVar($q_pub,'Name'); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Issue"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Section"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B></TD>

</TR></TABLE>

<P><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
<?php  if ($aaa != 0) { ?>
	<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><B><?php  putGS("Back to article list"); ?></B></A></TD></TR></TABLE></TD>
<?php  } ?>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
	<TR BGCOLOR="#C0D0FF">
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B>
		<?php
		switch($move) {
		case 'up_rel':
			putGS("Moving article '$1' up one position...", getVar($q_art, 'Name'), $pos);
			break;
		case 'down_rel':
			putGS("Moving article '$1' down one position...", getVar($q_art, 'Name'), $pos);
			break;
		case 'abs':
			putGS("Moving article '$1' to position $2...", getVar($q_art, 'Name'), $pos);
			break;
		default: ;
		}
		?></B></TD>
	</TR>
</TABLE>

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
<?php  } ?>
<?php
$ci = new CampsiteInterface;
$ci->CopyrightNotice();
?>
</BODY>

</HTML>
