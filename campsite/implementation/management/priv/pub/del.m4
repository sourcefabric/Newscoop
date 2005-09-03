INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeletePub*>)

B_HEAD
	X_TITLE(<*Delete publication*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete publications.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete publication*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    todefnum('Pub');
    query ("SELECT * FROM Publications WHERE Id=$Pub", 'p');
    if ($NUM_ROWS) {
	fetchRow($p);
?>dnl
<P>
B_MSGBOX(<*Delete publication*>)
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Are you sure you want to delete the publication $1?','<B>'.getHVar($p,'Name').'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  pencHTML($Pub); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*NO*>, <*No*>, <*X_ROOT/pub/*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
