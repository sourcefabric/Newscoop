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

 <TITLE><?php  putGS("Configure section"); ?></TITLE>
<?php  if ($access == 0) { ?> <META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to change section details" )); ?>">
<?php  } ?>
</HEAD>

<?php  if ($access) { ?> 
 

<BODY >
<?php
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Language');
?><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Configure section"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" class="breadcrumb" ><?php  putGS("Sections");  ?></A></TD>
<td class="breadcrumb_separator">&nbsp;</td>
<TD><A HREF="/admin/pub/issues/?Pub=<?php p($Pub); ?>" class="breadcrumb" ><?php  putGS("Issues");  ?></A></TD>
<td class="breadcrumb_separator">&nbsp;</td>
<TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php
    query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
    if ($NUM_ROWS) {
 query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
 if ($NUM_ROWS) {
  query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
  if ($NUM_ROWS) {
   query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_language');
   fetchRow($q_sect);
   fetchRow($q_iss);
   fetchRow($q_pub);
   fetchRow($q_language);
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_pub,'Name'); ?></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Issue"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_language,'Name'); ?>)</TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Section"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></TD>

</TR></TABLE>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php" >
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("Configure section"); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
 <TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
		<TD>
  <INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="64" value="<?php  pgetHVar($q_sect,'Name'); ?>">
 	</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Section Template"); ?>:</TD>
		<TD>
		<SELECT NAME="cSectionTplId" class="input_select">
			<OPTION VALUE="0">---</OPTION>
<?php 
	query ("SELECT Id, Name FROM Templates ORDER BY Level ASC, Name ASC", 'q_sect_tpl');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_sect_tpl);
		pcomboVar(getVar($q_sect_tpl,'Id'),getVar($q_sect,'SectionTplId'),getVar($q_sect_tpl,'Name'));
	}
?>	    </SELECT>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Article Template"); ?>:</TD>
		<TD>
		<SELECT NAME="cArticleTplId" class="input_select">
			<OPTION VALUE="0">---</OPTION>
<?php 
	query ("SELECT Id, Name FROM Templates ORDER BY Level ASC, Name ASC", 'q_art_tpl');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_art_tpl);
		pcomboVar(getVar($q_art_tpl,'Id'),getVar($q_sect,'ArticleTplId'),getVar($q_art_tpl,'Name'));
	}
?>	    </SELECT>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Short Name"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cShortName" SIZE="32" MAXLENGTH="32" value="<?php  pgetHVar($q_sect,'ShortName'); ?>">
		</TD>
	</TR>
 <TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Subscriptions"); ?>:</TD>
		<TD>
  <SELECT NAME="cSubs" class="input_select">
   <OPTION VALUE="n"> --- </OPTION>
   <OPTION VALUE="a"><?php  putGS("Add section to all subscriptions."); ?></OPTION>
   <OPTION VALUE="d"><?php  putGS("Delete section from all subscriptions."); ?></OPTION>
  </SELECT>
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
  <INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
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
<?php  } else { ?><BLOCKQUOTE>
 <LI><?php  putGS('No such section.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php CampsiteInterface::CopyrightNotice(); ?>
</BODY>
<?php  } ?>

</HTML>

