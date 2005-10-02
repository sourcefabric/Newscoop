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
	todef('TOL_Language');
    todefnum('Pub');
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'q_lang');
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

	<TITLE><?php  putGS("Configure publication"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to edit publication information." )); ?>">
<?php  }
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'q_lang');

    query ("SELECT Unit, Name FROM TimeUnits WHERE 1=0", 'q_unit');
    query("SELECT  Id as IdLang FROM Languages WHERE code='$TOL_Language'", 'q_def_lang');
	if($NUM_ROWS == 0){
		query("SELECT IdDefaultLanguage as IdLang  FROM Publications WHERE Id=$Pub", 'q_def_lang');
	}
	fetchRow($q_def_lang);
	$IdLang = getVar($q_def_lang,'IdLang');
?></HEAD>

<?php  if ($access) { ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Configure publication"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>
<?php
    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_pub,'Name'); ?></TD>

</TR></TABLE>
<?php
	$sql = "SELECT Unit, Name FROM TimeUnits WHERE (IdLanguage=$IdLang or IdLanguage = 1) and Unit='".getHVar($q_pub,'TimeUnit')."' order by IdLanguage desc";
	query($sql, 'q_tunit');
	fetchRow($q_tunit); $tunit =getVar($q_tunit,'Name');
?><P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php"  >
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("Configure publication"); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<tr><td colspan=2><b><?php putGS("General attributes"); ?></b></td></tr>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" VALUE="<?php  pgetHVar($q_pub,'Name'); ?>" SIZE="32" MAXLENGTH="255">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Default Site Alias"); ?>:</TD>
		<TD>
		<SELECT NAME="cDefaultAlias" class="input_select">
<?php
	$sql = "SELECT * FROM Aliases WHERE IdPublication = " . $Pub;
	query ($sql, 'q_alias');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_alias);
		pcomboVar(getVar($q_alias,'Id'),getVar($q_pub,'IdDefaultAlias'),getVar($q_alias,'Name'));
	}
?>
	    </SELECT>&nbsp;
	<a href="/admin/pub/aliases.php?Pub=<?php echo $Pub ?>"><?php putGS("Edit aliases"); ?></a>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Default language"); ?>:</TD>
		<TD>
		<SELECT NAME="cLanguage" class="input_select">
<?php 
	query ("SELECT Id, OrigName FROM Languages", 'q_lang');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_lang);
		pcomboVar(getVar($q_lang,'Id'),getVar($q_pub,'IdDefaultLanguage'),getVar($q_lang,'OrigName'));
	}
?>	    </SELECT>&nbsp;
	<a href="/admin/languages/"><?php putGS("Edit languages"); ?></a>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("URL Type"); ?>:</TD>
		<TD>
		<SELECT NAME="cURLType" class="input_select">
<?php
	$sql = "SELECT * FROM URLTypes";
	query ($sql, 'q_urltype');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_urltype);
		pcomboVar(getVar($q_urltype,'Id'),getVar($q_pub,'IdURLType'),getVar($q_urltype,'Name'));
	}
?>	    </SELECT>
		</TD>
	</TR>

	<tr><td colspan=2><HR NOSHADE SIZE="1" COLOR="BLACK"></td></tr>
	<tr><td colspan=2><b><?php putGS("Subscriptions defaults"); ?></b></td></tr>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Pay Period"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cPayTime" VALUE="<?php  pgetHVar($q_pub,'PayTime'); ?>" SIZE="5" MAXLENGTH="5"> <?php  p($tunit); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Time Unit"); ?>:</TD>
		<TD>
	    <SELECT NAME="cTimeUnit" class="input_select">
<?php 
	$q = "SELECT t.Unit, t.Name FROM TimeUnits as t, Languages as l WHERE t.IdLanguage = l.Id and l.Code = '" . $TOL_Language . "' order by t.Unit asc";
	query($q, 'q_unit');
	$nr = $NUM_ROWS;
	if ($nr == 0) {
		$q = "SELECT t.Unit, t.Name FROM TimeUnits as t, Languages as l WHERE t.IdLanguage = l.Id and l.Code = 'en' order by t.Unit asc";
		query($q, 'q_unit');
		$nr = $NUM_ROWS;
	}
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_unit);
		pcomboVar(getVar($q_unit,'Unit'),getVar($q_pub,'TimeUnit'),getVar($q_unit,'Name'));
	}
?>	    </SELECT>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Unit Cost"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cUnitCost" VALUE="<?php  pgetHVar($q_pub,'UnitCost'); ?>" SIZE="10" MAXLENGTH="10">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Currency"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cCurrency" VALUE="<?php  pgetHVar($q_pub,'Currency'); ?>" SIZE="10" MAXLENGTH="10">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Paid Period"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cPaid" VALUE="<?php  pgetHVar($q_pub,'PaidTime'); ?>" SIZE="10" MAXLENGTH="10"> <?php  p($tunit); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Trial Period"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cTrial" VALUE="<?php  pgetHVar($q_pub,'TrialTime'); ?>" SIZE="10" MAXLENGTH="10"> <?php  p($tunit); ?>
		</TD>
	</TR>
	<tr><td colspan=2 align=center><a href="deftime.php?Pub=<?php echo $Pub; ?>"><?php putGS("Countries defaults"); ?></a></td></tr>

	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  pencHTML($Pub); ?>">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
		<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/admin/pub/'">
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
<?php camp_html_copyright_notice(); ?>
</BODY>
<?php  } ?>

</HTML>
