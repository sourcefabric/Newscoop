<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
$globalfile=selectLanguageFile($Campsite['HTML_DIR'] . "/$ADMIN_DIR",'globals');
$localfile=selectLanguageFile("$ADMIN_DIR/issues","locals");
@include_once($globalfile);
@include_once($localfile);
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
?>
<?php
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");
?>


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
	query ("SELECT ManageIssue FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'Perm');
	 if ($NUM_ROWS) {
		fetchRow($Perm);
		$access = (getVar($Perm,'ManageIssue') == "Y");
	}
	else $access = 0;
    } ?>
    
 

<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">

	<TITLE><?php  putGS("Copying previous issue"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to add issues." )); ?>">
<?php  }
    query ("SELECT * FROM Issues WHERE 1=0", 'q_iss');
    query ("SELECT * FROM Sections WHERE 1=0", 'q_sect');
?></HEAD>

<?php  if ($access) { ?> 
 

<BODY >

<?php 
    todefnum('cOldNumber');
    todefnum('cNumber');
    todefnum('cPub');
?><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Copying previous issue"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/issues/?Pub=<?php  pencURL($cPub); ?>" class="breadcrumb" ><?php  putGS("Issues");  ?></A></TD>
<td class="breadcrumb_separator">&nbsp;</td>
<TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php 
    query ("SELECT Name FROM Publications WHERE Id=$cPub", 'publ');
    if ($NUM_ROWS) {
	fetchRow($publ);
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($publ,'Name'); ?></TD>

</TR></TABLE>

<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Copying previous issue"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php 
query ("SELECT * FROM Issues WHERE IdPublication=$cPub AND Number=$cOldNumber", 'q_iss');
//copy the whole structure; translated issues may exists
$nr=$NUM_ROWS;
for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_iss);
	$idlang=getVar($q_iss,'IdLanguage');

	$sql = "INSERT INTO Issues SET IdPublication=$cPub, Number=$cNumber, IdLanguage=$idlang, Name='" . getSVar($q_iss,'Name') . "', ShortName = '" . $cNumber . "'";
	$issueTplId = getSVar($q_iss,'IssueTplId');
	if ($issueTplId > 0)
		$sql .= ", IssueTplId=$issueTplId";
	$sectionTplId = getSVar($q_iss,'SectionTplId');
	if ($sectionTplId > 0)
		$sql .= ", SectionTplId=$sectionTplId";
	$articleTplId = getSVar($q_iss,'ArticleTplId');
	if ($articleTplId > 0)
		$sql .= ", ArticleTplId=$articleTplId";
	query($sql);
	query ("SELECT * FROM Sections WHERE IdPublication=$cPub AND NrIssue=$cOldNumber AND IdLanguage=$idlang", 'q_sect');
	$nr2=$NUM_ROWS;
	for($loop2=0;$loop2<$nr2;$loop2++) {
	    fetchRow($q_sect);
	    $sql = "INSERT INTO Sections SET IdPublication=$cPub, NrIssue=$cNumber, IdLanguage=$idlang, Number=".getSVar($q_sect,'Number').", Name='".getSVar($q_sect,'Name')."', ShortName='".getSVar($q_sect,'Number') . "'";
		$sectionTplId = getSVar($q_sect,'SectionTplId');
		if ($sectionTplId > 0)
			$sql .= ", SectionTplId=$sectionTplId";
		$articleTplId = getSVar($q_sect,'ArticleTplId');
		if ($articleTplId > 0)
			$sql .= ", ArticleTplId=$articleTplId";
		query($sql);
	}
}
?>	<?php  $logtext = getGS('New issue $1 from $2 in publication $3', $cNumber, $cOldNumber, getSVar($publ,'Name')); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=11, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
	<LI><?php  putGS('Copying done.'); ?></LI>
	</BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/issues/?Pub=<?php  pencURL($cPub); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
<P>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>
</BODY>
<?php  } ?>

</HTML>
