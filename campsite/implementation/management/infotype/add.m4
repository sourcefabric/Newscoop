B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageClasses*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new infotype*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add glossary infotypes.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new infotype*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary infotypes*>, <*infotype/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<P>
B_DIALOG(<*Add new infotype*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
		<? query ("SELECT Id, Name FROM Languages ORDER BY Name", 'q'); ?>
		<SELECT NAME="cLang"><?

		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q);
			pcomboVar(getVar($q,'Id'),'',getVar($q,'Name'));
		    }
		?></SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/infotype/*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

