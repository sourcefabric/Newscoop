INCLUDE_PHP_LIB(<*$ADMIN_DIR/glossary/keyword*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteDictionary*>)

B_HEAD
	X_TITLE(<*Unlinking infotype from keyword*>)
<?php  if ($access == 0) { ?>dnl
        X_AD(<*You do not have the right to unlink infotypes from keywords.*>)
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
B_HEADER(<*Unlinking infotype from keyword*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Keyword Infotypes*>, <*glossary/keyword/?Keyword=<?php  pencURL($Keyword); ?>&Language=<?php  pencURL($Language); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Glossary*>, <*glossary/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Keyword FROM Dictionary WHERE Id=$Keyword AND IdLanguage=$Language", 'q_kwd');
    if ($NUM_ROWS) {
	$NUM_ROWS= 0;
	query ("SELECT Name FROM Classes WHERE Id=$Class AND IdLanguage=$Language", 'q_cls');
	if ($NUM_ROWS) {
	    $NUM_ROWS= 0;
	    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
	    if ($NUM_ROWS) { 
		fetchRow($q_kwd);
		fetchRow($q_lang);
	    ?>dnl
B_CURRENT
X_CURRENT(<*Keyword*>, <*<?php  pgetHVar($q_kwd,'Keyword'); ?>*>)
X_CURRENT(<*Language*>, <*<?php  pgetHVar($q_lang,'Name'); ?>*>)
E_CURRENT


<P>
B_MSGBOX(<*Deleting keyword infotype*>)
<?php 
    query ("DELETE FROM KeywordClasses WHERE IdDictionary=$Keyword AND IdClasses=$Class AND IdLanguage=$Language");
    if ($AFFECTED_ROWS > 0) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The infotype has been deleted.'); ?></LI>*>)
<?php  } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The infotype could not be deleted.'); ?></LI>*>)
<?php  } ?>dnl
	B_MSGBOX_BUTTONS
<?php  if ($AFFECTED_ROWS > 0) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/glossary/keyword/?Keyword=<?php  pencURL($Keyword); ?>&Language=<?php  pencURL($Language); ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/glossary/keyword/?Keyword=<?php  pencURL($Keyword); ?>&Language=<?php  pencURL($Language); ?>*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
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
