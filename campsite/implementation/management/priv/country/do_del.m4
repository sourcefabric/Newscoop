INCLUDE_PHP_LIB(<*country*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteCountries*>)

B_HEAD
	X_TITLE(<*Deleting country*>)
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
B_HEADER(<*Deleting country*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Countries*>, <*country/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT * FROM Countries WHERE Code='$Code' AND IdLanguage=$Language", 'q_ctr');
    if ($NUM_ROWS) {
	query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
?>dnl

<P>
B_MSGBOX(<*Deleting country*>)
	X_MSGBOX_TEXT(<*
<?php 
    query ("DELETE FROM Countries WHERE Code='$Code' AND IdLanguage=$Language");
    if ($AFFECTED_ROWS > 0) { 
	fetchRow($q_ctr);
	fetchRow($q_lang);
	$del=1;
    ?>dnl
		<LI><?php  putGS('The country $1 has been deleted.' ,'<B>'.getHVar($q_ctr,'Name').'('.getHVar($q_lang,'Name').')</B>'); ?></LI>
X_AUDIT(<*134*>, <*getGS('Country $1 deleted',getSVar($q_ctr,'Name').' ('.getSVar($q_lang,'Name').')' )*>)
<?php  } else { ?>dnl
		<LI><?php  putGS('The country $1 could not be deleted.' ,'<B>'.getHVar($q_ctr,'Name').'('.getHVar($q_lang,'Name').')</B>'); ?></LI>
<?php  } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<?php  if ($del) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/country/*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/country/*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such country.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

