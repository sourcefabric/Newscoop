B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageClasses*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new infotype*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add glossary infotypes.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Adding new infotype*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary Classes*>, <*infotype/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? todef('cName');
    todefnum('cLang', 0);
    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new keyword infotype*>)
	X_MSGBOX_TEXT(<*
<? if ($cName == "") {
    $correct= 0; ?>
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<? 
    }

    if ($cLang == 0) {
	$correct= 0; ?>
		<LI><? putGS('You must select a language.'); ?></LI>
<?
    }
    
    if ($correct) {
	query ("UPDATE AutoId SET ClassId=LAST_INSERT_ID(ClassId + 1)");
	if ($AFFECTED_ROWS > 0) {
	    $AFFECTED_ROWS = 0;
	    query ("INSERT IGNORE INTO Classes SET Id=LAST_INSERT_ID(), IdLanguage='$cLang', Name='$cName'");
	    $created= ($AFFECTED_ROWS > 0);
	}
    }
    if ($created) { ?>dnl
		<LI><? putGS('The infotype $1 has been added.',"<B>".encHTML(decS($cName))."</B>"); ?></LI>
X_AUDIT(<*81*>, <*getGS('Infotype $1 added', decS($cName))*>)
<? } else {
    if ($correct != 0) { ?>dnl
		<LI><? putGS('The infotype could not be added.');print('</LI><LI>'); putGS('Please check if the infotype does not already exist.'); ?></LI>
<?  }
}
?>dnl
		*>)
<? if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*New*>, <*Add another*>, <*X_ROOT/infotype/add.php*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/infotype/*>)
	E_MSGBOX_BUTTONS
<? } else { ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/infotype/add.php*>)
	E_MSGBOX_BUTTONS
<? } ?>dnl
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
