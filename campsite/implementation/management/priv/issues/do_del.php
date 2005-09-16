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
	query ("SELECT DeleteIssue FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'Perm');
	 if ($NUM_ROWS) {
		fetchRow($Perm);
		$access = (getVar($Perm,'DeleteIssue') == "Y");
	}
	else $access = 0;
    } ?>
    
 

<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">

	<TITLE><?php  putGS("Deleting issue"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to delete issues." )); ?>">
<?php  } ?></HEAD>

<?php  if ($access) { ?> 
 

<BODY >

<?php 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Deleting issue"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/issues/?Pub=<?php  pencURL($Pub); ?>" class="breadcrumb" ><?php  putGS("Issues");  ?></A></TD>
<td class="breadcrumb_separator">&nbsp;</td>
<TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php 
    query ("SELECT Name FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
	query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    fetchRow($q_iss);
	    fetchRow($q_pub);
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_pub,'Name'); ?></TD>

</TR></TABLE>

<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Deleting issue"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php 
    todefnum('del', 1);
    $NUM_ROWS = 0;
    $AFFECTED_ROWS = 0;
    query ("SELECT COUNT(*) FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language", 'q_art');
    fetchRowNum($q_art);
    if (getNumVar($q_art,0) != 0) {
	$del= 0; ?>	<LI><?php  putGS('There are $1 article(s) left.',getNumVar($q_art,0)); ?></LI>
    <?php  }
    
	if ($del){
		query ("SELECT IdPublication FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language LIMIT 1", 'q_sect');
		if ($NUM_ROWS) {
			query ("DELETE FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language", 'q_sect');
	    	    	if ($AFFECTED_ROWS > 0) {?>
				<LI><?php  putGS('All sections from Issue $1 from publication $2 deleted','<B>'.getHVar($q_iss,'Name').'</B>', '<B>'.getHVar($q_pub,'Name').'</B>'); ?></LI>
					<?php  $logtext = getGS('All sections from Issue $1 from publication $2 deleted',getHVar($q_iss,'Name'),getHVar($q_pub,'Name')); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=12, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
			<?php  } else { ?>				<LI><?php  putGS('The issue $1 could not be deleted.','<B>'.getHVar($q_iss,'Name').'</B>'); ?></LI>
				<?php  $del = 0;
			}
		}
	}

	if ($del){
		query ("DELETE FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language");
		if ($AFFECTED_ROWS > 0) { ?>
			<LI><?php  putGS('The issue $1 has ben deleted.','<B>'.getHVar($q_iss,'Name').'</B>'); ?></LI>
			<?php  $logtext = getGS('Issue $1 from publication $2 deleted',getHVar($q_iss,'Name'),getHVar($q_pub,'Name')); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=12, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
		<?php  } else { ?>			<LI><?php  putGS('The issue $1 could not be deleted.','<B>'.getHVar($q_iss,'Name').'</B>'); ?></LI>
		<?php  }
	} ?></BLOCKQUOTE></TD>
	</TR>
	
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/issues/?Pub=<?php  pencURL($Pub); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
<P>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>
</BODY>
<?php  } ?>

</HTML>
