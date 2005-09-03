INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

<?php
	todef('TOL_Language');
    todefnum('Pub');
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'q_lang');
?>dnl
CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_TITLE(<*Configure publication*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to edit publication information.*>)
<?php  }
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'q_lang');

    query ("SELECT Unit, Name FROM TimeUnits WHERE 1=0", 'q_unit');
    query("SELECT  Id as IdLang FROM Languages WHERE code='$TOL_Language'", 'q_def_lang');
	if($NUM_ROWS == 0){
		query("SELECT IdDefaultLanguage as IdLang  FROM Publications WHERE Id=$Pub", 'q_def_lang');
	}
	fetchRow($q_def_lang);
	$IdLang = getVar($q_def_lang,'IdLang');
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl

B_HEADER(<*Configure publication*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER
<?php
    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT
<?php
	$sql = "SELECT Unit, Name FROM TimeUnits WHERE (IdLanguage=$IdLang or IdLanguage = 1) and Unit='".getHVar($q_pub,'TimeUnit')."' order by IdLanguage desc";
	query($sql, 'q_tunit');
	fetchRow($q_tunit); $tunit =getVar($q_tunit,'Name');
?>dnl
<P>
B_DIALOG(<*Configure publication*>, <*POST*>, <*do_edit.php*>)
	<tr><td colspan=2><b><?php putGS("General attributes"); ?></b></td></tr>
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" VALUE="<?php  pgetHVar($q_pub,'Name'); ?>" SIZE="32" MAXLENGTH="255">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Default Site Alias*>)
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
	<a href="X_ROOT/pub/aliases.php?Pub=<?php echo $Pub ?>"><?php putGS("Edit aliases"); ?></a>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Default language*>)
		<SELECT NAME="cLanguage" class="input_select">
<?php 
	query ("SELECT Id, OrigName FROM Languages", 'q_lang');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_lang);
		pcomboVar(getVar($q_lang,'Id'),getVar($q_pub,'IdDefaultLanguage'),getVar($q_lang,'OrigName'));
	}
?>dnl
	    </SELECT>&nbsp;
	<a href="X_ROOT/languages/"><?php putGS("Edit languages"); ?></a>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*URL Type*>)
		<SELECT NAME="cURLType" class="input_select">
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
		<INPUT TYPE="TEXT" class="input_text" NAME="cPayTime" VALUE="<?php  pgetHVar($q_pub,'PayTime'); ?>" SIZE="5" MAXLENGTH="5"> <?php  p($tunit); ?>
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
		pcomboVar(getVar($q_unit,'Unit'),getVar($q_pub,'TimeUnit'),getVar($q_unit,'Name'));
	}
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Unit Cost*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cUnitCost" VALUE="<?php  pgetHVar($q_pub,'UnitCost'); ?>" SIZE="10" MAXLENGTH="10">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Currency*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cCurrency" VALUE="<?php  pgetHVar($q_pub,'Currency'); ?>" SIZE="10" MAXLENGTH="10">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Paid Period*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cPaid" VALUE="<?php  pgetHVar($q_pub,'PaidTime'); ?>" SIZE="10" MAXLENGTH="10"> <?php  p($tunit); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Trial Period*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cTrial" VALUE="<?php  pgetHVar($q_pub,'TrialTime'); ?>" SIZE="10" MAXLENGTH="10"> <?php  p($tunit); ?>
	E_DIALOG_INPUT
	<tr><td colspan=2 align=center><a href="deftime.php?Pub=<?php echo $Pub; ?>"><?php putGS("Countries defaults"); ?></a></td></tr>

	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  pencHTML($Pub); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
