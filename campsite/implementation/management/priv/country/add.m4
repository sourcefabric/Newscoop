INCLUDE_PHP_LIB(<*country*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageCountries*>)

B_HEAD
	X_TITLE(<*Add New Country*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add countries.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new country*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Countries*>, <*country/*>)
E_HEADER_BUTTONS
E_HEADER

<P>
B_DIALOG(<*Add new country*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Code*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cCode" SIZE="2" MAXLENGTH="2">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
			<SELECT NAME="cLanguage" class="input_select">
<?php  query ("SELECT Id, Name FROM Languages ORDER BY Id", 'q_lng');
    for($loop=0;$loop<$NUM_ROWS;$loop++) {
	fetchRow($q_lng);
	print '<OPTION VALUE="'.getHVar($q_lng,'Id').'">'.getHVar($q_lng,'Name');
    } ?>dnl
			</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  todef('Language'); print encHTML($Language); ?>">
		SUBMIT(<*OK*>, <*Save changes*>)
<?php  todef('Back'); ?>dnl
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<?php  print encHTML($Back); ?>">
<?php  if ($Back != "") { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*<?php  print $Back; ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/country/*>)
<?php  } ?>dnl
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

