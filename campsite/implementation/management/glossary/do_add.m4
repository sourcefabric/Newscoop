INCLUDE_PHP_LIB(<*$ADMIN_DIR/glossary*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageDictionary*>)

B_HEAD
	X_TITLE(<*Adding new keyword*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add keywords.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Adding new keyword*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary*>, <*glossary/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    todef('cKeyword');
    todefnum('cLang');
    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new keyword*>)
	X_MSGBOX_TEXT(<*
<?php 
    if ($cKeyword == "") {
	$correct= 0; ?>
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Keyword').'</B>'); ?></LI>
<?php 
    }

    if ($cLang == 0) {
	$correct= 0; ?>
		<LI><?php  putGS('You must select a language.'); ?></LI>
<?php 
    }

    if ($correct) {
	query ("UPDATE AutoId SET DictionaryId=LAST_INSERT_ID(DictionaryId + 1)");
	if ($AFFECTED_ROWS > 0) {
	    $AFFECTED_ROWS= 0;
	    query ("INSERT IGNORE INTO Dictionary SET Id=LAST_INSERT_ID(), IdLanguage='$cLang', Keyword='$cKeyword'");
	    $created= ($AFFECTED_ROWS > 0);
	}
    }
    
    if ($created) { ?>dnl
		<LI><?php  putGS('The keyword $1 has been added.','<B>'.encHTML(decS($cKeyword)).'</B>'); ?></LI>
X_AUDIT(<*91*>, <*getGS('Keyword $1 added',decS($cKeyword))*>)
<?php 
    } else {
    
    if ($correct != 0) { ?>dnl
		<LI><?php  putGS('The keyword could not be added.'); ?><LI></LI><?php  putGS('Please check if the keyword does not already exist.'); ?></LI>
<?php 
    }
}
?>dnl
		*>)
<?php 
    if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*New*>, <*Add another*>, <*X_ROOT/glossary/add.php*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/glossary/*>)
	E_MSGBOX_BUTTONS
<?php  } else { ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/glossary/add.php*>)
	E_MSGBOX_BUTTONS
<?php  } ?>dnl
E_MSGBOX
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
