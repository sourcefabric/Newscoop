INCLUDE_PHP_LIB(<*$ADMIN_DIR/users*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_TITLE(<*Change user account information*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change user account information.*>)
<?php  }
    
    query ("SELECT * FROM Countries WHERE 1=0", 'countries');
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Change user account information*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    todefnum('User');
    query ("SELECT * FROM Users WHERE Id=$User", 'users');
    if ($NUM_ROWS) {
	fetchRow($users);
    ?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<B><?php  pgetHVar($users,'UName'); ?></B>*>)
E_CURRENT

<P>
B_DIALOG(<*Change user account information*>, <*POST*>, <*do_info.php*>)
	B_DIALOG_INPUT(<*Full Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="Name" VALUE="<?php  pgetHVar($users,'Name'); ?>" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Title*>)
		<SELECT NAME="Title">
		    <OPTION VALUE="Mr."<?php  if(getVar($users,'Title')== "Mr.") { ?> SELECTED<?php  } ?>><?php  putGS('Mr.'); ?></OPTION>
		    <OPTION VALUE="Mrs."<?php  if(getVar($users,'Title')== "Mrs.") { ?> SELECTED<?php  } ?>><?php  putGS('Mrs.'); ?></OPTION>
		    <OPTION VALUE="Ms."<?php  if(getVar($users,'Title')== "Ms.") { ?> SELECTED<?php  } ?>><?php  putGS('Ms.'); ?></OPTION>
		    <OPTION VALUE="Dr."<?php  if(getVar($users,'Title')== "Dr.") { ?> SELECTED<?php  } ?>><?php  putGS('Dr.'); ?></OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Gender*>)
		<INPUT TYPE=RADIO NAME=Gender VALUE="M"<?php  if(getVar($users,'Gender')== "M") { ?> CHECKED<?php  } ?>><?php  putGS('Male'); ?>
		<INPUT TYPE=RADIO NAME=Gender VALUE="F"<?php  if(getVar($users,'Gender')== "F") { ?> CHECKED<?php  } ?>><?php  putGS('Female'); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Age*>)
		<SELECT NAME="Age">
		    <OPTION VALUE="0-17"<?php  if(getVar($users,'Age')== "0-17") { ?> SELECTED<?php  } ?>><?php  putGS('under 18'); ?></OPTION>
		    <OPTION VALUE="18-24"<?php  if(getVar($users,'Age')== "18-24") { ?> SELECTED<?php  } ?>>18-24</OPTION>
		    <OPTION VALUE="25-39"<?php  if(getVar($users,'Age')== "25-39") { ?> SELECTED<?php  } ?>>25-39</OPTION>
		    <OPTION VALUE="40-49"<?php  if(getVar($users,'Age')== "40-49") { ?> SELECTED<?php  } ?>>40-49</OPTION>
		    <OPTION VALUE="50-65"<?php  if(getVar($users,'Age')== "50-65") { ?> SELECTED<?php  } ?>>50-65</OPTION>
		    <OPTION VALUE="65-"<?php  if(getVar($users,'Age')== "65-") { ?> SELECTED<?php  } ?>><?php  putGS('65 or over'); ?></OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*E-Mail*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="EMail" VALUE="<?php  pgetHVar($users,'EMail'); ?>" SIZE="32" MAXLENGTH="128">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*City*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="City" VALUE="<?php  pgetHVar($users,'City'); ?>" SIZE="32" MAXLENGTH="60">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Street Address*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="StrAddress" VALUE="<?php  pgetHVar($users,'StrAddress'); ?>" SIZE="50" MAXLENGTH="255">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Postal Code*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="PostalCode" VALUE="<?php  pgetHVar($users,'PostalCode'); ?>" SIZE="10" MAXLENGTH="10">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*State*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="State" VALUE="<?php  pgetHVar($users,'State'); ?>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Country*>)
		<SELECT NAME="CountryCode">
<?php 
    query ("SELECT * FROM Countries where IdLanguage = 1", 'countries'); 
    fetchRow($countries);
    ?>dnl
		<OPTION VALUE=""<?php  if(getVar($users,'CountryCode')== "") { ?> SELECTED<?php  } ?>>-</OPTION>
<?php 
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($countries);
	pComboVar(getHVar($countries,'Code'),getHVar($users,'CountryCode'),getHVar($countries,'Name'));
    }
    ?>dnl
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Phone*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="Phone" VALUE="<?php  pgetHVar($users,'Phone'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Fax*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="Fax" VALUE="<?php  pgetHVar($users,'Fax'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Contact Person*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="Contact" VALUE="<?php  pgetHVar($users,'Contact'); ?>" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Second Phone*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="Phone2" VALUE="<?php  pgetHVar($users,'Phone2'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Employer*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="Employer" VALUE="<?php  pgetHVar($users,'Employer'); ?>" SIZE="30" MAXLENGTH="30">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Employer Type*>)
		<SELECT NAME="EmployerType">
		<OPTION VALUE=""<?php  if(getVar($users,'EmployerType')== "") { ?> SELECTED<?php  } ?>></OPTION>
		<OPTION VALUE="Corporate"<?php  if(getVar($users,'EmployerType')== "Corporate") { ?> SELECTED<?php  } ?>><?php  putGS('Corporate'); ?></OPTION>
		<OPTION VALUE="NGO"<?php  if(getVar($users,'EmployerType')== "NGO") { ?> SELECTED<?php  } ?>><?php  putGS('Non-Governmental Organisation'); ?></OPTION>
		<OPTION VALUE="Government Agency"<?php  if(getVar($users,'EmployerType')== "Government Agency") { ?> SELECTED<?php  } ?>><?php  putGS('Government Agency'); ?></OPTION>
		<OPTION VALUE="Academic"<?php  if(getVar($users,'EmployerType')== "Academic") { ?> SELECTED<?php  } ?>><?php  putGS('Academic'); ?></OPTION>
		<OPTION VALUE="Media"<?php  if(getVar($users,'EmployerType')== "Media") { ?> SELECTED<?php  } ?>><?php  putGS('Media'); ?></OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Position*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="Position" VALUE="<?php  pgetHVar($users,'Position'); ?>" SIZE="30" MAXLENGTH="30">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<?php  pencHTML($User); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/users/*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
