INCLUDE_PHP_LIB(<*$ADMIN_DIR/glossary/keyword*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageDictionary*>)

B_HEAD
	X_TITLE(<*Changing keyword/infotype definition*>)
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
    todef('cDefinition');
?>dnl
B_HEADER(<*Changing keyword/infotype definition*>)
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
	    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
	    if ($NUM_ROWS) { 
		fetchRow($q_kwd);
		fetchRow($q_cls);
		fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Keyword*>, <*<?php  pgetHVar($q_kwd,'Keyword'); ?>*>)
X_CURRENT(<*Infotype*>, <*<?php  pgetHVar($q_cls,'Name'); ?>*>)
X_CURRENT(<*Language*>, <*<?php  pgetHVar($q_lang,'Name'); ?>*>)
E_CURRENT

<P>
B_MSGBOX(<*Changing keyword*>)
	X_MSGBOX_TEXT(<*
<?php 
    query ("UPDATE KeywordClasses SET Definition='".encHTML($cDefinition)."' WHERE IdDictionary=$Keyword AND IdClasses=$Class AND IdLanguage=$Language");
    if ($AFFECTED_ROWS > 0) { ?>dnl
		<LI><?php  putGS('The keyword has been changed.'); ?></LI>
X_AUDIT(<*93*>, <*getGS('Keyword $1 changed',getHVar($q_kwd,'Keyword'))*>)
<?php  } else { ?>dnl
		<LI><?php  putGS('The keyword could not be changed.'); ?><LI>
<?php  } ?>dnl
		*>)
<?php  if ($AFFECTED_ROWS > 0) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/glossary/keyword/?Keyword=<?php  pencURL($Keyword); ?>&Language=<?php  pencURL($Language); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } else { ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/glossary/keyword/?Keyword=<?php  pencURL($Keyword); ?>&Language=<?php  pencURL($Language); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } ?>dnl
E_MSGBOX
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
	<LI><?php  putGS('No such keyword.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

