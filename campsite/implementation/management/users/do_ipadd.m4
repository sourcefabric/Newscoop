B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new IP Group*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add IP address groups.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Adding new IP Group*>)
B_HEADER_BUTTONS
X_HBUTTON(<*IP Access List*>, <*users/ipaccesslist.php?User=<? p($User); ?>*>)
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todef('User');
    todef('cStartIP1');
    todef('cStartIP2');
    todef('cStartIP3');
    todef('cStartIP4');
    todef('cAddresses');
    todef('UName');

    query ("SELECT Name FROM Users WHERE Id = $User", 'uname');
    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new IP Group*>)
	X_MSGBOX_TEXT(<*
<?
    if (($cStartIP1 == "") || ($cStartIP2 == "") || ($cStartIP3 == "") || ($cStartIP4 == "")) {
	$correct= 0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Start IP').'</B>'); ?></LI>
    <? }

    if ($cAddresses == "") {
	$correct= 0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Number of addresses').'</B>'); ?></LI>
    <? }
    
    if ($correct) {
	query ("INSERT IGNORE INTO SubsByIP SET IdUser=$User, StartIP=".($cStartIP1*256*256*256+$cStartIP2*256*256+$cStartIP3*256+$cStartIP4).", Addresses=$cAddresses");
	$created= ($AFFECTED_ROWS > 0);
    }
    
    if ($created) { 
	fetchRowNum($uname);
	?>dnl
		<LI><? putGS('The IP Group $1 has been created.','<B>'.encHTML($cStartIP1).'.'.encHTML($cStartIP2).'.'.encHTML($cStartIP3).'.'.encHTML($cStartIP4).':'.encHTML($cAddresses).'</B>'); ?></LI>
X_AUDIT(<*57*>, <*getGS('IP Group $1 added for user $2',encHTML($cStartIP1).'.'.encHTML($cStartIP2).'.'.encHTML($cStartIP3).'.'.encHTML($cStartIP4).':'.encHTML($cAddresses),encHTML(getNumVar($uname,0)))*>)
<? } else {

    if ($correct != 0) { ?>dnl
		<LI><? putGS('The IP Group could not be created.'); ?><LI></LI><? putGS('Please check if an account with the same IP Group does not already exist.'); ?></LI>
<? }
}
?>
        *>)

<? 
if (($correct) && ($created)) { ?>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/users/ipadd.php?User=<? p($User); ?>"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another IP Group"></A>
		<A HREF="X_ROOT/users/ipaccesslist.php?User=<? p($User); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<? } else { ?>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/users/ipaccesslist.php?User=<? p($User); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
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

