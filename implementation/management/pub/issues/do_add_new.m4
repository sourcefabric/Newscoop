INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_TITLE(<*Adding new issue*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add issues.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
	todef('cName');
	todefnum('cNumber');
	todefnum('cLang');
	todefnum('cPub');
	todef('cShortName');
?>dnl
B_HEADER(<*Adding new issue*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  pencURL($cPub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Name FROM Publications WHERE Id=$cPub", 'publ');
    if ($NUM_ROWS) {
	fetchRow($publ);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($publ,'Name'); ?>*>)
E_CURRENT

<P>
B_MSGBOX(<*Adding new issue*>)
	X_MSGBOX_TEXT(<*
<?php 
	$correct = 1;
	$created = 0;
	$cName = trim($cName);
	$cNumber = trim($cNumber);
	if ($cLang == 0) {
		$correct = 0;
		echo "<LI>" . getGS('You must select a language.') . "</LI>\n";
	}
	if ($cName == "" || $cName == " ") {
		$correct = 0;
		echo "<LI>" . getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>') . "</LI>\n";
	}
	if ($cShortName == "" || $cShortName == " ") {
		$correct = 0;
		echo "<LI>" . getGS('You must complete the $1 field.','<B>'.getGS('URL Name').'</B>') . "</LI>\n";
	}
	$ok = valid_short_name($cShortName);
	if ($ok == 0) {
		$correct = 0;
		echo "<LI>" . getGS('The $1 field may only contain letters, digits and underscore (_) character.', '</B>' . getGS('URL Name') . '</B>') . "</LI>\n";
	}
	if ($cNumber == "" || $cNumber == " ") {
		$correct = 0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Number').'</B>'); ?></LI>
<?php
	}
	if ($correct) {
		$sql = "INSERT INTO Issues SET Name='$cName', IdPublication=$cPub, IdLanguage=$cLang, Number=$cNumber, ShortName='".$cShortName."'";
		query($sql);
		$created = ($AFFECTED_ROWS > 0);
	}
	if ($created) {
?>dnl
		<LI><?php  putGS('The issue $1 has been successfuly added.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
		X_AUDIT(<*11*>, <*getGS('Issue $1 added in publication $2',$cName,getVar($publ,'Name'))*>)
<?php  } else {
		if ($correct) { ?>dnl
			<LI><?php  putGS('The issue could not be added.'); ?></LI><LI><?php  putGS('Please check if another issue with the same number/language does not already exist.'); ?></LI>
<?php  }
	}
?>dnl
		*>)
<?php  if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*Another*>, <*Add another*>, <*X_ROOT/pub/issues/add_new.php?Pub=<?php  pencURL($cPub); ?>*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/issues/?Pub=<?php  pencURL($cPub); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } else { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/issues/add_new.php?Pub=<?php  pencURL($cPub); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } ?>dnl
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
