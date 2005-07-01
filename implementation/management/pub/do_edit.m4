INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_TITLE(<*Changing publication information*>)
<?php
if ($access == 0) {
?>dnl
	X_AD(<*You do not have the right to change publication information.*>)
<?php
}
?>dnl
E_HEAD

<?php
if ($access) {
?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
	todefnum('Pub');
	todef('cName');
	todefnum('cDefaultAlias');
	todefnum('cLanguage');
	todefnum('cURLType');
	todefnum('cPayTime');
	todef('cTimeUnit');
	todef('cUnitCost');
	todef('cCurrency');
	todefnum('cPaid');
	todefnum('cTrial');
?>dnl
B_HEADER(<*Changing publication information*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
	$correct = 1;
	$updated = 0;
	query ("SELECT * FROM Publications WHERE Id = $Pub", 'q_pub');
	if ($NUM_ROWS) { 
		fetchRow($q_pub);
?>dnl

B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT

<P>
B_MSGBOX(<*Changing publication information*>)
	X_MSGBOX_TEXT(<*
<?php 
	$cName=trim($cName);
	$cSite=trim($cSite);
	$cUnitCost=trim($cUnitCost);
	$cCurrency=trim($cCurrency);

	if ($cName == "" || $cName== " ") {
		$correct=0;
?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<?php
	}
	if ($cDefaultAlias == "" || $cSite == " ") {
		$correct= 0;
?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Site').'</B>'); ?></LI>
<?php
	}
	if ($correct) {
		$sql = "UPDATE Publications SET Name = '$cName', IdDefaultAlias = '$cDefaultAlias', "
		     . "IdDefaultLanguage = $cLanguage, IdURLType = '$cURLType', PayTime = '$cPayTime', "
		     . "TimeUnit = '$cTimeUnit', PaidTime = '$cPaid', TrialTime = '$cTrial'";
		if ($cUnitCost != '') {
			$sql .= ", UnitCost = '$cUnitCost' ";
		}
		if ($cCurrency != '') {
			$sql .= ", Currency = '$cCurrency' ";
		}
		$sql .= " WHERE Id=$Pub";
		query($sql);
		$updated = ($AFFECTED_ROWS >= 0);
	}

	if ($updated) {
		$params = array($operation_attr=>$operation_modify, "IdPublication"=>"$Pub" );
		$msg = build_reset_cache_msg($cache_type_publications, $params);
		send_message("127.0.0.1", server_port(), $msg, $err_msg);
?>dnl
		<LI><?php  putGS('The publication $1 has been successfuly updated.', "<B>" 
		                 . encHTML(decS($cName)) . "</B>"); ?></LI>
		X_AUDIT(<*3*>, <*getGS('Publication $1 changed', $cName)*>)
<?php
	} else {
		if ($correct != 0) { ?>dnl
			<LI><?php  putGS('The publication information could not be updated.'); ?></LI>
			<LI><?php  putGS('Please check if another publication with the same or the same site name does not already exist.'); ?></LI>
<?php  }
	}
?>dnl
*>)
	B_MSGBOX_BUTTONS
<?php  if ($correct && $updated) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/*>)
<?php  } else { ?>
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/edit.php?Pub=<?php  pencURL($Pub); ?>*>)
<?php  } ?>dnl
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
