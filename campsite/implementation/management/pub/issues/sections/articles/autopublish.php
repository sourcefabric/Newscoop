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
    


<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Article automatic publishing schedule"); ?></TITLE>
<? if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/logout.php">
<? }
    query ("SELECT * FROM Images WHERE 1=0", 'q_img');
?></HEAD>

<? if ($access) {


   if (getVar($XPerm,'Publish') == "Y")
	$pb=1;
    else 
	$pb=0;
    
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
<?
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('Article');
	todefnum('Language');
	todefnum('sLanguage');
	todef('publish_time');
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
		    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Article automatic publishing schedule"); ?></B></DIV>
		    <HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR><TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/priv/pub/issues/sections/articles/edit.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Back to article details"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/edit.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>" ><B><?php  putGS("Back to article details");  ?></B></A></TD>
<TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Articles"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>" ><B><?php  putGS("Articles");  ?></B></A></TD>
<TD><A HREF="/priv/pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Sections"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>" ><B><?php  putGS("Sections");  ?></B></A></TD>
<TD><A HREF="/priv/pub/issues/?Pub=<? p($Pub); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Issues"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/?Pub=<? p($Pub); ?>" ><B><?php  putGS("Issues");  ?></B></A></TD>
<TD><A HREF="/priv/pub/" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD><TD><A HREF="/priv/pub/" ><B><?php  putGS("Publications");  ?></B></A></TD>
<TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD><TD><A HREF="/priv/home.php" ><B><?php  putGS("Home");  ?></B></A></TD>
<TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD><TD><A HREF="/priv/logout.php" ><B><?php  putGS("Logout");  ?></B></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?
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
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><? pgetHVar($q_pub,'Name'); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Issue"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Section"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><? pgetHVar($q_sect,'Number'); ?>. <? pgetHVar($q_sect,'Name'); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Article"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><? pgetHVar($q_art,'Name'); ?></B></TD>

</TR></TABLE>

<?
if (getVar($q_art,'Published') != 'N') {
	if ($publish_time == "")
		$publish_time = date("Y-m-d H:i");
	if ($publish_time != "") {
		$sql = "select * from ArticlePublish where NrArticle = $Article and IdLanguage = $sLanguage and PublishTime = '$publish_time'";
		query($sql, 'q_autop');
		if ($NUM_ROWS > 0) {
			fetchRow($q_autop);
			$action = getVar($q_autop, 'Action');
		}
		$datetime = explode(" ", trim($publish_time));
		$publish_date = $datetime[0];
		$publish_time = explode(":", trim($datetime[1]));
		$publish_hour = $publish_time[0];
		$publish_min = $publish_time[1];
	}
?>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="autopublish_do_add.php" >
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("Schedule a new publish action"); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? echo $Pub; ?>">
	<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<? echo $Issue; ?>">
	<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<? echo $Section; ?>">
	<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<? echo $Article; ?>">
	<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? echo $Language; ?>">
	<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<? echo $sLanguage; ?>">
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Date"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="publish_date" SIZE="10" MAXLENGTH="10" VALUE="<? p($publish_date); ?>">
		<? putGS('YYYY-MM-DD'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Time"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="publish_hour" SIZE="2" MAXLENGTH="2" VALUE="<? p($publish_hour); ?>"> :
		<INPUT TYPE="TEXT" NAME="publish_min" SIZE="2" MAXLENGTH="2" VALUE="<? p($publish_min); ?>">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Action"); ?>:</TD>
		<TD>
		<SELECT NAME="action">
			<OPTION VALUE="P" <? if ($action == "P") echo "SELECTED"; ?>><? putGS("Publish"); ?></OPTION>
			<OPTION VALUE="U" <? if ($action == "U") echo "SELECTED"; ?>><? putGS("Unpublish"); ?></OPTION>
		</SELECT>
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/priv/pub/issues/sections/articles/edit.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
</P>

<P><?
	todefnum('Offs', 0);
	todefnum('lpp', 10);

	query ("SELECT * FROM ArticlePublish WHERE NrArticle=$Article AND IdLanguage=$sLanguage ORDER BY PublishTime DESC LIMIT $Offs, ".($lpp+1), 'q_autop');
	if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=$lpp;
	$color= 0;
	?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
	<TR BGCOLOR="#C0D0FF">
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Date/Time"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Action"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
	</TR>
<?
    for($loop=0; $loop<$nr; $loop++) {
	fetchRow($q_autop);
	if ($i) {
		$url_publish_time = encURL(getVar($q_autop,'PublishTime'));
		?>	<TR <?php  if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
		<TD >
			<A HREF="/priv/pub/issues/sections/articles/autopublish.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&publish_time=<? echo $url_publish_time; ?>"><? pgetHVar($q_autop,'PublishTime'); ?></A>
		</TD>
		<TD >
<?
	$action = getVar($q_autop,'Action');
	if ($action == "P")
		putGS("Publish");
	else
		putGS("Unpublish");
?>&nbsp;
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="/priv/pub/issues/sections/articles/autopublish_del.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&publish_time=<? echo $url_publish_time; ?>"><IMG SRC="/priv/img/icon/x.gif" BORDER="0" ALT="<? putGS('Delete entry'); ?>"></A>
		</TD>
	<? } ?>
	</TR>
<?
    $i--;
    }
?>	<TR><TD COLSPAN="2" NOWRAP>
<? if ($Offs <= 0) { ?>		&lt;&lt; <?php  putGS('Previous'); ?>
<? } else { ?>		<B><A HREF="autopublish.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&Offs=<? p($Offs - $lpp); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<? } ?><? if ($nr < $lpp+1) { ?>		 | <?php  putGS('Next'); ?> &gt;&gt;
<? } else { ?>		 | <B><A HREF="autopublish.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&Offs=<? p($Offs + $lpp); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<? } ?>	</TD></TR>
</TABLE>
<? } else { ?><BLOCKQUOTE>
	<LI><? putGS('No entries.'); ?></LI>
</BLOCKQUOTE>
<? } ?>
<? } else { ?><BLOCKQUOTE>
	<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Scheduling a new publish action"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>The article is new; it is not possible to schedule it for automatic publishing.</BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
	<INPUT TYPE="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/priv/pub/issues/sections/articles/edit.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>'">
		</DIV>
		</TD>
	</TR>
	</TABLE></CENTER>
</BLOCKQUOTE>
<? } ?>
<? } else { ?><BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>
<? } else { ?><BLOCKQUOTE>
	<LI><? putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<? } ?>
<? } else { ?><BLOCKQUOTE>
	<LI><? putGS('No such section.'); ?></LI>
</BLOCKQUOTE>
<? } ?>
<? } else { ?><BLOCKQUOTE>
	<LI><? putGS('No such article.'); ?></LI>
</BLOCKQUOTE>
<? } ?>
<HR NOSHADE SIZE="1" COLOR="BLACK">
<a STYLE='font-size:8pt;color:#000000' href='http://www.campware.org' target='campware'>CAMPSITE  2.1.6-rc1 &copy 1999-2004 MDLF, maintained and distributed under GNU GPL by CAMPWARE</a>
</BODY>
<? } ?>

</HTML>

