INCLUDE_PHP_LIB(<*$ADMIN_DIR/infotype*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageClasses*>)

B_HEAD
	X_TITLE(<*Delete infotype*>)
<?php  if ($access == 0) { ?>dnl
		X_AD(<*You do not have the right to delete glossary infotypes.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete infotype*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary infotypes*>, <*infotype/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    todefnum('Infotype');
    todefnum('Lang');
    query ("SELECT Name FROM Classes WHERE Id=$Infotype AND IdLanguage=$Lang", 'c');
?>dnl
<P>
<?php  if ($NUM_ROWS) { 
    fetchRow($c);
?>dnl
B_MSGBOX(<*Delete infotype*>)
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Are you sure you want to delete the infotype $1?','<B>'.getHVar($c,'Name').'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="Infotype" VALUE="<?php  print encHTML(decS($Infotype)); ?>">
		<INPUT TYPE="HIDDEN" NAME="Lang" VALUE="<?php  print encHTML(decS($Lang)); ?>">
		<INPUT TYPE="HIDDEN" NAME="cName" VALUE="<?php  pgetHVar($c,'Name'); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/infotype/*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<?php  } else { ?>dnl
	<LI><?php  putGS('No such infotype.'); ?></LI>
<?php  } ?>dnl
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

