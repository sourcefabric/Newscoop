<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
$globalfile=selectLanguageFile($Campsite['HTML_DIR'] . "/$ADMIN_DIR",'globals');
$localfile=selectLanguageFile("$ADMIN_DIR/issues","locals");
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

	<TITLE><?php  putGS("Add new issue"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to add issues." )); ?>">
<?php  } ?></HEAD>

<?php  if ($access) { ?> 
 

<BODY >

<?php 
    todefnum('Pub');
?><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Add new issue"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/issues/?Pub=<?php  pencURL($Pub); ?>" class="breadcrumb" ><?php  putGS("Issues");  ?></A></TD>
<td class="breadcrumb_separator">&nbsp;</td>
<TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php 
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'publ');
    if ($NUM_ROWS) { 
	fetchRow($publ);
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  getHVar($publ,'Name'); ?></TD>

</TR></TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" ALIGN="CENTER" class="table_input">
	<TR>
		<TD WIDTH="1%" VALIGN="TOP"><A HREF="add_prev.php?Pub=<?php  pencURL($Pub); ?>"><IMG SRC="/admin/img/tol.gif" BORDER="0"></A></TD><TD WIDTH="99%"><B><A HREF="add_prev.php?Pub=<?php  pencURL($Pub); ?>"><?php  putGS('Use the structure of the previous issue'); ?></A></B></TD>
	</TR>
	<TR>
		<TD></TD><TD VALIGN="TOP">
			<LI><?php  putGS('Copy the entire structure in all languages from the previous issue except for content.'); ?><LI><?php  putGS('You may modify it later if you wish.'); ?></LI>
		</TD>
	<TR>
	<TR>
		<TD WIDTH="1%" VALIGN="TOP"><A HREF="add_new.php?Pub=<?php  pencURL($Pub); ?>"><IMG SRC="/admin/img/tol.gif" BORDER="0"></A></TD><TD WIDTH="99%"><B><A HREF="add_new.php?Pub=<?php  pencURL($Pub); ?>"><?php  putGS('Create a new structure'); ?></A></B></TD>
	</TR>
	<TR>
		<TD></TD><TD VALIGN="TOP">
			<LI><?php  putGS('Create a complete new structure.'); ?><LI><?php  putGS('You must define an issue type for each language and then sections for them.'); ?></LI>
		</TD>
	<TR>
</TABLE>
<P>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>
</BODY>
<?php  } ?>

</HTML>
