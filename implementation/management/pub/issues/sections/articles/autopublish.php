<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<?php
include($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files();
require_once($_SERVER['DOCUMENT_ROOT']."/priv/CampsiteInterface.php");

todefnum('TOL_UserId');
todefnum('TOL_UserKey');
query ("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'Usr');
$access=($NUM_ROWS != 0);
if ($NUM_ROWS) {
	fetchRow($Usr);
	query ("SELECT * FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'XPerm');
	if ($NUM_ROWS) {
		fetchRow($XPerm);
	}
	else $access = 0;	//added lately; a non-admin can enter the administration area; he exists but doesn't have ANY rights
	$xpermrows= $NUM_ROWS;
} else {
	query ("SELECT * FROM UserPerm WHERE 1=0", 'XPerm');
}
?>
    
<HEAD>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Article automatic publishing schedule"); ?></TITLE>
<?php
if ($access == 0) {
?>
<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/logout.php">
<?php
}
query ("SELECT * FROM Images WHERE 1=0", 'q_img');
?>
</HEAD>

<?php
if ($access) {
	if (getVar($XPerm,'Publish') == "Y")
		$pb=1;
	else
		$pb=0;
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
	<TR><TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/priv/pub/issues/sections/articles/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Back to article details"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>" ><B><?php  putGS("Back to article details");  ?></B></A></TD>
<TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>&Section=<?php p($Section); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Articles"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>&Section=<?php p($Section); ?>" ><B><?php  putGS("Articles");  ?></B></A></TD>
<TD><A HREF="/priv/pub/issues/sections/?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Sections"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/sections/?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>" ><B><?php  putGS("Sections");  ?></B></A></TD>
<TD><A HREF="/priv/pub/issues/?Pub=<?php p($Pub); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Issues"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/?Pub=<?php p($Pub); ?>" ><B><?php  putGS("Issues");  ?></B></A></TD>
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
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php pgetHVar($q_pub,'Name'); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Issue"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php pgetHVar($q_iss,'Number'); ?>. <?php pgetHVar($q_iss,'Name'); ?> (<?php pgetHVar($q_lang,'Name'); ?>)</B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Section"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php pgetHVar($q_sect,'Number'); ?>. <?php pgetHVar($q_sect,'Name'); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Article"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php pgetHVar($q_art,'Name'); ?></B></TD>

</TR></TABLE>

<?php
if (getVar($q_art,'Published') != 'N') {
	if ($publish_time == "")
		$publish_time = date("Y-m-d H:i");
	if ($publish_time != "") {
		$sql = "select * from ArticlePublish where NrArticle = $Article and IdLanguage = $sLanguage and PublishTime = '$publish_time'";
		query($sql, 'q_autop');
		if ($NUM_ROWS > 0) {
			fetchRow($q_autop);
			$publish = getVar($q_autop, 'Publish');
			$front_page = getVar($q_autop, 'FrontPage');
			$section_page = getVar($q_autop, 'SectionPage');
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
	<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php echo $Pub; ?>">
	<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php echo $Issue; ?>">
	<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php echo $Section; ?>">
	<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php echo $Article; ?>">
	<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php echo $Language; ?>">
	<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php echo $sLanguage; ?>">
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Date"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="publish_date" SIZE="10" MAXLENGTH="10" VALUE="<?php p($publish_date); ?>">
		<?php putGS('YYYY-MM-DD'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Time"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="publish_hour" SIZE="2" MAXLENGTH="2" VALUE="<?php p($publish_hour); ?>"> :
		<INPUT TYPE="TEXT" NAME="publish_min" SIZE="2" MAXLENGTH="2" VALUE="<?php p($publish_min); ?>">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="CENTER" COLSPAN="2"><b><?php  putGS("Actions"); ?></b></TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Publish"); ?>:</TD>
		<TD>
		<SELECT NAME="action">
			<OPTION VALUE=" ">---</OPTION>
			<OPTION VALUE="P" <?php if ($publish == "P") echo "SELECTED"; ?>><?php putGS("Publish"); ?></OPTION>
			<OPTION VALUE="U" <?php if ($publish == "U") echo "SELECTED"; ?>><?php putGS("Unpublish"); ?></OPTION>
		</SELECT>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Front page"); ?>:</TD>
		<TD>
		<SELECT NAME="front_page">
			<OPTION VALUE=" ">---</OPTION>
			<OPTION VALUE="S" <?php if ($front_page == "S") echo "SELECTED"; ?>><?php putGS("Show on front page"); ?></OPTION>
			<OPTION VALUE="R" <?php if ($front_page == "R") echo "SELECTED"; ?>><?php putGS("Remove from front page"); ?></OPTION>
		</SELECT>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Section page"); ?>:</TD>
		<TD>
		<SELECT NAME="section_page">
			<OPTION VALUE=" ">---</OPTION>
			<OPTION VALUE="S" <?php if ($section_page == "S") echo "SELECTED"; ?>><?php putGS("Show on section page"); ?></OPTION>
			<OPTION VALUE="R" <?php if ($section_page == "R") echo "SELECTED"; ?>><?php putGS("Remove from section page"); ?></OPTION>
		</SELECT>
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/priv/pub/issues/sections/articles/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
</P>

<P><?php
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
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Publish"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Front page"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Section page"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
	</TR>
<?php
	for($loop=0; $loop<$nr; $loop++) {
	fetchRow($q_autop);
	if ($i) {
		$url_publish_time = encURL(getVar($q_autop,'PublishTime'));
		?>	<TR <?php  if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
		<TD >
			<A HREF="/priv/pub/issues/sections/articles/autopublish.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>&publish_time=<?php echo $url_publish_time; ?>"><?php pgetHVar($q_autop,'PublishTime'); ?></A>
		</TD>
		<TD >
<?php
	$publish = getVar($q_autop,'Publish');
	if ($publish == "P")
		putGS("Publish");
	if ($publish == "U")
		putGS("Unpublish");
?>&nbsp;
		</TD>
		<TD >
<?php
	$front_page = getVar($q_autop,'FrontPage');
	if ($front_page == "S")
		putGS("Show");
	if ($front_page == "R")
		putGS("Remove");
?>&nbsp;
		</TD>
		<TD >
<?php
	$section_page = getVar($q_autop,'SectionPage');
	if ($section_page == "S")
		putGS("Show");
	if ($section_page == "R")
		putGS("Remove");
?>&nbsp;
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="/priv/pub/issues/sections/articles/autopublish_del.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>&publish_time=<?php echo $url_publish_time; ?>"><IMG SRC="/priv/img/icon/x.gif" BORDER="0" ALT="<?php putGS('Delete entry'); ?>"></A>
		</TD>
	<?php } ?>
	</TR>
<?php
    $i--;
    }
?>	<TR><TD COLSPAN="2" NOWRAP>
<?php if ($Offs <= 0) { ?>		&lt;&lt; <?php  putGS('Previous'); ?>
<?php } else { ?>		<B><A HREF="autopublish.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>&Offs=<?php p($Offs - $lpp); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php } ?><?php if ($nr < $lpp+1) { ?>		 | <?php  putGS('Next'); ?> &gt;&gt;
<?php } else { ?>		 | <B><A HREF="autopublish.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>&Offs=<?php p($Offs + $lpp); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php } ?>	</TD></TR>
</TABLE>
<?php } else { ?><BLOCKQUOTE>
	<LI><?php putGS('No entries.'); ?></LI>
</BLOCKQUOTE>
<?php } ?>
<?php } else { ?><BLOCKQUOTE>
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
	<INPUT TYPE="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/priv/pub/issues/sections/articles/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Section=<?php p($Section); ?>&Article=<?php p($Article); ?>&Language=<?php p($Language); ?>&sLanguage=<?php p($sLanguage); ?>'">
		</DIV>
		</TD>
	</TR>
	</TABLE></CENTER>
</BLOCKQUOTE>
<?php } ?>
<?php } else { ?><BLOCKQUOTE>
	<LI><?php putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php } ?>
<?php } else { ?><BLOCKQUOTE>
	<LI><?php putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php } ?>
<?php } else { ?><BLOCKQUOTE>
	<LI><?php putGS('No such section.'); ?></LI>
</BLOCKQUOTE>
<?php } ?>
<?php } else { ?><BLOCKQUOTE>
	<LI><?php putGS('No such article.'); ?></LI>
</BLOCKQUOTE>
<?php } ?>
<?php }
CampsiteInterface::CopyrightNotice();
?>
</BODY>

</HTML>
