B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageLanguages*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Edit language*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to edit languages.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Edit language*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Languages*>, <*languages/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<P>
<?
    todefnum('Lang');
    query ("SELECT * FROM TimeUnits WHERE IdLanguage=1", 'q_def_tu');
    	$def_tu=$NUM_ROWS;
    query ("SELECT * FROM TimeUnits WHERE IdLanguage=$Lang", 'q_tu');
    	$tu=$NUM_ROWS;

    query ("SELECT * FROM Languages WHERE Id=$Lang", 'l');
    if ($NUM_ROWS) { 
	fetchRow($l);
	?>
B_DIALOG(<*Edit language*>, <*POST*>, <*do_modify.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" VALUE="<? pgetHVar($l,'Name'); ?>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Native name*>)
		<INPUT TYPE="TEXT" NAME="cOrigName" VALUE="<? pgetHVar($l,'OrigName'); ?>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Code page*>)
		<INPUT TYPE="TEXT" NAME="cCodePage" VALUE="<? pgetHVar($l,'CodePage'); ?>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Code*>)
		<INPUT TYPE="TEXT" NAME="cCode" VALUE="<? pgetHVar($l,'Code'); ?>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	X_DIALOG_TEXT(<*<? putGS('Please enter the translation for month names.'); ?>*>)
	B_DIALOG_INPUT(<*January*>)
		<INPUT TYPE="TEXT" NAME="cMonth1" VALUE="<? pgetHVar($l,'Month1'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*February*>)
		<INPUT TYPE="TEXT" NAME="cMonth2" VALUE="<? pgetHVar($l,'Month2'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*March*>)
		<INPUT TYPE="TEXT" NAME="cMonth3" VALUE="<? pgetHVar($l,'Month3'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*April*>)
		<INPUT TYPE="TEXT" NAME="cMonth4" VALUE="<? pgetHVar($l,'Month4'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*May*>)
		<INPUT TYPE="TEXT" NAME="cMonth5" VALUE="<? pgetHVar($l,'Month5'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*June*>)
		<INPUT TYPE="TEXT" NAME="cMonth6" VALUE="<? pgetHVar($l,'Month6'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*July*>)
		<INPUT TYPE="TEXT" NAME="cMonth7" VALUE="<? pgetHVar($l,'Month7'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*August*>)
		<INPUT TYPE="TEXT" NAME="cMonth8" VALUE="<? pgetHVar($l,'Month8'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*September*>)
		<INPUT TYPE="TEXT" NAME="cMonth9" VALUE="<? pgetHVar($l,'Month9'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*October*>)
		<INPUT TYPE="TEXT" NAME="cMonth10" VALUE="<? pgetHVar($l,'Month10'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*November*>)
		<INPUT TYPE="TEXT" NAME="cMonth11" VALUE="<? pgetHVar($l,'Month11'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*December*>)
		<INPUT TYPE="TEXT" NAME="cMonth12" VALUE="<? pgetHVar($l,'Month12'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	X_DIALOG_TEXT(<*<? putGS('Please enter the translation for week day names.'); ?>*>)
	B_DIALOG_INPUT(<*Sunday*>)
		<INPUT TYPE="TEXT" NAME="cWDay1" VALUE="<? pgetHVar($l,'WDay1'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Monday*>)
		<INPUT TYPE="TEXT" NAME="cWDay2" VALUE="<? pgetHVar($l,'WDay2'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Tuesday*>)
		<INPUT TYPE="TEXT" NAME="cWDay3" VALUE="<? pgetHVar($l,'WDay3'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Wednesday*>)
		<INPUT TYPE="TEXT" NAME="cWDay4" VALUE="<? pgetHVar($l,'WDay4'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Thursday*>)
		<INPUT TYPE="TEXT" NAME="cWDay5" VALUE="<? pgetHVar($l,'WDay5'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Friday*>)
		<INPUT TYPE="TEXT" NAME="cWDay6" VALUE="<? pgetHVar($l,'WDay6'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Saturday*>)
		<INPUT TYPE="TEXT" NAME="cWDay7" VALUE="<? pgetHVar($l,'WDay7'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	X_DIALOG_TEXT(<*<? putGS('Please enter the translation for time units.'); ?>*>)
	<? for($i=0; $i<$def_tu; $i++){
		fetchRow($q_def_tu);
		if($tu) fetchRow($q_tu); ?>dnl
		<TR><TD ALIGN="RIGHT"><?pgetHVar($q_def_tu, 'Name');?></TD><TD><INPUT TYPE="TEXT" NAME="<?pgetHVar($q_def_tu, 'Unit');?>" VALUE="<? pgetHVar($tu ? $q_tu : $q_def_tu ,'Name'); ?>" SIZE="20" MAXLENGTH="20"></TD></TR>
	<?} ?> dnl

	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Lang" VALUE="<? print encHTML($Lang); ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/languages/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such language.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML


