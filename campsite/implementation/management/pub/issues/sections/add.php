<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
$globalfile=selectLanguageFile($Campsite['HTML_DIR'] . "/$ADMIN_DIR",'globals');
$localfile=selectLanguageFile("$ADMIN_DIR/pub/issues/sections","locals");
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
	query ("SELECT ManageSection FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'Perm');
	 if ($NUM_ROWS) {
		fetchRow($Perm);
		$access = (getVar($Perm,'ManageSection') == "Y");
	}
	else $access = 0;
    } ?>
    
 

<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">

	<TITLE><?php  putGS("Add new section"); ?></TITLE>
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
		    <?php  putGS("Add new section"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" class="breadcrumb" ><?php  putGS("Sections");  ?></A></TD>
<td class="breadcrumb_separator">&nbsp;</td>
<TD><A HREF="/admin/pub/issues/?Pub=<?php  p($Pub); ?>" class="breadcrumb" ><?php  putGS("Issues");  ?></A></TD>
<td class="breadcrumb_separator">&nbsp;</td>
<TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php 
    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_language');

	    fetchRow($q_iss);
	    fetchRow($q_pub);
	    fetchRow($q_language);
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_pub,'Name'); ?></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Issue"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_language,'Name'); ?>)</TD>

</TR></TABLE>

<?php 
    query ("SELECT MAX(Number) + 1 FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language", 'q_nr');
    fetchRowNum($q_nr);
    
    if (getNumVar($q_nr,0) == "")
	$nr= 1;
    else
	$nr=getNumVar($q_nr,0);
?><P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php" >
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("Add new section"); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="64">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Number"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cNumber" VALUE="<?php  p($nr); ?>" SIZE="5" MAXLENGTH="5">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Short Name"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cShortName" SIZE="32" MAXLENGTH="32">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Subscriptions"); ?>:</TD>
		<TD>
		<INPUT TYPE="checkbox" NAME="cSubs" class="input_checkbox"> <?php  putGS("Add section to all subscriptions."); ?>
		</TD>
	</TR>

<?php 
	## added by sebastian
	if (function_exists ("incModFile"))
		incModFile ();
?>

	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
		<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/admin/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
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
