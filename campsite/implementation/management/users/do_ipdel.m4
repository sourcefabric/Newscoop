B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting IP Group*>)
<?php  if ($access == 0) { ?>dnl
		X_AD(<*You do not have the right to delete IP Groups.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting IP Group*>)
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
    query ("SELECT (StartIP & 0xff000000) >> 24 as ip0, (StartIP & 0x00ff0000) >> 16 as ip1, (StartIP & 0x0000ff00) >> 8 as ip2, StartIP & 0x000000ff as ip3, Addresses FROM SubsByIP WHERE IdUser=$User and StartIP=$StartIP", 'ig');
    $onlyone= 0;
    if ($NUM_ROWS) {
	fetchRow($ig);
	query ("DELETE FROM SubsByIP WHERE IdUser=$User and StartIP=$StartIP");
	$del= 1;
    } else {
	$del= 0;
    }
?>dnl
<P>
B_MSGBOX(<*Deleting IP Group*>)
<?php  if ($del) { ?>
X_AUDIT(<*58*>, <*getGS('The IP address group $1 has been deleted.',getHVar($ig,'ip0').'.'.getHVar($ig,'ip1').'.'.getHVar($ig,'ip2').'.'.getHVar($ig,'ip3').':'.getHVar($ig,'Addresses') )*>)
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The IP address group $1 has been deleted.','<B>'.getHVar($ig,'ip0').'.'.getHVar($ig,'ip1').'.'.getHVar($ig,'ip2').'.'.getHVar($ig,'ip3').':'.getHVar($ig,'Addresses').'</B>'); ?></LI>*>)
<?php  } else { ?>
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The IP Group could not be deleted.'); ?></LI>*>)
<?php  } ?>
	B_MSGBOX_BUTTONS
<?php  if ($del) { ?>
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/users/ipaccesslist.php?User=<?php  p($User); ?>*>)
<?php  } else { ?>
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/users/ipaccesslist.php?User=<?php  p($User); ?>*>)
<?php  } ?>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No IP Group.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY

E_DATABASE
E_HTML
