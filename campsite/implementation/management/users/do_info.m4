B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Changing user account information*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change user account information.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Changing user account information*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?

todef('Name');
todef('Title');
todef('Gender');
todef('Age');
todef('EMail');
todef('City');
todef('StrAddress');
todef('State');
todef('CountryCode');
todef('Phone');
todef('Fax');
todef('Contact');
todef('Phone2');
todef('PostalCode');
todef('Employer');
todef('EmployerType');
todef('Position');
todefnum('User');

    query ("SELECT * FROM Users WHERE Id=$User", 'users');
    if ($NUM_ROWS) {
	fetchRow($users);
    
    ?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<B><? pgetHVar($users,'UName'); ?></B>*>)
E_CURRENT

<?
    $correct= 1;
    $changed= 0;
?>dnl
<P>
B_MSGBOX(<*Changing user account information*>)
	X_MSGBOX_TEXT(<*
<?
    if ($Name == "") {
	$correct= 0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<? }

    if ($correct) {
	query ("UPDATE Users SET Name='$Name', Title='$Title', Gender='$Gender', Age='$Age', EMail='$EMail', City='$City', StrAddress='$StrAddress', State='$State', CountryCode='$CountryCode', Phone='$Phone', Fax='$Fax', Contact='$Contact', Phone2='$Phone2', PostalCode='$PostalCode', Employer='$Employer', EmployerType='$EmployerType', Position='$Position' WHERE Id=$User");
	$changed= $AFFECTED_ROWS;
	if ($changed) { ?>dnl
		<LI><? putGS('User account information has been changed.'); ?></LI>
X_AUDIT(<*56*>, <*getGS('User account information changed for $1',getHVar($users,'UName'))*>)
	<? } else { ?>dnl
		<LI><? putGS('User account information could not be changed.'); ?></LI>
	<? } ?>dnl
<? } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<? if ($changed) { ?>dnl
		<A HREF="X_ROOT/users/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/users/"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such user.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

