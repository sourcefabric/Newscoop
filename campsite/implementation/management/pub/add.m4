INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_TITLE(<*Add new publication*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add publications.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new publication*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

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
B_DIALOG(<*Add new publication*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="255">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Site*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cSite" VALUE="<?php pencHTML($_SERVER['HTTP_HOST']); ?>" SIZE="32" MAXLENGTH="255">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Default language*>)
	    <SELECT NAME="cLanguage" class="input_select">
	    <?php 
		query ("SELECT Id, OrigName FROM Languages", 'q_lang');
		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q_lang);
			pcomboVar(getVar($q_lang,'Id'),getVar($q_def_lang,'IdLang'),getVar($q_lang,'OrigName'));
		    }
	    ?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*URL Type*>)
		<SELECT NAME="cURLType" class="input_select">
<?php
	$sql = "SELECT * FROM URLTypes";
	query ($sql, 'q_urltype');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_urltype);
		pcomboVar(getVar($q_urltype,'Id'),0,getVar($q_urltype,'Name'));
	}
?>dnl
	    </SELECT>
	E_DIALOG_INPUT

	<tr><td colspan=2><HR NOSHADE SIZE="1" COLOR="BLACK"></td></tr>
	<tr><td colspan=2><b><?php putGS("Subscriptions defaults"); ?></b></td></tr>
	B_DIALOG_INPUT(<*Pay Period*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cPayTime" VALUE="" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Time Unit*>)
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
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Unit Cost*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cUnitCost" VALUE="" SIZE="10" MAXLENGTH="10">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Currency*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cCurrency" VALUE="" SIZE="10" MAXLENGTH="10">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Paid Period*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cPaid" VALUE="" SIZE="10" MAXLENGTH="10">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Trial Period*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cTrial" VALUE="" SIZE="10" MAXLENGTH="10">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
