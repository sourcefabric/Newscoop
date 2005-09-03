INCLUDE_PHP_LIB(<*$ADMIN_DIR/country*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteCountries*>)

B_HEAD
	X_TITLE(<*Delete country*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete countries.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
	todef('Code');
	todefnum('Language');
?>dnl
B_HEADER(<*Delete country*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Countries*>, <*country/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
	query ("SELECT * FROM Countries WHERE Code='$Code' AND IdLanguage=$Language", 'q_ctr');
	fetchRow($q_ctr);
	if ($NUM_ROWS) {
		query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		fetchRow($q_lang);
?>dnl

<P>
B_MSGBOX(<*Delete country*>)
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Are you sure you want to delete the country $1?' ,'<B>'.getHVar($q_ctr,'Name').'('.getHVar($q_lang,'Name').')</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="Code" VALUE="<?php  print encHTML(decS($Code)); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  print $Language; ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/country/*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<?php  } else {?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such country.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML


