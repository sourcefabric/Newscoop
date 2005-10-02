INCLUDE_PHP_LIB(<*country*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageCountries*>)

B_HEAD
	X_TITLE(<*Changing country name*>)
<?php  if ($access == 0) { ?>dnl
	    X_AD(<*You do not have the right to change country names.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todef('cName');
    todefnum('Language');
    todef('Code');
?>dnl
B_HEADER(<*Changing country name*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Countries*>, <*country/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT * FROM Countries WHERE IdLanguage=$Language AND Code='$Code'", 'q_country');
    if ($NUM_ROWS) {
	$correct= 1; ?>dnl
<P>
B_MSGBOX(<*Changing country name*>)
	X_MSGBOX_TEXT(<*
<?php 
    if (trim($cName) == "" || trim($cName) == " ") {
	$correct= 0; ?>dnl
	<LI>You must complete the <B>Name</B> field.</LI>
<?php  } 

    if ($correct) {
	query ("SELECT COUNT(*) FROM Countries WHERE Name = '$cName' AND IdLanguage = $Language", 'q_cnt');
	fetchRowNum($q_cnt);
	if (getNumVar($q_cnt,0) == 0)
	    query ("UPDATE Countries SET Name = '$cName' WHERE Code='$Code' AND IdLanguage = $Language");
	else
	    $AFFECTED_ROWS= 0;
    
    if ($AFFECTED_ROWS > 0) { ?>dnl
	<LI><?php  putGS('The country name $1 has been changed','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
X_AUDIT(<*133*>, <*getGS('Country name $1 changed',$cName)*>)
<?php  } else { ?>dnl
	<LI><?php  putGS('The country name $1 could not be changed','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
<?php  } 
 } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<?php  if ($AFFECTED_ROWS > 0) { ?>dnl
		REDIRECT(<*No*>, <*OK*>, <*X_ROOT/country/*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/country/edit.php?Code=<?php  print encURL(decS($Code)); ?>&Language=<?php  print encHTML($Language); ?>*>)
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

