INCLUDE_PHP_LIB(<*$ADMIN_DIR/country*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageCountries*>)

B_HEAD
	X_TITLE(<*Adding new translation*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to translate country names.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todef('cName');
    todefnum('cLanguage');
    todefnum('Language');
    todef('cCode');
?>dnl
B_HEADER(<*Adding new translation*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Countries*>, <*country/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT * FROM Countries WHERE Code='$cCode' AND IdLanguage=$Language", 'q_country');
    if ($NUM_ROWS) {
	fetchRow($q_country);
	$correct= 1;
	$created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new translation*>)
	X_MSGBOX_TEXT(<*
<?php 
    if (trim($cName) == "" || trim($cName) == " ") {
	$correct= 0;
	?>
	<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
    <?php  }
    
    if ($correct) {
	if ($cLanguage == "")
	    $cLanguage= 0;
	query ("SELECT COUNT(*) as count FROM Languages WHERE Id=$cLanguage", 'q_xlang');
	fetchRow($q_xlang);
	if (getVar($q_xlang,'count') == 0) {
	    $correct= 0;
	    ?>
	<LI><?php  putGS('You must select a language.'); ?></LI>
	<?php  }
    }
    
    if ($correct) {
	query ("INSERT IGNORE INTO Countries SET Code='$cCode', IdLanguage = $cLanguage, Name = '$cName'");
	if ($AFFECTED_ROWS > 0)
	    $created= 1;
    }
    
    if ($correct) {
	if ($created) {
	    ?>dnl
	<LI><?php  putGS('The country name $1 has been translated','<B>'.getHVar($q_country,'Name').'</B>'); ?></LI>
X_AUDIT(<*132*>, <*getGS('Country name $1 translated',getSVar($q_country,'Name'))*>)
	<?php  } else { ?>dnl
	<LI><?php  putGS('The country name $1 could not be translated','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
	<?php  } ?>dnl
    <?php  } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<?php  if ($created) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/country/*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/country/*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such country name.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

