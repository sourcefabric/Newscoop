B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageCountries*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Changing country name*>)
<? if ($access == 0) { ?>dnl
	    X_AD(<*You do not have the right to change country names.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todef('cName');
    todefnum('Language');
    todef('Code');
?>dnl
B_HEADER(<*Changing country name*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Countries*>, <*country/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT * FROM Countries WHERE IdLanguage=$Language AND Code='$Code'", 'q_country');
    if ($NUM_ROWS) {
	$correct= 1; ?>dnl
<P>
B_MSGBOX(<*Changing country name*>)
	X_MSGBOX_TEXT(<*
<?
    if (trim($cName) == "" || trim($cName) == " ") {
	$correct= 0; ?>dnl
	<LI>You must complete the <B>Name</B> field.</LI>
<? } 

    if ($correct) {
	query ("SELECT COUNT(*) FROM Countries WHERE Name = '$cName' AND IdLanguage = $Language", 'q_cnt');
	fetchRowNum($q_cnt);
	if (getNumVar($q_cnt,0) == 0)
	    query ("UPDATE Countries SET Name = '$cName' WHERE Code='$Code' AND IdLanguage = $Language");
	else
	    $AFFECTED_ROWS= 0;
    
    if ($AFFECTED_ROWS > 0) { ?>dnl
	<LI><? putGS('The country name $1 has been changed','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
X_AUDIT(<*133*>, <*getGS('Country name $1 changed',$cName)*>)
<? } else { ?>dnl
	<LI><? putGS('The country name $1 could not be changed','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
<? } 
 } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<? if ($AFFECTED_ROWS > 0) { ?>dnl
		REDIRECT(<*No*>, <*OK*>, <*X_ROOT/country/*>)
<? } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/country/edit.php?Code=<? print encURL(decS($Code)); ?>&Language=<? print encHTML($Language); ?>*>)
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such country name.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

