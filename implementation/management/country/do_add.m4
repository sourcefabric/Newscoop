B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageCountries*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new country*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add countries.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<? todefnum('Language'); ?>dnl
B_HEADER(<*Adding new country*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Countries*>, <*country/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? 
    todef('cCode');
    todef('cName');
    todefnum('cLanguage');
    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new country*>)
	X_MSGBOX_TEXT(<*
<? 
    query ("SELECT TRIM('$cCode'), TRIM('$cName')", 'q_var');
    fetchRowNum($q_var);
    if (getNumVar($q_var,0) == "" || getNumVar($q_var,0) == " ") {
	$correct= 0;
	?>dnl
	<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Code').'</B>'); ?></LI>
<? } 
    if (getNumVar($q_var,1) == "" || getNumVar($q_var,1) == " ") {
	$correct=0;
	?>dnl
	<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<? }
    if ($cLanguage == "" || $cLanguage == "0") {
	$correct= 0
        ?>dnl
	<LI><? putGS('You must select a language.'); ?></LI>
<? } 
    if ($correct) { 
	query ("INSERT IGNORE INTO Countries SET Code='$cCode', Name='$cName', IdLanguage=$cLanguage");
	if ($AFFECTED_ROWS > 0)
		$created= 1;
 }
    if ($correct) {
	if ($created) { ?>
	<LI><? putGS('The country $1 has been created','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
X_AUDIT(<*131*>, <*getGS('Country $1 added',$cName)*>)
<? } else { ?>dnl
	<LI><? putGS('The country $1 could not be created','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
<? } ?>dnl
<? } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<? 
    todef('Back');
    if ($created) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/country/*>)
<? } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/country/add.php<? if ($Back != "") { ?>?Back=<? print encURL($Back); } ?>*>)
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

