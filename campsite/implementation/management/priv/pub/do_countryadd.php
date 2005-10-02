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

	<TITLE><?php  putGS("Adding new country default subscription time"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to manage publications." )); ?>">
<?php  } ?></HEAD>

<?php  if ($access) { ?> 
 

<BODY >

<?php 
    todefnum('cPub');
    todef('cCountryCode');
    todefnum('cTrialTime');
    todefnum('cPaidTime');
    todefnum('Language', 1);
    $correct= 1;
    $created= 0;
    
?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Adding new country default subscription time"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/pub/deftime.php?Pub=<?php  pencURL($cPub); ?>" class="breadcrumb" ><?php  putGS("Subscriptions");  ?></A></TD>
<td class="breadcrumb_separator">&nbsp;</td>
<TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php 
    query ("SELECT Name FROM Publications WHERE Id=$cPub", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);    
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_pub,'Name'); ?></TD>

</TR></TABLE>

<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Adding new country default subscription time"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php 
    $cCountryCode=trim($cCountryCode);
    if ($cCountryCode == "" || $cCountryCode == " ") {
	$correct= 0; ?>		<LI><?php  putGS('You must select a country.'); ?></LI>
<?php 
    }
    
    if ($correct) {
	query ("INSERT IGNORE INTO SubsDefTime SET CountryCode='$cCountryCode', IdPublication='$cPub', TrialTime='$cTrialTime', PaidTime='$cPaidTime'");
	$created= ($AFFECTED_ROWS > 0);
    }

    if ($created) { ?>		<LI><?php  putGS('The default subscription time for $1 has been added.','<B>'.getHVar($q_pub,'Name').':'.encHTML($cCountryCode).'</B>'); ?></LI>
<?php  $logtext = getGS('The default subscription time for $1 has been added.',getVar($q_pub,'Name').':'.$cCountryCode); query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=4, User='".getVar($Usr,'UName')."', Text='$logtext'"); ?>
<?php  } else {
    if ($correct != 0) { ?>		<LI><?php  putGS('The default subscription time for country $1 could not be added.',getHVar($q_pub,'Name').':'.encHTML($cCountryCode)); ?></LI><LI><?php  putGS('Please check if another entry with the same country code exists already.'); ?></LI>
<?php  
    }
    }
?>		</BLOCKQUOTE></TD>
	</TR>
<?php  if ($correct && $created) { ?>	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="new" VALUE="<?php  putGS('Add another'); ?>" ONCLICK="location.href='/admin/pub/countryadd.php?Pub=<?php  pencURL($cPub); ?>&Language=<?php  pencURL($Language); ?>'">
		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/pub/deftime.php?Pub=<?php  pencURL($cPub); ?>'">
		</DIV>
		</TD>
	</TR>
<?php  } else { ?>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/pub/countryadd.php?Pub=<?php  pencURL($cPub); ?>&Language=<?php  pencURL($Language); ?>'">
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

