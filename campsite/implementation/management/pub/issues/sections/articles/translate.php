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
    


<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Translate article"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/logout.php">
<?php  }
    query ("SELECT Name FROM Articles WHERE 1=0", 'q_alist');
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'q_lang');
?></HEAD>

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

<?php  if ($access) { ?><BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">

<?php  
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Article');
    todefnum('Language');
?><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
		    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Translate article"); ?></B></DIV>
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

</TR></TABLE>


	<?php  if($xpermrows) {
		$xaccess=(getvar($XPerm,'ChangeArticle') == "Y");
		if($xaccess =='') $xaccess = 0;
	}
	else $xaccess = 0;
	?>
	


<?php 
    query ("SELECT ($xaccess != 0) or ((".getVar($q_art,'IdUser')." = ".getVar($Usr,'Id').") and ('".getVar($q_art,'Published')."' = 'N'))", 'q_xperm');
    fetchRowNum($q_xperm);
    if (getNumVar($q_xperm,0)) { ?><P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_translate.php" >
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("Translate article"); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Article"); ?>:</TD>
		<TD>
<?php 
    query ("SELECT Name FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article", 'q_alist');
    $comma= 0;
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_alist);
	if ($comma)
	    print ', ';
	else
	    $comma= 1;
	pgetHVar($q_alist,'Name');
    }
?>		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
		<TD>
		<SELECT NAME="cLanguage">
<?php 
    query ("SELECT Id, Name FROM Languages ORDER BY Name", 'q_lang');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_lang);
	query ("SELECT COUNT(*) FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article AND IdLanguage=".getVar($q_lang,'Id'), 'q_xart');
	fetchRowNum($q_xart);
	if (getNumVar($q_xart,0) == 0)
			pcomboVar(getHVar($q_lang,'Id'),'',getHVar($q_lang,'Name'));
    }
?>		</SELECT>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Keywords"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="cKeywords" SIZE="32" MAXLENGTH="255">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		<INPUT TYPE="HIDDEN" NAME="cType" VALUE="<?php  pgetHVar($q_art,'Type'); ?>">
		<INPUT TYPE="HIDDEN" NAME="cOnFrontPage" VALUE="<?php  pgetHVar($q_art,'OnFrontPage'); ?>">
		<INPUT TYPE="HIDDEN" NAME="cOnSection" VALUE="<?php  pgetHVar($q_art,'OnSection'); ?>">
		<INPUT TYPE="HIDDEN" NAME="cPublic" VALUE="<?php  pgetHVar($q_art,'Public'); ?>">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
<?php  todef('Back'); ?>		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<?php  pencHTML($Back); ?>">
<?php  if ($Back != "") { ?>		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='<?php  p($Back); ?>'">
<?php  } else { ?>		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>'">
<?php  } ?>		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
<P>
<?php  } else { ?>    
<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <font color="red"><?php  putGS("Access denied"); ?> </font></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><font color=red><li><?php  putGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users." ); ?></li></font></BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<A HREF="/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>"><IMG SRC="/priv/img/button/ok.gif" BORDER="0" ALT="OK"></A>
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
<P>

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
