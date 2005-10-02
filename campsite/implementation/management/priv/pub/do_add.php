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

	<TITLE><?php  putGS("Adding new publication"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to add publications." )); ?>">
<?php  } ?></HEAD>

<?php  if ($access) { ?> 
 

<BODY >

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Adding new publication"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php 
	todef('cName');
	todef('cSite');
	todefnum('cLanguage');
	todefnum('cPayTime');
	todef('cTimeUnit');
	todef('cUnitCost');
	todef('cCurrency');
	todefnum('cPaid');
	todefnum('cTrial');
	todefnum('cURLType');

	$correct = 1;
	$created = 0;
?><P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Adding new publication"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php 
	$cName = trim($cName);
	$cSite = trim($cSite);

	if ($cName == "" || $cName == " ") {
		$correct= 0;
?>		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<?php
	}

	if ($cSite == "" || $cSite == " ") {
		$correct = 0;
?>		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Site').'</B>'); ?></LI>
<?php
	}

	if ($correct) {
		$sql = "SELECT COUNT(*) AS alias_count FROM Aliases WHERE Name = '" . $cSite . "'";
		query($sql, 'q_alias_count');
		fetchRow($q_alias_count);
		$aliases_nr = getVar($q_alias_count, 'alias_count');
		if ($aliases_nr == 0) {
			$sql = "INSERT INTO Aliases (Name) VALUES('" . $cSite . "')";
			query($sql);
			$cDefaultAlias = mysql_insert_id();
		}
		if ($aliases_nr == 0 && $cDefaultAlias > 0) {
			$AFFECTED_ROWS = 0;
			$sql = "INSERT INTO Publications SET Name='$cName', IdDefaultAlias='$cDefaultAlias', IdDefaultLanguage=$cLanguage, IdURLType=$cURLType, PayTime='$cPayTime', TimeUnit='$cTimeUnit', UnitCost='$cUnitCost', Currency='$cCurrency', PaidTime='$cPaid', TrialTime='$cTrial'";
			query($sql);
			$created = ($AFFECTED_ROWS > 0);
			if ($created) {
				$pub_id = mysql_insert_id();
				$sql = "UPDATE Aliases SET IdPublication = " . $pub_id . " WHERE Id = " . $cDefaultAlias;
				query($sql);
				$params = array($operation_attr=>$operation_create, "IdPublication"=>"$pub_id");
				$msg = build_reset_cache_msg($cache_type_publications, $params);
				send_message("127.0.0.1", server_port(), $msg, $err_msg);
			} else {
				$sql = "DELETE FROM Aliases WHERE Id = " . $cDefaultAlias;
				query($sql);
			}
		}
	}

	if ($created) {
?>		<LI><?php  putGS('The publication $1 has been successfuly added.', "<B>".encHTML(decS($cName))."</B>"); ?></LI>
		<?php  $logtext = getGS('Publication $1 added',$cName); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=1, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
<?php 
	} else {
		if ($correct != 0) { ?>			<LI><?php  putGS('The publication could not be added.'); ?></LI><LI><?php  putGS('Please check if another publication with the same or the same site name does not already exist.'); ?></LI>
<?php
		}
	}
?>	</BLOCKQUOTE></TD>
	</TR>
<?php  if ($correct && $created) { ?>	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="another" VALUE="<?php  putGS('Add another'); ?>" ONCLICK="location.href='/admin/pub/add.php'">
		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/pub/'">
		</DIV>
		</TD>
	</TR>
<?php  } else { ?>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/pub/add.php'">
		</DIV>
		</TD>
	</TR>
<?php  } ?></TABLE></CENTER>
<P>

<?php camp_html_copyright_notice(); ?>
</BODY>
<?php  } ?>

</HTML>
