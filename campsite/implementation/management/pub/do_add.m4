B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new publication*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add publications.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Adding new publication*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    todef('cName');
    todef('cSite');
    todefnum('cLanguage');
    todefnum('cPayTime');
    todef('cTimeUnit');
    todef('cUnitCost');
    todef('cCurrency');
    todefnum('cPaid');
    todefnum('cTrial');

    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new publication*>)
	X_MSGBOX_TEXT(<*
<?php 
    $cName=trim($cName);
    $cSite=trim($cSite);
//!sql query "SELECT TRIM('?cName'), TRIM('?cSite')" q_tr>dnl

    if ($cName == "" || $cName == " ") {
	$correct= 0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
    <?php  }
    
    if ($cSite == "" || $cSite == " ") {
	$correct=0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Site').'</B>'); ?></LI>
    <?php  }

    if ($correct) {
	$AFFECTED_ROWS=0;
	query ("INSERT IGNORE INTO Publications SET Name='$cName', Site='$cSite', IdDefaultLanguage=$cLanguage, PayTime='$cPayTime', TimeUnit='$cTimeUnit', UnitCost='$cUnitCost', Currency='$cCurrency', PaidTime='$cPaid', TrialTime='$cTrial'");
	$created= ($AFFECTED_ROWS > 0);
    }

    if ($created) { ?>dnl
		<LI><?php  putGS('The publication $1 has been successfuly added.',"<B>".encHTML(decS($cName))."</B>"); ?></LI>
X_AUDIT(<*1*>, <*getGS('Publication $1 added',$cName)*>)
<?php 
    } else {
	if ($correct != 0) { ?>dnl
		<LI><?php  putGS('The publication could not be added.'); ?></LI><LI><?php  putGS('Please check if another publication with the same or the same site name does not already exist.'); ?></LI>
<?php  }
}
?>dnl
		*>)
<?php  if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*another*>, <*Add another*>, <*X_ROOT/pub/add.php*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/*>)
	E_MSGBOX_BUTTONS
<?php  } else { ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/add.php*>)
	E_MSGBOX_BUTTONS
<?php  } ?>dnl
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
