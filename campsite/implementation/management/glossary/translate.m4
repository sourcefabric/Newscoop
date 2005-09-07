INCLUDE_PHP_LIB(<*$ADMIN_DIR/glossary*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageDictionary*>)

B_HEAD
	X_TITLE(<*Translate keyword*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add keywords.*>)
<?php  }
    query ("SELECT Keyword FROM Dictionary WHERE 1=0", 'k');
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Translate keyword*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary*>, <*glossary/*>)
E_HEADER_BUTTONS
E_HEADER

<?php  
    todefnum('Keyword');
    query ("SELECT Keyword FROM Dictionary WHERE Id=$Keyword ORDER BY IdLanguage", 'k');
    if ($NUM_ROWS) {
        $nr=$NUM_ROWS;
	$NUM_ROWS= 0;
	query ("SELECT Languages.Id, Languages.Name FROM Languages LEFT JOIN Dictionary ON Dictionary.Id = $Keyword AND Dictionary.IdLanguage = Languages.Id WHERE Dictionary.Id IS NULL ORDER BY Name", 'languages');
	if ($NUM_ROWS) { 
	    $nr2=$NUM_ROWS;
?>dnl
<P>
B_DIALOG(<*Translate keyword*>, <*POST*>, <*do_translate.php*>)
	B_DIALOG_INPUT(<*Keyword*>)
<?php 
    $comma= 0;
    for ($loop=0;$loop<$nr;$loop++) {
	fetchRow($k);
	if ($comma)
	    print ', ';
	pgetHVar($k,'Keyword');
	$comma= 1;
    }
?>dnl
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Translation*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cKeyword" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
		<SELECT NAME="cLang" class="input_select"><?php 
		    for($loop2=0;$loop2<$nr2;$loop2++) {
			fetchRow($languages);
			pcomboVar(getVar($languages,'Id'),'',getVar($languages,'Name'));
		    }
		?></SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="cId" VALUE="<?php  print encHTML($Keyword); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/glossary/*>)
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
	<LI><?php  putGS('No such keyword.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
