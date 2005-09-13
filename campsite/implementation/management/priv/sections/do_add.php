<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
$globalfile=selectLanguageFile($Campsite['HTML_DIR'] . "/$ADMIN_DIR",'globals');
$localfile=selectLanguageFile("$ADMIN_DIR/sections","locals");
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
	query ("SELECT ManageSection FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'Perm');
	 if ($NUM_ROWS) {
		fetchRow($Perm);
		$access = (getVar($Perm,'ManageSection') == "Y");
	}
	else $access = 0;
    } ?>
    
 

<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">

	<TITLE><?php  putGS("Adding new section"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to add sections." )); ?>">
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
		    <?php  putGS("Adding new section"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" class="breadcrumb" ><?php  putGS("Sections");  ?></A></TD>
<td class="breadcrumb_separator">&nbsp;</td>
<TD><A HREF="/admin/issues/?Pub=<?php  p($Pub); ?>" class="breadcrumb" ><?php  putGS("Issues");  ?></A></TD>
<td class="breadcrumb_separator">&nbsp;</td>
<TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php 
    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
	    fetchRow($q_iss);
	    fetchRow($q_pub);
	    fetchRow($q_lang);
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_pub,'Name'); ?></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Issue"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</TD>

</TR></TABLE>

<?php 
	todef('cName');
	todefnum('cNumber');
	todef('cSubs');
	todef('cShortName');

	$correct= 1;
	$created= 0;
?><P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Adding new section"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php 
	if ($cName == "") {
		$correct= 0; ?>		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<?php  }
	if ($cNumber == "") {
		$correct= 0;
		$cNumber= ($cNumber + 0); ?>		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Number').'</B>'); ?></LI>
<?php  
	}
	if ($cShortName == "" || $cShortName == " ") {
		$correct = 0;
		echo "<LI>" . getGS('You must complete the $1 field.','<B>'.getGS('Short Name').'</B>') . "</LI>\n";
	}
	$ok = valid_short_name($cShortName);
	if ($ok == 0) {
		$correct= 0;
		echo "<LI>" . getGS('The $1 field may only contain letters, digits and underscore (_) character.', '</B>' . getGS('Short Name') . '</B>') . "</LI>\n";
	}
	if ($correct) {
		$sql = "INSERT INTO Sections SET Name='$cName', IdPublication=$Pub, NrIssue=$Issue, IdLanguage=$Language, Number=$cNumber, ShortName='$cShortName'";
		query($sql);
		$created = ($AFFECTED_ROWS >= 0);
	}
	if ($created) {
		## added by sebastian
		if (function_exists ("incModFile"))
			incModFile ();
?>		<LI><?php  putGS('The section $1 has been successfuly added.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
	<?php 	if ($cSubs != "") {
			$add_subs_res = add_subs_section($Pub, $cNumber);
			if ($add_subs_res == -1) { ?>
				<LI><?php  putGS('Error updating subscriptions.'); ?></LI>
		<?php 	} else { ?>
				<LI><?php  putGS('A total of $1 subscriptions were updated.','<B>'.encHTML(decS($add_subs_res)).'</B>'); ?></LI>
	<?php 		}
		}
	?>
<?php  $logtext = getGS('Section $1 added to issue $2. $3 ($4) of $5',$cName,getHVar($q_iss,'Number'),getHVar($q_iss,'Name'),getHVar($q_lang,'Name'),getHVar($q_pub,'Name')); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=21, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
<?php  } else {
    
    if ($correct != 0) { ?>		<LI><?php  putGS('The section could not be added.'); ?></LI><LI><?php  putGS('Please check if another section with the same number does not already exist.'); ?></LI>
<?php  }
}
?>		</BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
<?php  if ($correct && $created) { ?>		<INPUT TYPE="button" class="button" NAME="Add another" VALUE="<?php  putGS('Add another'); ?>" ONCLICK="location.href='/admin/sections/add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>'">
		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>'">
<?php  } else { ?>
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/sections/add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>'">
<?php  } ?>		</DIV>
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
