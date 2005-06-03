B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Delete IP Group*>)
<?php  if ($access == 0) { ?>dnl
		X_AD(<*You do not have the right to delete IP Groups.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete IP Group*>)
B_HEADER_BUTTONS
X_HBUTTON(<*IP Access List*>, <*users/ipaccesslist.php?User=<?php  p($User); ?>*>)
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php  
    todefnum('User');
    todef('StartIP');
    query ("SELECT (StartIP & 0xff000000) >> 24, (StartIP & 0x00ff0000) >> 16, (StartIP & 0x0000ff00) >> 8, StartIP & 0x000000ff, Addresses FROM SubsByIP WHERE IdUser=$User and StartIP=$StartIP", 'u');
    if ($NUM_ROWS) {
	fetchRowNum($u);
    ?>dnl
<P>
B_MSGBOX(<*Delete IP Group*>)
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Are you sure you want to delete the IP Group $1?','<B>'.getNumVar($u,0).'.'.getNumVar($u,1).'.'.getNumVar($u,2).'.'.getNumVar($u,3).':'.getNumVar($u,4).'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="GET" ACTION="do_ipdel.php">
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<?php  p($User); ?>">
		<INPUT TYPE="HIDDEN" NAME="StartIP" VALUE="<?php  p($StartIP); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/users/ipaccesslist.php?User=<?php  p($User); ?>*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such IP Group.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
