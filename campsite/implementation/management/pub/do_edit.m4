B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Changing publication information*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change publication information.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Pub');
    todef('cName');
    todef('cSite');
    todefnum('cLanguage');
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
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    $correct= 1;
    $created= 0;
    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);
?>dnl

B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
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
	$correct=0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
    <?php  }
    
    if ($cSite == "" || $cSite == " ") {
	$correct= 0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Site').'</B>'); ?></LI>
    <?php  }

    if ($cUnitCost == "" || $cUnitCost == " ") {
	$correct= 0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Unit Cost').'</B>'); ?></LI>
    <?php  }
    
    if ($cCurrency == "" || $cCurrency == " ") {
	$correct= 0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Currency').'</B>'); ?></LI>
    <?php  }
    
    if ($correct) {
	query ("UPDATE Publications SET Name='$cName', Site='$cSite', IdDefaultLanguage=$cLanguage, PayTime='$cPayTime', TimeUnit='$cTimeUnit', UnitCost='$cUnitCost', Currency='$cCurrency', PaidTime='$cPaid', TrialTime='$cTrial' WHERE Id=$Pub");
	$created= ($AFFECTED_ROWS > 0);
    }

    if ($created) { ?>dnl
		<LI><?php  putGS('The publication $1 has been successfuly updated.',"<B>".encHTML(decS($cName))."</B>"); ?></LI>
X_AUDIT(<*3*>, <*getGS('Publication $1 changed',$cName)*>)
<?php  } else {

    if ($correct != 0) { ?>dnl
		<LI><?php  putGS('The publication information could not be updated.'); ?></LI><LI><?php  putGS('Please check if another publication with the same or the same site name does not already exist.'); ?></LI>
<?php  }
    } ?>dnl
		*>)
	B_MSGBOX_BUTTONS
<?php  if ($correct && $created) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/*>)
<?php  } else { ?>
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/edit.php?Pub=<?php  pencURL($Pub); ?>*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
