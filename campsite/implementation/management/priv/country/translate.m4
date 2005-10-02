INCLUDE_PHP_LIB(<*country*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageCountries*>)

B_HEAD
	X_TITLE(<*Translate country name*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to translate country names.*>)
<?php  }
    query ("SELECT Name FROM Countries WHERE 1=0", 'q_clist');
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'q_lang');
?>
E_HEAD

B_STYLE
E_STYLE

<?php  if ($access) { ?>dnl
B_BODY

<?php 
    todef('Code');
    todefnum('Language');
?>dnl
B_HEADER(<*Translate country name*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Countries*>, <*country/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT * FROM Countries WHERE Code = '$Code' and IdLanguage = $Language", 'q_country');
    if ($NUM_ROWS) { ?>dnl

<P>
B_DIALOG(<*Translate country name*>, <*POST*>, <*do_translate.php*>)
	B_DIALOG_INPUT(<*Country*>)
<?php 
    query ("SELECT Name FROM Countries WHERE Code='$Code'", 'q_clist');
    $comma= 0;
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_clist);
	if ($comma)
	    print ',';
	else
	    $comma= 1;
	pgetHVar($q_clist,'Name');
    }
?>dnl
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
		<SELECT NAME="cLanguage" class="input_select">
<?php 
    query ("SELECT Id, Name FROM Languages ORDER BY Name", 'q_lang');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_lang);
	query ("SELECT COUNT(*) as count FROM Countries WHERE Code='$Code' AND IdLanguage=".getSVar($q_lang,'Id'), 'q_xc');
	fetchRow($q_xc);
	if (getVar($q_xc,'count') == 0) { ?>dnl
			<OPTION VALUE="<?php  pgetHVar($q_lang,'Id'); ?>"><?php  pgetHVar($q_lang,'Name'); ?>
<?php  }
} ?>dnl
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE=HIDDEN NAME=cCode VALUE="<?php  print encHTML(decS($Code)); ?>">
		<INPUT TYPE=HIDDEN NAME=Language VALUE="<?php  print $Language; ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/country/*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such country name.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

