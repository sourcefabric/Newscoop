B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting user account*>)
<? if ($access == 0) { ?>dnl
		X_AD(<*You do not have the right to delete user accounts.*>)
<? }
    query ("SELECT Id FROM Subscriptions WHERE 1=0", 's');
    ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting user account*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('User');
    query ("SELECT UName FROM Users WHERE Id=$User", 'uu');
    if ($NUM_ROWS) {
	fetchRow($uu);
	$del= 1;
	query ("DELETE FROM Users WHERE Id=$User");
	if ($AFFECTED_ROWS > 0) {
	    query ("DELETE FROM UserPerm WHERE IdUser=$User");
	    query ("SELECT Id FROM Subscriptions WHERE IdUser=$User", 's');
	    $nr=$NUM_ROWS;
	    for($loop=0;$loop<$nr;$loop++) {
		fetchRowNum($s);
		query ("DELETE FROM SubsSections WHERE IdSubscription=".encS(getNumVar($s,0)) );
	    }
	    
	    query ("DELETE FROM Subscriptions WHERE IdUser=$User");
	    query ("DELETE FROM SubsByIP WHERE IdUser=$User");
	} else {
	    $del= 0;
	}
	?>dnl
<P>
B_MSGBOX(<*Deleting user account*>)
<? if ($del) { ?>
X_AUDIT(<*52*>, <*getGS('The user account $1 has been deleted.',getHVar($uu,'UName'))*>)
	X_MSGBOX_TEXT(<*<LI><? putGS('The user account $1 has been deleted.','<B>'.getHVar($uu,'UName').'</B>'); ?></LI>*>)
<? } else { ?>
	X_MSGBOX_TEXT(<*<LI><? putGS('The user account $1 could not be deleted.','<B>'.getHVar($uu,'UName').'</B>'); ?></LI>*>)
<? } ?>
	B_MSGBOX_BUTTONS
<? if ($del) { ?>
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/users/*>)
<? } else { ?>
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/users/*>)
<? } ?>
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
