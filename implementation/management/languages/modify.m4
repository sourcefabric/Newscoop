INCLUDE_PHP_LIB(<*$ADMIN_DIR/languages*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageLanguages*>)

B_HEAD
	X_TITLE(<*Edit language*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to edit languages.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Edit language*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Languages*>, <*languages/*>)
E_HEADER_BUTTONS
E_HEADER

<P>
<?php 
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
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" VALUE="<?php  pgetHVar($l,'Name'); ?>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Native name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cOrigName" VALUE="<?php  pgetHVar($l,'OrigName'); ?>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Code page*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cCodePage" VALUE="<?php  pgetHVar($l,'CodePage'); ?>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Code*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cCode" VALUE="<?php  pgetHVar($l,'Code'); ?>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	X_DIALOG_TEXT(<*<?php  putGS('Please enter the translation for month names.'); ?>*>)
	B_DIALOG_INPUT(<*January*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cMonth1" VALUE="<?php  pgetHVar($l,'Month1'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*February*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cMonth2" VALUE="<?php  pgetHVar($l,'Month2'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*March*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cMonth3" VALUE="<?php  pgetHVar($l,'Month3'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*April*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cMonth4" VALUE="<?php  pgetHVar($l,'Month4'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*May*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cMonth5" VALUE="<?php  pgetHVar($l,'Month5'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*June*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cMonth6" VALUE="<?php  pgetHVar($l,'Month6'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*July*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cMonth7" VALUE="<?php  pgetHVar($l,'Month7'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*August*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cMonth8" VALUE="<?php  pgetHVar($l,'Month8'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*September*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cMonth9" VALUE="<?php  pgetHVar($l,'Month9'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*October*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cMonth10" VALUE="<?php  pgetHVar($l,'Month10'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*November*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cMonth11" VALUE="<?php  pgetHVar($l,'Month11'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*December*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cMonth12" VALUE="<?php  pgetHVar($l,'Month12'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	X_DIALOG_TEXT(<*<?php  putGS('Please enter the translation for week day names.'); ?>*>)
	B_DIALOG_INPUT(<*Sunday*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cWDay1" VALUE="<?php  pgetHVar($l,'WDay1'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Monday*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cWDay2" VALUE="<?php  pgetHVar($l,'WDay2'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Tuesday*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cWDay3" VALUE="<?php  pgetHVar($l,'WDay3'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Wednesday*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cWDay4" VALUE="<?php  pgetHVar($l,'WDay4'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Thursday*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cWDay5" VALUE="<?php  pgetHVar($l,'WDay5'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Friday*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cWDay6" VALUE="<?php  pgetHVar($l,'WDay6'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Saturday*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cWDay7" VALUE="<?php  pgetHVar($l,'WDay7'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	X_DIALOG_TEXT(<*<?php  putGS('Please enter the translation for time units.'); ?>*>)
	<?php  for($i=0; $i<$def_tu; $i++){
		fetchRow($q_def_tu);
		if($tu) fetchRow($q_tu); ?>dnl
		<TR><TD ALIGN="RIGHT"><?php pgetHVar($q_def_tu, 'Name');?></TD><TD><INPUT TYPE="TEXT" NAME="<?php pgetHVar($q_def_tu, 'Unit');?>" VALUE="<?php  pgetHVar($tu ? $q_tu : $q_def_tu ,'Name'); ?>" SIZE="20" MAXLENGTH="20"></TD></TR>
	<?php } ?> dnl

	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Lang" VALUE="<?php  print encHTML($Lang); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/languages/*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such language.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML


