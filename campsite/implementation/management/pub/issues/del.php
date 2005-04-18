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
	query ("SELECT DeleteIssue FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'Perm');
	 if ($NUM_ROWS) {
		fetchRow($Perm);
		$access = (getVar($Perm,'DeleteIssue') == "Y");
	}
	else $access = 0;
    } ?>
    
 

<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">

	<TITLE><?php  putGS("Delete issue"); ?></TITLE>
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
		    <?php  putGS("Delete issue"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/pub/issues/?Pub=<?php  pencURL($Pub); ?>" class="breadcrumb" ><?php  putGS("Issues");  ?></A></TD>
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
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_pub,'Name'); ?></TD>

</TR></TABLE>

<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Delete issue"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  putGS('Are you sure you want to delete the issue $1?','<B>'.getHVar($q_iss,'Name').'</B>'); ?></LI></BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  pencHTML($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  pencHTML($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  pencHTML($Language); ?>">
		<INPUT TYPE="submit" class="button" NAME="Yes" VALUE="<?php  putGS('Yes'); ?>">
		<INPUT TYPE="button" class="button" NAME="No" VALUE="<?php  putGS('No'); ?>" ONCLICK="location.href='/admin/pub/issues/?Pub=<?php  pencURL($Pub); ?>'">
		</FORM>
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
<?php CampsiteInterface::CopyrightNotice(); ?>
</BODY>
<?php  } ?>

</HTML>

