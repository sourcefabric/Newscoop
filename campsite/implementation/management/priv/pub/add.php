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

	<TITLE><?php  putGS("Add new publication"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to add publications." )); ?>">
<?php  } ?></HEAD>

<?php  if ($access) { ?> 
 

<BODY >

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Add new publication"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php
	todef('TOL_Language');
	query ("SELECT Unit, Name FROM TimeUnits WHERE 1=0", 'q_unit');
	query("SELECT Id as IdLang FROM Languages WHERE code='$TOL_Language'", 'q_def_lang');
	if($NUM_ROWS == 0){
		query("SELECT IdDefaultLanguage as IdLang FROM Publications WHERE Id = 1", 'q_def_lang');
	}
	fetchRow($q_def_lang);
	$IdLang = getVar($q_def_lang,'IdLang');
?>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php"  >
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("Add new publication"); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="255">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Site"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cSite" VALUE="<?php pencHTML($_SERVER['HTTP_HOST']); ?>" SIZE="32" MAXLENGTH="255">
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
			pcomboVar(getVar($q_lang,'Id'),getVar($q_def_lang,'IdLang'),getVar($q_lang,'OrigName'));
		    }
	    ?>	    </SELECT>
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
		pcomboVar(getVar($q_urltype,'Id'),0,getVar($q_urltype,'Name'));
	}
?>	    </SELECT>
		</TD>
	</TR>

	<tr><td colspan=2><HR NOSHADE SIZE="1" COLOR="BLACK"></td></tr>
	<tr><td colspan=2><b><?php putGS("Subscriptions defaults"); ?></b></td></tr>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Pay Period"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cPayTime" VALUE="" SIZE="5" MAXLENGTH="5">
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
		pcomboVar(getVar($q_unit,'Unit'),0,getVar($q_unit,'Name'));
	}
?>	    </SELECT>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Unit Cost"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cUnitCost" VALUE="" SIZE="10" MAXLENGTH="10">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Currency"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cCurrency" VALUE="" SIZE="10" MAXLENGTH="10">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Paid Period"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cPaid" VALUE="" SIZE="10" MAXLENGTH="10">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Trial Period"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="cTrial" VALUE="" SIZE="10" MAXLENGTH="10">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
		<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/admin/pub/'">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
</BODY>
<?php  } ?>

</HTML>
