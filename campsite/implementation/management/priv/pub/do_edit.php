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
	query ("SELECT ManagePub FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'Perm');
	 if ($NUM_ROWS) {
		fetchRow($Perm);
		$access = (getVar($Perm,'ManagePub') == "Y");
	}
	else $access = 0;
    } ?>
    
 

<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">

	<TITLE><?php  putGS("Changing publication information"); ?></TITLE>
<?php
if ($access == 0) {
?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to change publication information." )); ?>">
<?php
}
?></HEAD>

<?php
if ($access) {
?> 
 

<BODY >

<?php 
	todefnum('Pub');
	todef('cName');
	todef('cSite');
	todefnum('cDefaultAlias');
	todefnum('cLanguage');
	todefnum('cURLType');
	todefnum('cPayTime');
	todef('cTimeUnit');
	todef('cUnitCost');
	todef('cCurrency');
	todefnum('cPaid');
	todefnum('cTrial');
?><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Changing publication information"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php 
	$correct = 1;
	$updated = 0;
	query ("SELECT * FROM Publications WHERE Id = $Pub", 'q_pub');
	if ($NUM_ROWS) { 
		fetchRow($q_pub);
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_pub,'Name'); ?></TD>

</TR></TABLE>

<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Changing publication information"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php 
	$cName=trim($cName);
	$cSite=trim($cSite);
	$cUnitCost=trim($cUnitCost);
	$cCurrency=trim($cCurrency);

	if ($cName == "" || $cName== " ") {
		$correct=0;
?>		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<?php
	}
	if ($cDefaultAlias == "" || $cSite == " ") {
		$correct= 0;
?>		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Site').'</B>'); ?></LI>
<?php
	}
	if ($correct) {
		$sql = "UPDATE Publications SET Name = '$cName', IdDefaultAlias = '$cDefaultAlias', "
		     . "IdDefaultLanguage = $cLanguage, IdURLType = '$cURLType', PayTime = '$cPayTime', "
		     . "TimeUnit = '$cTimeUnit', PaidTime = '$cPaid', TrialTime = '$cTrial'";
		if ($cUnitCost != '') {
			$sql .= ", UnitCost = '$cUnitCost' ";
		}
		if ($cCurrency != '') {
			$sql .= ", Currency = '$cCurrency' ";
		}
		$sql .= " WHERE Id=$Pub";
		query($sql);
		$updated = ($AFFECTED_ROWS >= 0);
	}

	if ($updated) {
		$params = array($operation_attr=>$operation_modify, "IdPublication"=>"$Pub" );
		$msg = build_reset_cache_msg($cache_type_publications, $params);
		send_message("127.0.0.1", server_port(), $msg, $err_msg);
?>		<LI><?php  putGS('The publication $1 has been successfuly updated.', "<B>" 
		                 . encHTML(decS($cName)) . "</B>"); ?></LI>
		<?php  $logtext = getGS('Publication $1 changed', $cName); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=3, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
<?php
	} else {
		if ($correct != 0) { ?>			<LI><?php  putGS('The publication information could not be updated.'); ?></LI>
			<LI><?php  putGS('Please check if another publication with the same or the same site name does not already exist.'); ?></LI>
<?php  }
	}
?></BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
<?php  if ($correct && $updated) { ?>		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/pub/'">
<?php  } else { ?>
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/pub/edit.php?Pub=<?php  pencURL($Pub); ?>'">
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
