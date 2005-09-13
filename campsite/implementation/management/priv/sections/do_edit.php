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

	<TITLE><?php  putGS("Updating section name"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to modify sections." )); ?>">
<?php  } ?></HEAD>

<?php  if ($access) { ?> 
 

<BODY >

<?php 
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('Language');
	todefnum('cSectionTplId');
	todefnum('cArticleTplId');
	todef('cShortName');
	todef('cSubs');
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Updating section name"); ?>
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
    $correct= 1;
    $created= 0;
?><P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Updating section name"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php 
    if ($cName == "") { ?><?php  $correct= 0; ?>		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<?php  }
	if ($cShortName == "" || $cShortName == " ") {
		$correct = 0;
		echo "<LI>" . getGS('You must complete the $1 field.','<B>'.getGS('URL Name').'</B>') . "</LI>\n";
	}
	$ok = valid_short_name($cShortName);
	if ($ok == 0) {
		$correct= 0;
		echo "<LI>" . getGS('The $1 field may only contain letters, digits and underscore (_) character.', '</B>' . getGS('URL Name') . '</B>') . "</LI>\n";
	}
	if ($correct) {
		$sql = "UPDATE Sections SET Name='$cName'";
		$sql .= ", SectionTplId = " . ($cSectionTplId > 0 ? $cSectionTplId : "NULL");
		$sql .= ", ArticleTplId = " . ($cArticleTplId > 0 ? $cArticleTplId : "NULL");
		$sql .= ", ShortName = '" . $cShortName . "'";
		$sql .= " WHERE IdPublication=$Pub AND NrIssue=$Issue AND Number=$Section AND IdLanguage=$Language";
		query($sql);
		$created= ($AFFECTED_ROWS >= 0);

		## added by sebastian
		if (function_exists ("incModFile"))
			incModFile ();

		if ($cSubs == "a") {
			$add_subs_res = add_subs_section($Pub, $Section);
			if ($add_subs_res == -1) { ?>
				<LI><?php  putGS('Error updating subscriptions.'); ?></LI>
		<?php 	} else { ?>
				<LI><?php  putGS('A total of $1 subscriptions were updated.','<B>'.encHTML(decS($add_subs_res)).'</B>'); ?></LI>
	<?php 		}
		}
		if ($cSubs == "d") {
			$del_subs_res = del_subs_section($Pub, $Section);
			if ($del_subs_res == -1) { ?>
				<LI><?php  putGS('Error updating subscriptions.'); ?></LI>
		<?php 	} else { ?>
				<LI><?php  putGS('A total of $1 subscriptions were updated.','<B>'.encHTML(decS($del_subs_res)).'</B>'); ?></LI>
	<?php 		}
		}
    }

    if ($created) { ?>		<LI><?php  putGS('The section $1 has been successfuly modified.', '<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
<?php  $logtext = getGS('Section $1 updated to issue $2. $3 ($4) of $5',$cName,getHVar($q_iss,'Number'),getHVar($q_iss,'Name'),getHVar($q_lang,'Name'),getHVar($q_pub,'Name') ); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=21, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
<?php  } else {
    if ($correct != 0) { ?>		<LI><?php  putGS('The section could not be changed.'); ?></LI>
<!--LI><?php  putGS('Please check if another section with the same number does not already exist.'); ?></LI-->
<?php  }
}
?>		</BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
<?php 
    if ($correct && $created) { ?>		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>'">
<?php  } else { ?>
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/sections/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>'">
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

