B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE
<? todef('cName');
$correct= 1;
$created= 0;
$j= 0;
?>dnl

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageArticleTypes*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new article type*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add article types.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Adding new article type*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Article Types*>, <*a_types/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<P>
B_MSGBOX(<*Adding new article type*>)
	X_MSGBOX_TEXT(<*
<? if ($cName == "") {
    $correct= 0; ?>dnl
	<LI><? putGS('You must complete the $1 field.','</B>'.getGS('Name').'</B>'); ?></LI>
<? } else {
    $cName=decS($cName);
    
    $ok= 1;
    for ($i=0;$i<strlen($cName);$i++) {
	$c = ord ( strtolower ( substr ( $cName,$i,1 ) ) );
	if ($c<97 || $c>122)
	    $ok=0;
    }
    if ($ok == 0) {
	$correct= 0; ?>dnl
	<LI><? putGS('The $1 field may only contain letters.','</B>'.getGS('Name').'</B>'); ?></LI>
    <? }

    $cName=encS($cName);
    if ($correct) {
	query ("SHOW TABLES LIKE 'X$cName'", 't');
	if ($NUM_ROWS) {
	    $correct= 0; ?>dnl
	<LI><? putGS('The article type $1 already exists.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
	<? }
    }
    
    if ($correct) {
	query ("CREATE TABLE X$cName (NrArticle INT UNSIGNED NOT NULL, IdLanguage INT UNSIGNED NOT NULL, PRIMARY KEY(NrArticle, IdLanguage))");
	$created= 1; ?>
	<LI><? putGS('The article type $1 has been added.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
X_AUDIT(<*61*>, <*getGS('The article type $1 has been added.',$cName)*>)
<? }
} ?>dnl
	*>)
<?
    todef ('Back');
    if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*New field*>, <*Add new field*>, <*X_ROOT/a_types/fields/add.php?AType=<? print encURL($cName); ?>*>)
		REDIRECT(<*New type*>, <*Add another*>, <*X_ROOT/a_types/add.php*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/a_types/*>)
	E_MSGBOX_BUTTONS
<? } else { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/a_types/add.php<? if ($Back != "") { ?>?Back=<? print encURL($Back); } ?>*>)
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


