B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageLanguages*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new language*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add new languages.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Adding new language*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Languages*>, <*languages/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
todef('cName');
todef('cCodePage');
todef('cOrigName');
todef('cCode');
todef('cMonth1');
todef('cMonth2');
todef('cMonth3');
todef('cMonth4');
todef('cMonth5');
todef('cMonth6');
todef('cMonth7');
todef('cMonth8');
todef('cMonth9');
todef('cMonth10');
todef('cMonth11');
todef('cMonth12');
todef('cWDay1');
todef('cWDay2');
todef('cWDay3');
todef('cWDay4');
todef('cWDay5');
todef('cWDay6');
todef('cWDay7');

$correct= 1;
$created= 0;
    ?>dnl
<P>
B_MSGBOX(<*Adding new language*>)
	X_MSGBOX_TEXT(<*
<? if ($cName == "") {
    $correct=0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<? }
   if ($cOrigName == "") {
    $correct= 0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Native name').'</B>'); ?></LI>
<? }
   if ($cCodePage == "") {
    $correct= 0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Code page').'</B>'); ?></LI>
<? }
   if ($correct) {
	query ("INSERT IGNORE INTO Languages SET Name='$cName', CodePage='$cCodePage', Code='$cCode', OrigName='$cOrigName', Month1='$cMonth1', Month2='$cMonth2', Month3='$cMonth3', Month4='$cMonth4', Month5='$cMonth5', Month6='$cMonth6', Month7='$cMonth7', Month8='$cMonth8', Month9='$cMonth9', Month10='$cMonth10', Month11='$cMonth11', Month12='$cMonth12', WDay1='$cWDay1', WDay2='$cWDay2', WDay3='$cWDay3', WDay4='4cWDay4', WDay5='$cWDay5', WDay6='$cWDay6', WDay7='$cWDay7'");
	$created= ($AFFECTED_ROWS > 0);
		query ("SELECT LAST_INSERT_ID()", 'lid');
		fetchRowNum($lid);
		$IdLang = getNumVar($lid,0);
	query("INSERT IGNORE INTO TimeUnits VALUES ('D', $IdLang, '$D'), ('W', $IdLang, '$W'), ('M', $IdLang, '$M'), ('Y', $IdLang, '$Y')");
    }
    if ($created) { ?>dnl
		<LI><? putGS('The language $1 has been successfuly added.','<B>'.decS($cName).'</B>'); ?></LI>
X_AUDIT(<*101*>, <*getGS('Language $1 added',$cName)*>)
    <? } else {
    if ($correct != 0) { ?>dnl
		<LI><? putGS('The language could not be added.'); ?></LI><LI><? putGS('Please check if a language with the same name does not already exist.'); ?></LI>
    <? } 
    } ?>dnl
		*>)
	B_MSGBOX_BUTTONS
<? todef('Back');
    if (($correct) && ($created)) { ?>dnl
		REDIRECT(<*New*>, <*Add another language*>, <*X_ROOT/languages/add.php<? if ($Back) { ?>?Back=<? print encURL($Back); } ?>*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/languages/*>)
<? } else { ?>
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/languages/add.php<? if ($Back) { ?>?Back=<? print encURL($Back); } ?>*>)
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

