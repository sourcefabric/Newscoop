B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageLanguages*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new language*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add languages.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new language*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Languages*>, <*languages/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?	query ("SELECT * FROM TimeUnits WHERE IdLanguage=1", 'q_def_tu');
    	$def_tu=$NUM_ROWS;
    ?>

<P>
B_DIALOG(<*Add new language*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Native name*>)
		<INPUT TYPE="TEXT" NAME="cOrigName" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Code*>)
		<INPUT TYPE="TEXT" NAME="cCode" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Code page*>)
		<INPUT TYPE="TEXT" NAME="cCodePage" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	X_DIALOG_TEXT(<*<? putGS('Please enter the translation for month names.'); ?>*>)
	B_DIALOG_INPUT(<*January*>)
		<INPUT TYPE="TEXT" NAME="cMonth1" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*February*>)
		<INPUT TYPE="TEXT" NAME="cMonth2" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*March*>)
		<INPUT TYPE="TEXT" NAME="cMonth3" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*April*>)
		<INPUT TYPE="TEXT" NAME="cMonth4" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*May*>)
		<INPUT TYPE="TEXT" NAME="cMonth5" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*June*>)
		<INPUT TYPE="TEXT" NAME="cMonth6" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*July*>)
		<INPUT TYPE="TEXT" NAME="cMonth7" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*August*>)
		<INPUT TYPE="TEXT" NAME="cMonth8" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*September*>)
		<INPUT TYPE="TEXT" NAME="cMonth9" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*October*>)
		<INPUT TYPE="TEXT" NAME="cMonth10" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*November*>)
		<INPUT TYPE="TEXT" NAME="cMonth11" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*December*>)
		<INPUT TYPE="TEXT" NAME="cMonth12" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	X_DIALOG_TEXT(<*<? putGS('Please enter the translation for week day names.'); ?>*>)
	B_DIALOG_INPUT(<*Sunday*>)
		<INPUT TYPE="TEXT" NAME="cWDay1" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Monday*>)
		<INPUT TYPE="TEXT" NAME="cWDay2" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Tuesday*>)
		<INPUT TYPE="TEXT" NAME="cWDay3" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Wednesday*>)
		<INPUT TYPE="TEXT" NAME="cWDay4" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Thursday*>)
		<INPUT TYPE="TEXT" NAME="cWDay5" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Friday*>)
		<INPUT TYPE="TEXT" NAME="cWDay6" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Saturday*>)
		<INPUT TYPE="TEXT" NAME="cWDay7" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	
	X_DIALOG_TEXT(<*<? putGS('Please enter the translation for time units.'); ?>*>)
	<? for($i=0; $i<$def_tu; $i++){
		fetchRow($q_def_tu); ?>dnl
		<TR><TD ALIGN="RIGHT"><?pgetHVar($q_def_tu, 'Name');?></TD><TD><INPUT TYPE="TEXT" NAME="<?pgetHVar($q_def_tu, 'Unit');?>" VALUE="" SIZE="20" MAXLENGTH="20"></TD></TR>
	<?} ?> dnl
	
	B_DIALOG_BUTTONS
		SUBMIT(<*Save*>, <*Save changes*>)
<? todef('Back'); ?>dnl
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<? print encHTML($Back); ?>">
<? if ($Back != "") { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*<? print $Back; ?>*>)
<? } else { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/languages/*>)
<? } ?>dnl
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

