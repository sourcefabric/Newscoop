B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageClasses*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new translation*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add glossary infotypes.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Adding new translation*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary infotypes*>, <*infotype/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todef('cName');
    todefnum('cLang');
    todefnum('cId');
    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new translation*>)
	X_MSGBOX_TEXT(<*
<?
    if ($cName == "") {
	$correct= 0; ?>
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Translation').'</B>'); ?></LI>
<? } 

    if ($correct) {
	query ("INSERT IGNORE INTO Classes SET Id=$cId, IdLanguage='$cLang', Name='$cName'");
	$created= ($AFFECTED_ROWS > 0);
    }

    if ($created) { ?>dnl
		<LI><? putGS('The infotype $1 has been added.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
X_AUDIT(<*81*>, <*getGS('Infotype $1 added', decS($cName))*>)
<? } else {
    if ($correct != 0) { ?>dnl
		<LI><? putGS('The infotype could not be added.'); ?><LI></LI><? putGS('Check if the translation does not already exist.'); ?></LI>
<? }
} ?>dnl
		*>)
<? if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*New*>, <*Add another*>, <*X_ROOT/infotype/translate.php?Class=<? print encURL($cId); ?>*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/infotype/*>)
	E_MSGBOX_BUTTONS
<? } else { ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/infotype/translate.php?Class=<? print encURL($cId); ?>*>)
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

