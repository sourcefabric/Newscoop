INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)
CHECK_XACCESS(<*Publish*>)

B_HEAD
	X_TITLE(<*Changing issue status*>)
<?php  if ($access == 0 || $xaccess == 0) { ?>dnl
	X_XAD(<*You do not have the right to change issues.*>, <*pub/issues/?Pub=<?php  p($Pub); ?>&Language=<?php  p($Language); ?> ?>*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access && $xaccess) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?>dnl
B_HEADER(<*Changing issue status*>)
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
B_MSGBOX(<*Changing issue status*>)
<?php 

	$AFFECTED_ROWS= 0;
	query ("UPDATE Issues SET PublicationDate=IF(Published = 'N', NOW(), PublicationDate), Published=IF(Published = 'N', 'Y', 'N') WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language");
	$changed_status = $AFFECTED_ROWS > 0;
	if ($changed_status) {
		if (getVar($q_iss,'Published') == "Y") {
			$t2=getGS('Published');
			$t3=getGS('Not published');
		}
		else {
			$t2=getGS('Not published');
			$t3=getGS('Published');
		} ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Status of the issue $1 has been changed from $2 to $3','<B>'.getHVar($q_iss,'Number').'. '.getHVar($q_iss,'Name').' ('.getHVar($q_lang,'Name').')</B>',"<B>$t2</B>","<B>$t3</B>"); ?></LI>*>)

X_AUDIT(<*14*>, <*getGS('Issue $1 Published: $2  changed status',getVar($q_iss,'Number').'. '.getVar($q_iss,'Name').' ('.getVar($q_lang,'Name').')',getVar($q_iss,'Published'))*>)
<?php  } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Status of the issue $1 could not be changed.','<B>'.getVar($q_iss,'Number').'. '.getVar($q_iss,'Name').' ('.getVar($q_lang,'Name').')</B>'); ?></LI>*>)
<?php  } ?>dnl
	B_MSGBOX_BUTTONS
<?php  
    if ($changed_status) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/issues/status.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Language=<?php  pencURL($Language); ?>*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such publication.'); ?></LI>
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

