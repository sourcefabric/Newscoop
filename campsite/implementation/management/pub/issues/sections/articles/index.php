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
<script>
<!--
/* A slightly modified version of "Break-out-of-frames script"
   By JavaScript Kit (http://javascriptkit.com)                     */
if (window != top.fmain && window != top) {
	if (top.fmenu)
		top.fmain.location.href=location.href
	else
		top.location.href=location.href
}
-->
</script>

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
	$sql = "select count(*) as nr_art from Articles where IdPublication = $Pub and "
	     . "NrIssue = $Issue and NrSection = $Section and IdLanguage = $Language";
	query($sql, 'art_count');
	fetchRow($art_count);
	$art_count = getVar($art_count, 'nr_art');

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

?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pgetHVar($q_pub,'Name'); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Issue"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Section"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B></TD>

</TR></TABLE>

<P><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
<?php  if ($aaa != 0) { ?>
	<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&Back=<?php  pencURL($REQUEST_URI); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&Back=<?php  pencURL($REQUEST_URI); ?>" ><B><?php  putGS("Add new article"); ?></B></A></TD></TR></TABLE></TD>
<?php  } ?>
	<TD ALIGN="RIGHT">
	<FORM METHOD="GET" ACTION="index.php" NAME="">
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" BGCOLOR="#C0D0FF">
	<TR>
		<TD><?php  putGS('Language'); ?>:</TD>
		<TD><SELECT NAME="sLanguage"><OPTION><?php 

		    query ("SELECT Id, Name FROM Languages ORDER BY Name", 'ls');
		    $nr=$NUM_ROWS;
		for($loop=0;$loop<$nr;$loop++) {
			fetchRow($ls);
			pcomboVar(getHVar($ls,'Id'),'',getHVar($ls,'Name'));
	        }
		?>		    </SELECT></TD>
		<TD><INPUT TYPE="submit" NAME="Search" VALUE="<?php  putGS('Search'); ?>"></TD>
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
	</TR>
	</TABLE>
</FORM>
	</TD>
</TABLE>

<?php 
    if ($sLanguage) {
	$ll= "AND IdLanguage=$sLanguage";
	$oo= "";
    } else {
	$ll= "";
	$oo= ", LangOrd asc, IdLanguage asc";
    }

    $kwdid= "ssssssssss";
?><P><?php 
    todefnum('ArtOffs');
    if ($ArtOffs < 0) $ArtOffs= 0;
    todefnum('lpp', 20);

	$sql = "SELECT *, abs($Language - IdLanguage) as LangOrd FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section $ll ORDER BY ArticleOrder ASC $oo LIMIT $ArtOffs, ".($lpp+1);
	query($sql, 'q_art');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= $lpp;
	$color= 0;
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
	<TR BGCOLOR="#C0D0FF">
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Name<BR><SMALL>(click to edit)</SMALL>"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Type"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Language"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Status"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Order"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Preview"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Translate"); ?></B></TD>
<?php  if ($aaa != 0) { ?>		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Duplicate"); ?></B></TD>
<?php  } ?><?php  if ($daa != 0) { ?>		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
<?php  } ?>	</TR>
<?php 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_art);
	if ($i) { ?>	<TR <?php  if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
		<TD >
			<?php  if (getVar($q_art,'Number') == $kwdid) { ?>&nbsp;<?php  } ?><A HREF="/priv/pub/issues/sections/articles/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>"><?php  pgetHVar($q_art,'Name'); ?>&nbsp;</A>
		</TD>
		<TD ALIGN="RIGHT">
			<?php  pgetHVar($q_art,'Type'); ?>
		</TD>

		<TD >
<?php 
    query ("SELECT Name FROM Languages WHERE Id=".getVar($q_art,'IdLanguage'), 'q_ail');
    fetchRow($q_ail);
    pgetHVar($q_ail,'Name');
?>		</TD>
		<TD ALIGN="CENTER">
<?php  if (getVar($q_art,'Published') == "Y") { ?>
	<A HREF="/priv/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>&Back=<?php  pencURL($REQUEST_URI); ?>"><?php  putGS("Published"); ?></A>
<?php  } elseif (getVar($q_art,'Published') == "N") { ?>
	<A HREF="/priv/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>&Back=<?php  pencURL($REQUEST_URI); ?>"><?php  putGS("New"); ?></A>
<?php  } else { ?>
	<A HREF="/priv/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>&Back=<?php  pencURL($REQUEST_URI); ?>"><?php  putGS("Submitted"); ?></A>
<?php  } ?>		</TD>

<?php // article order link ?>
		<?php  if ($pa != 0) { ?>		<TD ALIGN="CENTER" NOWRAP>
		<?php if ($ArtOffs <= 0 && $loop == 0) { ?>
		<img src="/priv/img/up-dis.png">
		<?php } else { ?>
		<A HREF="/priv/pub/issues/sections/articles/move.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>&move=up_rel&pos=1&ArtOffs=<?php  p($ArtOffs); ?>"><img src="/priv/img/up.png" width="20" height="20" border="0"></A>
		<?php } ?>
		<?php if ($nr < $lpp+1 && $loop >= ($nr-1)) { ?>
		<img src="/priv/img/down-dis.png">
		<?php } else { ?>
		<A HREF="/priv/pub/issues/sections/articles/move.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>&move=down_rel&pos=1&ArtOffs=<?php  p($ArtOffs); ?>"><img src="/priv/img/down.png" width="20" height="20" border="0"></A>
		<?php } ?>
		<select name="pos" onChange="location=this.options[this.selectedIndex].value">
		<?php
		$mlink = "/priv/pub/issues/sections/articles/move.php?Pub=$Pub&Issue=$Issue"
		       . "&Section=$Section&Article=" . getUVar($q_art,'Number')
		       . "&Language=$Language&sLanguage=" . getUVar($q_art,'IdLanguage')
		       . "&ArtOffs=$ArtOffs";
		$current_index = $ArtOffs + $loop + 1;
		for ($j = 1; $j <= $art_count; $j++) {
			if ($current_index != $j) {
				$vlink = $mlink . "&move=abs&pos=$j";
				echo "\t\t<option value=\"$vlink\">$j</option>\n";
			} else {
				echo "\t\t<option value=\"$mlink\" selected>$j</option>\n";
			}
		}
		?>
		</select>
		</TD>
<?php  } ?>

		<TD ALIGN="CENTER">
			<A HREF="" ONCLICK="window.open('/priv/pub/issues/sections/articles/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>', 'fpreview', 'resizable=yes, menubar=yes, toolbar=yes, width=680, height=560'); return false"><?php  putGS("Preview"); ?></A>
		</TD>
		<TD ALIGN="CENTER">
<?php  if (getVar($q_art,'Number') != $kwdid) { ?>			<A HREF="/priv/pub/issues/sections/articles/translate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&Back=<?php  pencURL($REQUEST_URI); ?>"><?php  putGS("Translate"); ?></A>
<?php  } else { ?>		&nbsp;
<?php  } ?>		</TD>
<?php  if ($aaa != 0) { ?>		<TD ALIGN="CENTER">
			<A HREF="/priv/pub/issues/sections/articles/fduplicate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>"><?php  putGS("Duplicate"); ?></A>
		</TD>
<?php  } ?>	<?php  if ($daa != 0) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/priv/pub/issues/sections/articles/del.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetUVar($q_art,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pgetUVar($q_art,'IdLanguage'); ?>&Back=<?php  pencURL($REQUEST_URI); ?>"><IMG SRC="/priv/img/icon/x.gif" BORDER="0" ALT="<?php  putGS('Delete article $1',getHVar($q_art,'Name')); ?>"></A>
		</TD>
	<?php  }
		if (getVar($q_art,'Number') != $kwdid)
			$kwdid=getVar($q_art,'Number');
		?>	</TR>
<?php 
    $i--;
    }
}
?>	<TR><TD COLSPAN="2" NOWRAP>
<?php 
    if ($ArtOffs <= 0) { ?>		&lt;&lt; <?php  putGS('Previous'); ?>
<?php  } else { ?>		<B><A HREF="index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&ArtOffs=<?php  p($ArtOffs - $lpp); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php  }

    if ($nr < $lpp+1) { ?>		 | <?php  putGS('Next'); ?> &gt;&gt;
<?php  } else { ?>		 | <B><A HREF="index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&ArtOffs=<?php  p($ArtOffs + $lpp); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>	</TD></TR>
</TABLE>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No articles.'); ?></LI>
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
<?php  } ?>
<?php
$ci = new CampsiteInterface;
$ci->CopyrightNotice();
?>
</BODY>

</HTML>

