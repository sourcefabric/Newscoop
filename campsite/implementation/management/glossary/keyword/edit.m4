INCLUDE_PHP_LIB(<*$ADMIN_DIR/glossary/keyword*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageDictionary*>)

B_HEAD
	X_TITLE(<*Edit keyword/infotype definition*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change definitions.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Keyword');
    todefnum('Class');
    todefnum('Language');
?>dnl
B_HEADER(<*Edit keyword/infotype definition*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Keyword infotypes*>, <*glossary/keyword/?Keyword=<?php  pencURL($Keyword); ?>&Language=<?php  pencURL($Language); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Glossary*>, <*glossary/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Keyword FROM Dictionary WHERE Id=$Keyword AND IdLanguage=$Language", 'q_kwd');
    if ($NUM_ROWS) { 
	query ("SELECT Name FROM Classes WHERE Id=$Class AND IdLanguage=$Language", 'q_cls');
	if ($NUM_ROWS) {
	    query ("SELECT Definition FROM KeywordClasses WHERE IdDictionary=$Keyword AND IdClasses=$Class AND IdLanguage=$Language", 'q_kwdcls');
	    if ($NUM_ROWS){
		query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		if ($NUM_ROWS) {
		    fetchRow($q_kwd);
		    fetchRow($q_cls);
		    fetchRow($q_lang);
		    fetchRow($q_kwdcls);
		
?>dnl
B_CURRENT
X_CURRENT(<*Keyword*>, <*<?php  pgetHVar($q_kwd,'Keyword'); ?>*>)
X_CURRENT(<*Infotype*>, <*<?php  pgetHVar($q_cls,'Name'); ?>*>)
X_CURRENT(<*Language*>, <*<?php  pgetHVar($q_lang,'Name'); ?>*>)
E_CURRENT


<P>
B_DIALOG(<*Edit keyword*>, <*POST*>, <*do_edit.php*>)
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Definition*>)
		<TEXTAREA NAME="cDefinition" ROWS="8" COLS="60"><?php  pgetHVar($q_kwdcls,'Definition'); ?></TEXTAREA>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Keyword" VALUE="<?php  pencHTML($Keyword); ?>">
		<INPUT TYPE="HIDDEN" NAME="Class" VALUE="<?php  pencHTML($Class); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  pencHTML($Language); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/glossary/keyword/?Keyword=<?php  pencURL($Keyword); ?>&Language=<?php  pencHTML($Language); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such language.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such infotype.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such keyword/infotype definition.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such keyword.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

