B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageLanguages*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Updating language information*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to edit languages.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Updating language information*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Languages*>, <*languages/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('Lang');
    
todef('cName');
todef('cCodePage');
todef('cCode');
todef('cOrigName');
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

?>

<P>
B_MSGBOX(<*Updating language information*>)
<?
    query ("SELECT COUNT(*) FROM Languages WHERE Id != $Lang AND Name='$cName'", 'c');
    fetchRowNum($c);
    if (getNumVar($c,0) == 0)
	if ($cName != "") 
	    if ($cCodePage != "")
			query("REPLACE  INTO TimeUnits VALUES ('D', $Lang, '$D'), ('W', $Lang, '$W'), ('M', $Lang, '$M'), ('Y', $Lang, '$Y')");
		query ("UPDATE Languages SET Name='$cName', CodePage='$cCodePage', Code='$cCode', OrigName='$cOrigName', Month1='$cMonth1', Month2='$cMonth2', Month3='$cMonth3', Month4='$cMonth4', Month5='$cMonth5', Month6='$cMonth6', Month7='$cMonth7', Month8='$cMonth8', Month9='$cMonth9', Month10='$cMonth10', Month11='$cMonth11', Month12='$cMonth12', WDay1='$cWDay1', WDay2='$cWDay2', WDay3='$cWDay3', WDay4='$cWDay4', WDay5='$cWDay5', WDay6='$cWDay6', WDay7='$cWDay7' WHERE Id=$Lang");
    if ($AFFECTED_ROWS > 0) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('Language information has been successfuly updated.'); ?></LI>*>)
X_AUDIT(<*103*>, <*getGS('Language $1 modified',$cName)*>)
<? } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('Language information could not be updated.'); ?></LI>
<? if ($cName == "") { ?>dnl
	<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<? } 
    if ($cCodePage == "") { ?>dnl
	<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Code page').'</B>'); ?></LI>
<? }
    if (getNumVar($c,0)) { ?>dnl
	<LI><? putGS('A language with the same name already exists.'); ?></LI>
<? } ?>dnl
	*>)
<? } ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/languages/*>)
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML


