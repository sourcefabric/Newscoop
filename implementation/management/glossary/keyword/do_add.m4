INCLUDE_PHP_LIB(<*$ADMIN_DIR/glossary/keyword*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageDictionary*>)

B_HEAD
	X_TITLE(<*Adding new keyword infotype*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add keyword infotypes.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Keyword');
    todefnum('Language');
    todefnum('cClass');
?>dnl
B_HEADER(<*Adding new keyword infotype*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Keyword infotypes*>, <*glossary/keyword/?Keyword=<?php  pencURL($Keyword); ?>&Language=<?php  pencURL($Language); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Glossary*>, <*glossary/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Keyword FROM Dictionary WHERE Id=$Keyword AND IdLanguage=$Language", 'q_dict');
    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
    fetchRow($q_dict);
    fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Keyword*>, <*<?php  pgetHVar($q_dict,'Keyword'); ?>*>)
X_CURRENT(<*Language*>, <*<?php  pgetHVar($q_lang,'Name') ;?>*>)
E_CURRENT

<?php 
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new keyword infotype*>)
	X_MSGBOX_TEXT(<*
<?php 
    $AFFECTED_ROWS = 0;
    if ($cClass != 0)
	query ("INSERT IGNORE INTO KeywordClasses SET IdDictionary=$Keyword, IdClasses=$cClass, IdLanguage=$Language");
    if ($AFFECTED_ROWS > 0) { ?>dnl
		<LI><?php  putGS('The keyword infotype has been added.'); ?></LI>
<?php  } else { ?>dnl
		<LI><?php  putGS('The keyword infotype could not be added.'); ?><LI></LI><?php  putGS('Please check if the keyword infotype does not already exist.'); ?></LI>
<?php  } ?>dnl
		*>)
<?php  if ($AFFECTED_ROWS > 0) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*New*>, <*Add another*>, <*X_ROOT/glossary/keyword/add.php?Keyword=<?php  pencURL($Keyword); ?>&Language=<?php  pencURL($Language); ?>*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/glossary/keyword/?Keyword=<?php  pencURL($Keyword); ?>&Language=<?php  pencURL($Language); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } else { ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/glossary/keyword/add.php?Keyword=<?php  pencURL($Keyword); ?>&Language=<?php  pencURL($Language); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } ?>dnl
E_MSGBOX
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
