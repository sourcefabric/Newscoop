<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
$globalfile=selectLanguageFile('globals');
$localfile=selectLanguageFile("issues");
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

	<TITLE><?php  putGS("Adding new issue"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to add issues." )); ?>">
<?php  } ?></HEAD>

<?php  if ($access) { ?> 
 

<BODY >

<?php 
	todef('cName');
	todefnum('cNumber');
	todefnum('cLang');
	todefnum('cPub');
	todef('cShortName');
?><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Adding new issue"); ?>
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
			<B> <?php  putGS("Adding new issue"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php 
	$correct = 1;
	$created = 0;
	$cName = trim($cName);
	$cNumber = trim($cNumber);
	if ($cLang == 0) {
		$correct = 0;
		echo "<LI>" . getGS('You must select a language.') . "</LI>\n";
	}
	if ($cName == "" || $cName == " ") {
		$correct = 0;
		echo "<LI>" . getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>') . "</LI>\n";
	}
	if ($cShortName == "" || $cShortName == " ") {
		$correct = 0;
		echo "<LI>" . getGS('You must complete the $1 field.','<B>'.getGS('URL Name').'</B>') . "</LI>\n";
	}
	$ok = camp_is_valid_url_name($cShortName);
	if ($ok == 0) {
		$correct = 0;
		echo "<LI>" . getGS('The $1 field may only contain letters, digits and underscore (_) character.', '</B>' . getGS('URL Name') . '</B>') . "</LI>\n";
	}
	if ($cNumber == "" || $cNumber == " ") {
		$correct = 0; ?>		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Number').'</B>'); ?></LI>
<?php
	}
	if ($correct) {
		$sql = "INSERT INTO Issues SET Name='$cName', IdPublication=$cPub, IdLanguage=$cLang, Number=$cNumber, ShortName='".$cShortName."'";
		query($sql);
		$created = ($AFFECTED_ROWS > 0);
	}
	if ($created) {
?>		<LI><?php  putGS('The issue $1 has been successfuly added.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
		<?php  $logtext = getGS('Issue $1 added in publication $2',$cName,getVar($publ,'Name')); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=11, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
<?php  } else {
		if ($correct) { ?>			<LI><?php  putGS('The issue could not be added.'); ?></LI><LI><?php  putGS('Please check if another issue with the same number/language does not already exist.'); ?></LI>
<?php  }
	}
?>		</BLOCKQUOTE></TD>
	</TR>
<?php  if ($correct && $created) { ?>	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="Another" VALUE="<?php  putGS('Add another'); ?>" ONCLICK="location.href='/admin/issues/add_new.php?Pub=<?php  pencURL($cPub); ?>'">
		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/issues/?Pub=<?php  pencURL($cPub); ?>'">
		</DIV>
		</TD>
	</TR>
<?php  } else { ?>	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/issues/add_new.php?Pub=<?php  pencURL($cPub); ?>'">
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
