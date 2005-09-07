INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues/sections*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSection*>)

B_HEAD
	X_TITLE(<*Adding new section*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add sections.*>)
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
B_HEADER(<*Adding new section*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  p($Pub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
	    fetchRow($q_iss);
	    fetchRow($q_pub);
	    fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
X_CURRENT(<*Issue*>, <*<?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)*>)
E_CURRENT

<?php 
	todef('cName');
	todefnum('cNumber');
	todef('cSubs');
	todef('cShortName');

	$correct= 1;
	$created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new section*>)
	X_MSGBOX_TEXT(<*
<?php 
	if ($cName == "") {
		$correct= 0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<?php  }
	if ($cNumber == "") {
		$correct= 0;
		$cNumber= ($cNumber + 0); ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Number').'</B>'); ?></LI>
<?php  
	}
	if ($cShortName == "" || $cShortName == " ") {
		$correct = 0;
		echo "<LI>" . getGS('You must complete the $1 field.','<B>'.getGS('Short Name').'</B>') . "</LI>\n";
	}
	$ok = valid_short_name($cShortName);
	if ($ok == 0) {
		$correct= 0;
		echo "<LI>" . getGS('The $1 field may only contain letters, digits and underscore (_) character.', '</B>' . getGS('Short Name') . '</B>') . "</LI>\n";
	}
	if ($correct) {
		$sql = "INSERT INTO Sections SET Name='$cName', IdPublication=$Pub, NrIssue=$Issue, IdLanguage=$Language, Number=$cNumber, ShortName='$cShortName'";
		query($sql);
		$created = ($AFFECTED_ROWS >= 0);
	}
	if ($created) {
		## added by sebastian
		if (function_exists ("incModFile"))
			incModFile ();
?>dnl
		<LI><?php  putGS('The section $1 has been successfuly added.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
	<?php 	if ($cSubs != "") {
			$add_subs_res = add_subs_section($Pub, $cNumber);
			if ($add_subs_res == -1) { ?>
				<LI><?php  putGS('Error updating subscriptions.'); ?></LI>
		<?php 	} else { ?>
				<LI><?php  putGS('A total of $1 subscriptions were updated.','<B>'.encHTML(decS($add_subs_res)).'</B>'); ?></LI>
	<?php 		}
		}
	?>
X_AUDIT(<*21*>, <*getGS('Section $1 added to issue $2. $3 ($4) of $5',$cName,getHVar($q_iss,'Number'),getHVar($q_iss,'Name'),getHVar($q_lang,'Name'),getHVar($q_pub,'Name'))*>)
<?php  } else {
    
    if ($correct != 0) { ?>dnl
		<LI><?php  putGS('The section could not be added.'); ?></LI><LI><?php  putGS('Please check if another section with the same number does not already exist.'); ?></LI>
<?php  }
}
?>dnl
		*>)
	B_MSGBOX_BUTTONS
<?php  if ($correct && $created) { ?>dnl
		REDIRECT(<*Add another*>, <*Add another*>, <*X_ROOT/pub/issues/sections/add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
<?php  } else { ?>
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/issues/sections/add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
<?php  } ?>dnl
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
