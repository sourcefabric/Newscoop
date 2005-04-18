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
    
 

	<?php  if($xpermrows) {
		$xaccess=(getvar($XPerm,'Publish') == "Y");
		if($xaccess =='') $xaccess = 0;
	}
	else $xaccess = 0;
	?>
	


<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">

	<TITLE><?php  putGS("Changing issue status"); ?></TITLE>
<?php  if ($access == 0 || $xaccess == 0) { ?>	
<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <font color="red"><?php  putGS("Access denied"); ?> </font></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><font color=red><li><?php  putGS("You do not have the right to change issues." ); ?></li></font></BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<A HREF="/admin/pub/issues/?Pub=<?php  p($Pub); ?>&Language=<?php  p($Language); ?> ?>"><IMG SRC="/admin/img/button/ok.gif" BORDER="0" ALT="OK"></A>
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
<P>

<?php  } ?></HEAD>

<?php  if ($access && $xaccess) { ?> 
 

<BODY >

<?php 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Changing issue status"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/pub/issues/?Pub=<?php  pencURL($Pub); ?>" class="breadcrumb" ><?php  putGS("Issues");  ?></A></TD>
<td class="breadcrumb_separator">&nbsp;</td>
<TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>
<?php 
    query ("SELECT Number, Name, Published FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
	query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	    if ($NUM_ROWS) {
		query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		fetchRow($q_iss);
		fetchRow($q_pub);
		fetchRow($q_lang);
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_pub,'Name'); ?></TD>

</TR></TABLE>

<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Changing issue status"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
<?php 

	$AFFECTED_ROWS= 0;
	query ("UPDATE Issues SET PublicationDate=IF(Published = 'N', NOW(), PublicationDate), Published=IF(Published = 'N', 'Y', 'N') WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language");
	$changed_status = $AFFECTED_ROWS > 0;
	if ($changed_status) {
		if (getVar($q_iss,'Published') == "Y") {
			$t2=getGS('Published');
			$t3=getGS('Not published');
		}
		else {
			$t2=getGS('Not published');
			$t3=getGS('Published');
		} ?>	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  putGS('Status of the issue $1 has been changed from $2 to $3','<B>'.getHVar($q_iss,'Number').'. '.getHVar($q_iss,'Name').' ('.getHVar($q_lang,'Name').')</B>',"<B>$t2</B>","<B>$t3</B>"); ?></LI></BLOCKQUOTE></TD>
	</TR>

<?php  $logtext = getGS('Issue $1 Published: $2  changed status',getVar($q_iss,'Number').'. '.getVar($q_iss,'Name').' ('.getVar($q_lang,'Name').')',getVar($q_iss,'Published')); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=14, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
<?php  } else { ?>	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  putGS('Status of the issue $1 could not be changed.','<B>'.getVar($q_iss,'Number').'. '.getVar($q_iss,'Name').' ('.getVar($q_lang,'Name').')</B>'); ?></LI></BLOCKQUOTE></TD>
	</TR>
<?php  } ?>	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
<?php  
    if ($changed_status) { ?>		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/pub/issues/?Pub=<?php  pencURL($Pub); ?>'">
<?php  } else { ?>		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/pub/issues/status.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Language=<?php  pencURL($Language); ?>'">
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
<?php CampsiteInterface::CopyrightNotice(); ?>
</BODY>
<?php  } ?>

</HTML>

