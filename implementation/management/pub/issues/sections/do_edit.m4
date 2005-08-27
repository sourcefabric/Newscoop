INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues/sections*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSection*>)

B_HEAD
	X_TITLE(<*Updating section name*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to modify sections.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('Language');
	todefnum('cSectionTplId');
	todefnum('cArticleTplId');
	todef('cShortName');
	todef('cSubs');
?>dnl

B_HEADER(<*Updating section name*>)
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
    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Updating section name*>)
	X_MSGBOX_TEXT(<*
<?php 
    if ($cName == "") { ?>dnl
<?php  $correct= 0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<?php  }
	if ($cShortName == "" || $cShortName == " ") {
		$correct = 0;
		echo "<LI>" . getGS('You must complete the $1 field.','<B>'.getGS('URL Name').'</B>') . "</LI>\n";
	}
	$ok = valid_short_name($cShortName);
	if ($ok == 0) {
		$correct= 0;
		echo "<LI>" . getGS('The $1 field may only contain letters, digits and underscore (_) character.', '</B>' . getGS('URL Name') . '</B>') . "</LI>\n";
	}
	if ($correct) {
		$sql = "UPDATE Sections SET Name='$cName'";
		$sql .= ", SectionTplId = " . ($cSectionTplId > 0 ? $cSectionTplId : "NULL");
		$sql .= ", ArticleTplId = " . ($cArticleTplId > 0 ? $cArticleTplId : "NULL");
		$sql .= ", ShortName = '" . $cShortName . "'";
		$sql .= " WHERE IdPublication=$Pub AND NrIssue=$Issue AND Number=$Section AND IdLanguage=$Language";
		query($sql);
		$created= ($AFFECTED_ROWS >= 0);

		## added by sebastian
		if (function_exists ("incModFile"))
			incModFile ();

		if ($cSubs == "a") {
			$add_subs_res = add_subs_section($Pub, $Section);
			if ($add_subs_res == -1) { ?>
				<LI><?php  putGS('Error updating subscriptions.'); ?></LI>
		<?php 	} else { ?>
				<LI><?php  putGS('A total of $1 subscriptions were updated.','<B>'.encHTML(decS($add_subs_res)).'</B>'); ?></LI>
	<?php 		}
		}
		if ($cSubs == "d") {
			$del_subs_res = del_subs_section($Pub, $Section);
			if ($del_subs_res == -1) { ?>
				<LI><?php  putGS('Error updating subscriptions.'); ?></LI>
		<?php 	} else { ?>
				<LI><?php  putGS('A total of $1 subscriptions were updated.','<B>'.encHTML(decS($del_subs_res)).'</B>'); ?></LI>
	<?php 		}
		}
    }

    if ($created) { ?>dnl
		<LI><?php  putGS('The section $1 has been successfuly modified.', '<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
X_AUDIT(<*21*>, <*getGS('Section $1 updated to issue $2. $3 ($4) of $5',$cName,getHVar($q_iss,'Number'),getHVar($q_iss,'Name'),getHVar($q_lang,'Name'),getHVar($q_pub,'Name') )*>)
<?php  } else {
    if ($correct != 0) { ?>dnl
		<LI><?php  putGS('The section could not be changed.'); ?></LI>
<!--LI><?php  putGS('Please check if another section with the same number does not already exist.'); ?></LI-->
<?php  }
}
?>dnl
		*>)
	B_MSGBOX_BUTTONS
<?php 
    if ($correct && $created) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
<?php  } else { ?>
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/issues/sections/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
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

