<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
$globalfile=selectLanguageFile($Campsite['HTML_DIR'] . "/$ADMIN_DIR",'globals');
$localfile=selectLanguageFile("$ADMIN_DIR/pub/issues","locals");
@include_once($globalfile);
@include_once($localfile);
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
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

	<TITLE><?php  putGS("Updating issue"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to add issues." )); ?>">
<?php  } ?></HEAD>

<?php  if ($access) { ?> 
 

<BODY >

<?php 
	todef('cName');
	todefnum('cLang');
	todefnum('cPublicationDate');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Language');
	todefnum('cIssueTplId');
	todefnum('cSectionTplId');
	todefnum('cArticleTplId');
	todef('cShortName');
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Changing issue's details"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/pub/issues/?Pub=<?php  pencURL($cPub); ?>" class="breadcrumb" ><?php  putGS("Issues");  ?></A></TD>
<td class="breadcrumb_separator">&nbsp;</td>
<TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php 
    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'publ');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT Id, Name FROM Languages WHERE Id=$Language", 'q_lang');
	    fetchRow($publ);
	    fetchRow($q_pub);
	    fetchRow($q_lang);
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_pub,'Name'); ?></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Issue"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($publ,'Number'); ?>. <?php  pgetHVar($publ,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</TD>

</TR></TABLE>   

<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Changing issue's details"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>

<?php 
	$correct = 1;
	$created = 0;
	$cName = trim($cName);
	if ($cLang == 0) {
		$correct = 0;
?>		<LI><?php  putGS('You must select a language.'); ?></LI>
<?php
	}
	if ($cName == "" || $cName == " ") {
		$correct = 0;
		echo "<LI>" . getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>') . "</LI>\n";
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
		$sql = "UPDATE Issues SET Name = '$cName', IdLanguage = $cLang";
		if (getVar($publ, 'Published') == 'Y')
			$sql .= ", PublicationDate = '$cPublicationDate'";
		$sql .= ", IssueTplId = " . ($cIssueTplId > 0 ? $cIssueTplId : "NULL");
		$sql .= ", SectionTplId = " . ($cSectionTplId > 0 ? $cSectionTplId : "NULL");
		$sql .= ", ArticleTplId = " . ($cArticleTplId > 0 ? $cArticleTplId : "NULL");
		$sql .= ", ShortName = '" . $cShortName . "'";
		$sql .= " WHERE IdPublication = $Pub AND Number = $Issue AND IdLanguage = $cLang";
		query($sql);
		$created = ($AFFECTED_ROWS >= 0);
	}
	if ($created) {
?>		<LI><?php  putGS('The issue $1 has been successfuly changed.', '<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
		<?php  $logtext = getGS('Issue $1 updated in publication $2',$cName,getVar($publ,'Name')); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=11, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
<?php
	} else {
		if ($correct != 0) { ?>			<LI><?php  putGS('The issue could not be changed.'); ?></LI>
<?php
		}
	}
?>	</BLOCKQUOTE></TD>
	</TR>
<?php  if ($correct && $created) { ?>	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/pub/issues/?Pub=<?php  pencURL($Pub); ?>'">
		</DIV>
		</TD>
	</TR>
<?php  } else { ?>	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/pub/issues/edit.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Language=<?php  pencURL($Language); ?>'">
		</DIV>
		</TD>
	</TR>
<?php  } ?></TABLE></CENTER>
<P>

<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php  } else { ?><BLOCKQUOTE>
        <LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php CampsiteInterface::CopyrightNotice(); ?>
</BODY>
<?php  } ?>

</HTML>
