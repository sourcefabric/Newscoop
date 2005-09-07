INCLUDE_PHP_LIB(<*$ADMIN_DIR/country*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageCountries*>)

B_HEAD
	X_TITLE(<*Adding new country*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add countries.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php  todefnum('Language'); ?>dnl
B_HEADER(<*Adding new country*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Countries*>, <*country/*>)
E_HEADER_BUTTONS
E_HEADER

<?php  
    todef('cCode');
    todef('cName');
    todefnum('cLanguage');
    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new country*>)
	X_MSGBOX_TEXT(<*
<?php  
    query ("SELECT TRIM('$cCode'), TRIM('$cName')", 'q_var');
    fetchRowNum($q_var);
    if (getNumVar($q_var,0) == "" || getNumVar($q_var,0) == " ") {
	$correct= 0;
	?>dnl
	<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Code').'</B>'); ?></LI>
<?php  } 
    if (getNumVar($q_var,1) == "" || getNumVar($q_var,1) == " ") {
	$correct=0;
	?>dnl
	<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<?php  }
    if ($cLanguage == "" || $cLanguage == "0") {
	$correct= 0
        ?>dnl
	<LI><?php  putGS('You must select a language.'); ?></LI>
<?php  } 
    if ($correct) { 
	query ("INSERT IGNORE INTO Countries SET Code='$cCode', Name='$cName', IdLanguage=$cLanguage");
	if ($AFFECTED_ROWS > 0)
		$created= 1;
 }
    if ($correct) {
	if ($created) { ?>
	<LI><?php  putGS('The country $1 has been created','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
X_AUDIT(<*131*>, <*getGS('Country $1 added',$cName)*>)
<?php  } else { ?>dnl
	<LI><?php  putGS('The country $1 could not be created','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
<?php  } ?>dnl
<?php  } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<?php  
    todef('Back');
    if ($created) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/country/*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/country/add.php<?php  if ($Back != "") { ?>?Back=<?php  print encURL($Back); } ?>*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

