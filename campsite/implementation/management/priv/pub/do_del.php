<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
$globalfile=selectLanguageFile('globals');
$localfile=selectLanguageFile("pub");
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
	query ("SELECT DeletePub FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'Perm');
	 if ($NUM_ROWS) {
		fetchRow($Perm);
		$access = (getVar($Perm,'DeletePub') == "Y");
	}
	else $access = 0;
    } ?>
    
 

<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">

	<TITLE><?php  putGS("Deleting publication"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to delete publications." )); ?>">
<?php  } ?></HEAD>

<?php  if ($access) { ?> 
 

<BODY >

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Deleting publication"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php 
    todefnum('Pub');
    todefnum('del',1);
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) { 
	    fetchRow($q_pub);
	
	?><P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Deleting publication"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php 
    query ("SELECT COUNT(*) FROM Issues WHERE IdPublication=$Pub", 'q_iss');
    fetchRowNum($q_iss);
    if (getNumVar($q_iss,0) != 0) {
	$del= 0; ?>	<LI><?php  putGS('There are $1 issue(s) left.',getNumVar($q_iss,0)); ?></LI>
    <?php  }
    
    query ("SELECT COUNT(*) FROM Sections WHERE IdPublication=$Pub", 'q_sect');
    fetchRowNum($q_sect);
    if (getNumVar($q_sect,0) != 0) {
	$del= 0; ?>	<LI><?php  putGS('There are $1 section(s) left.',getNumVar($q_sect,0)); ?></LI>
    <?php  }
    
    query ("SELECT COUNT(*) FROM Articles WHERE IdPublication=$Pub", 'q_art');
    fetchRowNum($q_art);
    if (getNumVar($q_art,0) != 0) {
	$del= 0; ?>	<LI><?php  putGS('There are $1 article(s) left.',getNumVar($q_art,0)); ?></LI>
    <?php  }
    
    query ("SELECT COUNT(*) FROM Subscriptions WHERE IdPublication=$Pub", 'q_subs');
    fetchRowNum($q_subs);
    if (getNumVar($q_subs,0) != 0) {
	$del= 0; ?>	<LI><?php  putGS('There are $1 subscription(s) left.',getNumVar($q_subs,0)); ?></LI>
    <?php  }
    
    $AFFECTED_ROWS=0;
    
	if ($del) {
		query("DELETE FROM Aliases WHERE IdPublication = " . $Pub);
		query("DELETE FROM Publications WHERE Id = " . $Pub);
		$params = array($operation_attr=>$operation_delete, "IdPublication"=>"$Pub" );
		$msg = build_reset_cache_msg($cache_type_publications, $params);
		send_message("127.0.0.1", server_port(), $msg, $err_msg);
	}

    if ($AFFECTED_ROWS > 0) { ?>	<LI><?php  putGS('The publication $1 has been deleted.','<B>'.getHVar($q_pub,'Name').'</B>'); ?></LI>
<?php  $logtext = getGS('Publication $1 deleted',getHVar($q_pub,'Name')); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=2, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
<?php  } else { ?>	<LI><?php  putGS('The publication $1 could not be deleted.','<B>'.getHVar($q_pub,'Name').'</B>'); ?></LI>
<?php  } ?>	</BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
<?php  if ($AFFECTED_ROWS > 0) { ?>		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/pub/'">
<?php  } else { ?>		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/pub/'">
<?php  } ?>		</DIV>
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
