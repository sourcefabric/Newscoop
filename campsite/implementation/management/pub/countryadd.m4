INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_TITLE(<*Add new country default subscription time*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to manage publications.*>)
<?php  }
    query ("SELECT Code, Name FROM Countries WHERE 1=0", 'q_ctr');
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Language', 1);
    todefnum('Pub');
?>
B_HEADER(<*Add new country default subscription time*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*pub/deftime.php?Pub=<?php  pencHTML($Pub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) {
	fetchRow($q_pub);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT
 
<P>
B_DIALOG(<*Add new country default subscription time*>, <*POST*>, <*do_countryadd.php*>)
	<INPUT TYPE=HIDDEN NAME=cPub VALUE="<?php  pencHTML($Pub); ?>">
	B_DIALOG_INPUT(<*Country*>)
	    <SELECT NAME="cCountryCode" class="input_select">
<?php 
    query ("SELECT Code, Name FROM Countries WHERE IdLanguage = $Language", 'q_ctr');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) { 
	fetchRow($q_ctr);
	query ("SELECT * FROM SubsDefTime WHERE CountryCode = '".getSVar($q_ctr,'Code')."' AND IdPublication=$Pub", 'q_subs');
	if ($NUM_ROWS == 0) { ?>
	    <OPTION VALUE="<?php  pgetHVar($q_ctr,'Code'); ?>"><?php  pgetHVar($q_ctr,'Name'); ?>dnl
	<?php  }
    }
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Trial Period*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cTrialTime" VALUE="1" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Paid Period*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cPaidTime" VALUE="1" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/deftime.php?Pub=<?php  pencURL($Pub); ?>&Language=<?php  pencURL($Language); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
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
