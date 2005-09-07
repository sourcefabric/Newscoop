INCLUDE_PHP_LIB(<*$ADMIN_DIR/infotype*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageClasses*>)

B_HEAD
	X_TITLE(<*Adding new translation*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add glossary infotypes.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Adding new translation*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary infotypes*>, <*infotype/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    todef('cName');
    todefnum('cLang');
    todefnum('cId');
    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new translation*>)
	X_MSGBOX_TEXT(<*
<?php 
    if ($cName == "") {
	$correct= 0; ?>
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Translation').'</B>'); ?></LI>
<?php  } 

    if ($correct) {
	query ("INSERT IGNORE INTO Classes SET Id=$cId, IdLanguage='$cLang', Name='$cName'");
	$created= ($AFFECTED_ROWS > 0);
    }

    if ($created) { ?>dnl
		<LI><?php  putGS('The infotype $1 has been added.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
X_AUDIT(<*81*>, <*getGS('Infotype $1 added', decS($cName))*>)
<?php  } else {
    if ($correct != 0) { ?>dnl
		<LI><?php  putGS('The infotype could not be added.'); ?><LI></LI><?php  putGS('Check if the translation does not already exist.'); ?></LI>
<?php  }
} ?>dnl
		*>)
<?php  if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*New*>, <*Add another*>, <*X_ROOT/infotype/translate.php?Class=<?php  print encURL($cId); ?>*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/infotype/*>)
	E_MSGBOX_BUTTONS
<?php  } else { ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/infotype/translate.php?Class=<?php  print encURL($cId); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } ?>dnl
E_MSGBOX
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

