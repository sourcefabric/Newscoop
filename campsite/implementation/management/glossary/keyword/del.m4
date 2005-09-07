INCLUDE_PHP_LIB(<*$ADMIN_DIR/glossary/keyword*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteDictionary*>)

B_HEAD
	X_TITLE(<*Unlink infotype from keyword*>)
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
B_HEADER(<*Unlink infotype from keyword*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Keyword infotypes*>, <*glossary/keyword/?Keyword=<?php  pencURL($Keyword); ?>&Language=<?php  pencURL($Language); ?>*>)
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
		fetchRow($q_cls);
		     ?>dnl
B_CURRENT
X_CURRENT(<*Keyword*>, <*<?php  pgetHVar($q_kwd,'Keyword'); ?>*>)
X_CURRENT(<*Language*>, <*<?php  pgetHVar($q_lang,'Name'); ?>*>)
E_CURRENT

<P>
B_MSGBOX(<*Unlink infotype from keyword*>)
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Are you sure you want to unlink the infotype $1 from the keyword $2?','<B>'.getHVar($q_cls,'Name').'</B>','<B>'.getHVar($q_kwd,'Keyword').'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="Keyword" VALUE="<?php  pencHTML($Keyword); ?>">
		<INPUT TYPE="HIDDEN" NAME="Class" VALUE="<?php  pencHTML($Class); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  pencHTML($Language); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/glossary/keyword/?Keyword=<?php  pencURL($Keyword); ?>&Language=<?php  pencURL($Language); ?>*>)
		</FORM>
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
