B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/users*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new IP Group*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add IP address groups.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Adding new IP Group*>)
B_HEADER_BUTTONS
X_HBUTTON(<*IP Access List*>, <*users/ipaccesslist.php?User=<?php  p($User); ?>*>)
X_HBUTTON(<*Users*>, <*users/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
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
<?php 
    if (($cStartIP1 == "") || ($cStartIP2 == "") || ($cStartIP3 == "") || ($cStartIP4 == "")) {
	$correct= 0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Start IP').'</B>'); ?></LI>
    <?php  }

    if ($cAddresses == "") {
	$correct= 0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Number of addresses').'</B>'); ?></LI>
    <?php  }
    
    if ($correct) {
	query ("INSERT IGNORE INTO SubsByIP SET IdUser=$User, StartIP=".($cStartIP1*256*256*256+$cStartIP2*256*256+$cStartIP3*256+$cStartIP4).", Addresses=$cAddresses");
	$created= ($AFFECTED_ROWS > 0);
    }
    
    if ($created) { 
	fetchRowNum($uname);
	?>dnl
		<LI><?php  putGS('The IP Group $1 has been created.','<B>'.encHTML($cStartIP1).'.'.encHTML($cStartIP2).'.'.encHTML($cStartIP3).'.'.encHTML($cStartIP4).':'.encHTML($cAddresses).'</B>'); ?></LI>
X_AUDIT(<*57*>, <*getGS('IP Group $1 added for user $2',encHTML($cStartIP1).'.'.encHTML($cStartIP2).'.'.encHTML($cStartIP3).'.'.encHTML($cStartIP4).':'.encHTML($cAddresses),encHTML(getNumVar($uname,0)))*>)
<?php  } else {

    if ($correct != 0) { ?>dnl
		<LI><?php  putGS('The IP Group could not be created.'); ?><LI></LI><?php  putGS('Please check if an account with the same IP Group does not already exist.'); ?></LI>
<?php  }
}
?>
        *>)

<?php  
if (($correct) && ($created)) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*New*>, <*Add another*>, <*X_ROOT/users/ipadd.php?User=<?php  p($User); ?>*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/users/ipaccesslist.php?User=<?php  p($User); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } else { ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/users/ipaccesslist.php?User=<?php  p($User); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } ?>dnl
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

