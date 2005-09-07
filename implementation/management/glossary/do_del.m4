INCLUDE_PHP_LIB(<*$ADMIN_DIR/glossary*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteDictionary*>)

B_HEAD
	X_TITLE(<*Deleting keyword*>)
<?php  if ($access == 0) { ?>dnl
		X_AD(<*You do not have the right to delete keywords.*>)
<?php  query ("SELECT 1", 's');
} ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting keyword*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary*>, <*glossary/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    todefnum('Keyword');
    todefnum('Language');

    query ("SELECT Keyword FROM Dictionary WHERE Id=$Keyword AND IdLanguage=$Language", 'q_dic');
    if ($NUM_ROWS) {
	query ("SELECT COUNT(*) FROM KeywordClasses WHERE IdDictionary=$Keyword AND IdLanguage=$Language", 'q_kwdcls');
	fetchRowNum($q_kwdcls);
	if (getNumVar($q_kwdcls,0) == 0) {
	    $AFFECTED_ROWS= 0;
	    fetchRow($q_dic);
	    query ("DELETE FROM Dictionary WHERE Id=$Keyword AND IdLanguage=$Language");
	    ?>dnl
<P>
B_MSGBOX(<*Deleting keyword*>)
<?php  if ($AFFECTED_ROWS > 0) { ?>
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The keyword has been deleted.'); ?></LI>*>)
X_AUDIT(<*82*>, <*getGS('Keyword $1 deleted',getHVar($q_dic,'Keyword'))*>)
<?php  } else { ?>
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The keyword could not be deleted.'); ?></LI>*>)
<?php  } ?>
	B_MSGBOX_BUTTONS
<?php  if ($AFFECTED_ROWS > 0) { ?>
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/glossary/*>)
<?php  } else { ?>
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/glossary/*>)
<?php  } ?>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('You must delete keyword infotypes first.'); ?></LI>
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
