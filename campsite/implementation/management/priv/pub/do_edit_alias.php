<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
$globalfile=selectLanguageFile($Campsite['HTML_DIR'] . "/$ADMIN_DIR",'globals');
$localfile=selectLanguageFile("$ADMIN_DIR/pub","locals");
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

	<TITLE><?php  putGS("Editing alias"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to manage publications." )); ?>">
<?php  } ?></HEAD>

<?php  if ($access) { ?> 
 

<BODY >

<?php
	todefnum('cAlias');
	todefnum('cPub');
	todef('cName');
	$correct = 1;
	$updated = 0;
?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Editing alias"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php 
	query ("SELECT Name FROM Publications WHERE Id=$cPub", 'q_pub');
	if ($NUM_ROWS) {
		fetchRow($q_pub);
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_pub,'Name'); ?></TD>

</TR></TABLE>

<TABLE>
<TR>
	<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="edit.php?Pub=<?php  pencURL($cPub); ?>" ><IMG SRC="/admin/img/icon/back.png" BORDER="0"></A></TD><TD><A HREF="edit.php?Pub=<?php  pencURL($cPub); ?>" ><B><?php  putGS("Back to publication"); ?></B></A></TD></TR></TABLE></TD>
	<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="aliases.php?Pub=<?php  pencURL($cPub); ?>" ><IMG SRC="/admin/img/icon/back.png" BORDER="0"></A></TD><TD><A HREF="aliases.php?Pub=<?php  pencURL($cPub); ?>" ><B><?php  putGS("Back to aliases"); ?></B></A></TD></TR></TABLE></TD>
</TR>
</TABLE>

<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Editing alias"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php 
	$cName = trim($cName);
	if ($cName == "" || $cName == " ") {
		$correct= 0;
?>		<LI><?php  putGS('You must complete the $1 field.', '<B>Name</B>'); ?></LI>
<?php 
	}

	$aliases = 0;
	if ($correct) {
		$sql = "SELECT COUNT(*) AS alias_count FROM Aliases WHERE Name = '" . $cName . "' AND Id != " . $cAlias;
		query($sql, 'q_count');
		fetchRow($q_count);
		$aliases = getHVar($q_count, 'alias_count');
		if ($aliases == 0) {
			query ("UPDATE Aliases SET Name = '$cName' WHERE Id = " . $cAlias);
			$updated = ($AFFECTED_ROWS >= 0);
		}
	}

	if ($updated) {
		$params = array($operation_attr=>$operation_modify, "IdPublication"=>"$cPub" );
		$msg = build_reset_cache_msg($cache_type_publications, $params);
		send_message("127.0.0.1", server_port(), $msg, $err_msg);
?>		<LI><?php  putGS('The site alias for publication $1 has been modified to $2.', '<B>'.getHVar($q_pub,'Name').'</B>', '<B>'.$cName.'</B>'); ?></LI>
		<?php  $logtext = getGS('The site alias for publication $1 has been modified to $2.',getVar($q_pub,'Name'), $cName); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=153, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
<?php
	} else {
		if ($correct != 0) {
			if ($aliases > 0) {
				echo "<LI>"; putGS('Another alias with the same name exists already.'); echo "</LI>\n";
			}
			echo "<LI>";
			putGS('The site alias $1 could not be modified.', '<B>'.$cName.'</B>');
			echo "</LI>\n";
		}
	}
?>	</BLOCKQUOTE></TD>
	</TR>
<?php  if ($correct && $updated) { ?>	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="new" VALUE="<?php  putGS('Add another'); ?>" ONCLICK="location.href='/admin/pub/add_alias.php?Pub=<?php  pencURL($cPub); ?>'">
		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/pub/aliases.php?Pub=<?php  pencURL($cPub); ?>'">
		</DIV>
		</TD>
	</TR>
<?php  } else { ?>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/pub/aliases.php?Pub=<?php  pencURL($cPub); ?>'">
		</DIV>
		</TD>
	</TR>
<?php  } ?></TABLE></CENTER>
<P>
<?php  } else { ?><BLOCKQUOTE>
        <LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>
</BODY>
<?php  } ?>

</HTML>
