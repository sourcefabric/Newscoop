INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_TITLE(<*Change issue status*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change issues.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?>dnl
B_HEADER(<*Change issue status*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER
<?php 
    query ("SELECT Number, Name, Published FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
	 query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
	    fetchRow($q_iss);
	    fetchRow($q_pub);
	    fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT

<P>
B_MSGBOX(<*Change issue status*>)
	<?php 
	    if (getVar($q_iss,'Published') == "Y") {
		$t2=getGS('Published');
		$t3=getGS('Not published');
	    }
	    else {
		$t2=getGS('Not published');
		$t3=getGS('Published');
	    }
	    ?>
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Are you sure you want to change the issue $1 status from $2 to $3?','<B>'.getHVar($q_iss,'Number').'. '.getHVar($q_iss,'Name').' ('.getHVar($q_lang,'Name').')</B>',"<B>$t2</B>","<B>$t3</B>"); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_status.php">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  pencURL($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  pencHTML($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  pencHTML($Language); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
