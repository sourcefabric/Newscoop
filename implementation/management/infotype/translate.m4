INCLUDE_PHP_LIB(<*$ADMIN_DIR/infotype*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageClasses*>)

B_HEAD
	X_TITLE(<*Translate infotype*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add glossary infotypes.*>)
<?php  }
    query ("SELECT Name FROM Classes WHERE 1=0", 'c');
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Translate infotype*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary infotypes*>, <*infotype/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    todefnum('Infotype');
    query ("SELECT Name FROM Classes WHERE Id=$Infotype", 'c');
    $nr=$NUM_ROWS;
    if ($NUM_ROWS) {
	$NUM_ROWS= 0;
	query ("SELECT Languages.Id, Languages.Name FROM Languages LEFT JOIN Classes ON Classes.Id = $Infotype AND Classes.IdLanguage = Languages.Id WHERE Classes.Id IS NULL ORDER BY Name", 'languages');
	$nr_lang=$NUM_ROWS;
	if ($NUM_ROWS) { ?>dnl
<P>
B_DIALOG(<*Translate keyword*>, <*POST*>, <*do_translate.php*>)
	B_DIALOG_INPUT(<*Keyword infotype*>)
<?php 
    $comma= 0;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($c);
	if ($comma)
	    print ',';
	pgetHVar($c,'Name');
	$comma= 1;
    }
?>dnl	
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Translation*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
		<SELECT NAME="cLang" class="input_select"><?php 
		
		    for($loop=0;$loop<$nr_lang;$loop++) {
			fetchRow($languages);
			pcomboVar(getVar($languages,'Id'),'',getVar($languages,'Name'));
		    }
		?></SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="cId" VALUE="<?php  print encHTML($Infotype); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/infotype/*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No more languages.'); ?></LI>
</BLOCKQUOTE>
<?php  }
} else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such keyword infotype.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

