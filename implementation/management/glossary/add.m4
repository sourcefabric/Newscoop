INCLUDE_PHP_LIB(<*$ADMIN_DIR/glossary*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageDictionary*>)

B_HEAD
	X_TITLE(<*Add new keyword*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add keywords.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new keyword*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary*>, <*glossary/*>)
E_HEADER_BUTTONS
E_HEADER

<P>
B_DIALOG(<*Add new keyword*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Keyword*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cKeyword" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
		<?php  query ("SELECT Id, Name FROM Languages ORDER BY Name", 'q'); ?>
		<SELECT NAME="cLang" class="input_select"><?php 

		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q);
			pcomboVar(getVar($q,'Id'),'',getVar($q,'Name'));
		    }
		?></SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/glossary/*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
