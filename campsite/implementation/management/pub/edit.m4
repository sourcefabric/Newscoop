B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

<?php 
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'q_lang');
?>dnl
CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Change publication information*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to edit publication information.*>)
<?php  }
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'q_lang');

    query ("SELECT Unit, Name FROM TimeUnits WHERE 1=0", 'q_unit');
    query("SELECT  Id as IdLang FROM Languages WHERE code='$TOL_Language'", 'q_def_lang');
	if($NUM_ROWS == 0){
		query("SELECT IdDefaultLanguage as IdLang  FROM Publications WHERE Id=1", 'q_def_lang');
	}
	fetchRow($q_def_lang);
	$IdLang = getVar($q_def_lang,'IdLang');
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Pub');
?>
B_HEADER(<*Change publication information*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT
	<?php query ("SELECT Unit, Name FROM TimeUnits WHERE (IdLanguage=$IdLang or IdLanguage = 1) and Unit='".getHVar($q_pub,'TimeUnit')."' order by IdLanguage desc", 'q_tunit');
		fetchRow($q_tunit); $tunit =getVar($q_tunit,'Name'); ?>dnl
<P>
B_DIALOG(<*Change publication information*>, <*POST*>, <*do_edit.php*>)
	<tr><td colspan=2><b><?php putGS("General attributes"); ?></b></td></tr>
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" VALUE="<?php  pgetHVar($q_pub,'Name'); ?>" SIZE="32" MAXLENGTH="255">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Default Site Alias*>)
		<SELECT NAME="cDefaultAlias">
<?php
	$sql = "SELECT * FROM Aliases WHERE IdPublication = " . $Pub;
	query ($sql, 'q_alias');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_alias);
		pcomboVar(getVar($q_alias,'Id'),getVar($q_pub,'IdDefaultAlias'),getVar($q_alias,'Name'));
	}
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Default language*>)
		<SELECT NAME="cLanguage">
<?php 
	query ("SELECT Id, OrigName FROM Languages", 'q_lang');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_lang);
		pcomboVar(getVar($q_lang,'Id'),getVar($q_pub,'IdDefaultLanguage'),getVar($q_lang,'OrigName'));
	}
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*URL Type*>)
		<SELECT NAME="cURLType">
<?php
	$sql = "SELECT * FROM URLTypes";
	query ($sql, 'q_urltype');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_urltype);
		pcomboVar(getVar($q_urltype,'Id'),getVar($q_pub,'IdURLType'),getVar($q_urltype,'Name'));
	}
?>dnl
	    </SELECT>
	E_DIALOG_INPUT

	<tr><td colspan=2><HR NOSHADE SIZE="1" COLOR="BLACK"></td></tr>
	<tr><td colspan=2><b><?php putGS("Subscriptions defaults"); ?></b></td></tr>
	B_DIALOG_INPUT(<*Pay Period*>)
		<INPUT TYPE="TEXT" NAME="cPayTime" VALUE="<?php  pgetHVar($q_pub,'PayTime'); ?>" SIZE="5" MAXLENGTH="5"> <?php  p($tunit); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Time Unit*>)
	    <SELECT NAME="cTimeUnit">
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
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Unit Cost*>)
		<INPUT TYPE="TEXT" NAME="cUnitCost" VALUE="<?php  pgetHVar($q_pub,'UnitCost'); ?>" SIZE="10" MAXLENGTH="10">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Currency*>)
		<INPUT TYPE="TEXT" NAME="cCurrency" VALUE="<?php  pgetHVar($q_pub,'Currency'); ?>" SIZE="10" MAXLENGTH="10">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Paid Period*>)
		<INPUT TYPE="TEXT" NAME="cPaid" VALUE="<?php  pgetHVar($q_pub,'PaidTime'); ?>" SIZE="10" MAXLENGTH="10"> <?php  p($tunit); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Trial Period*>)
		<INPUT TYPE="TEXT" NAME="cTrial" VALUE="<?php  pgetHVar($q_pub,'TrialTime'); ?>" SIZE="10" MAXLENGTH="10"> <?php  p($tunit); ?>
	E_DIALOG_INPUT

	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  pencHTML($Pub); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
