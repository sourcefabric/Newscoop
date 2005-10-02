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

    <TITLE><?php  putGS("Changing default subscription time"); ?></TITLE>
<?php  if ($access == 0) { ?>    <META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to change publication information." )); ?>">
<?php  } ?></HEAD>

<?php  if ($access) { ?> 
 

<BODY >

<?php
    todefnum('Pub');
    todefnum('cPub');
    todef('cCountryCode');
    todefnum('Language', 1);
    todefnum('cPaidTime');
    todefnum('cTrialTime');
?><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Changing default subscription time"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/pub/deftime.php?Pub=<?php  pencURL($Pub); ?>" class="breadcrumb" ><?php  putGS("Subscriptions");  ?></A></TD>
<td class="breadcrumb_separator">&nbsp;</td>
<TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php
    $created= 0;

    query ("SELECT * FROM Publications WHERE Id=$cPub", 'q_pub');

    if ($NUM_ROWS) {
    query ("SELECT * FROM Countries WHERE Code='$cCountryCode'", 'q_ctr');

    if ($NUM_ROWS) {
        fetchRow($q_pub);
        fetchRow($q_ctr);
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_pub,'Name'); ?></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Country"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_ctr,'Name'); ?></TD>

</TR></TABLE>

<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Changing default subscription time"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
    <TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php
    query ("UPDATE SubsDefTime SET TrialTime='$cTrialTime', PaidTime='$cPaidTime' WHERE CountryCode='$cCountryCode' AND IdPublication=$cPub");
    $created= ($AFFECTED_ROWS > 0);

    if ($created) { ?>        <LI><?php  putGS('The default subscription time for $1 has been successfuly updated.','<B>'.getHVar($q_pub,'Name').':'.getHVar($q_ctr,'Name').'</B>'); ?></LI>
<?php  $logtext = getGS('Default subscription time for $1 changed',getVar($q_pub,'Name').':'.$cCountryCode); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=6, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
<?php  } else { ?>        <LI><?php  putGS('The default subscription time could not be updated.'); ?></LI>
<?php  } ?>        </BLOCKQUOTE></TD>
	</TR>
    <TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
<?php  if ($created) { ?>        <INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/pub/deftime.php?Pub=<?php  pencURL($Pub); ?>&Language=<?php  pencURL($Language); ?>'">
<?php  } else { ?>
        <INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/pub/deftime.php?Pub=<?php  pencURL($Pub); ?>&Language=<?php  pencURL($Language); ?>'">
<?php  } ?>    	</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
<P>
<?php  } else { ?><BLOCKQUOTE>
    <LI><?php  putGS('No such country.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php  } else { ?><BLOCKQUOTE>
    <LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>
</BODY>
<?php  } ?>

</HTML>
