B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Change user account information*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change user account information.*>)
<? }
    
    query ("SELECT * FROM Countries WHERE 1=0", 'countries');
?>dnl
E_HEAD

<? if ($access) { ?>dnl
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

<?
    todefnum('User');
    query ("SELECT * FROM Users WHERE Id=$User", 'users');
    if ($NUM_ROWS) {
	fetchRow($users);
    ?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<B><? pgetHVar($users,'UName'); ?></B>*>)
E_CURRENT

<P>
B_DIALOG(<*Change user account information*>, <*POST*>, <*do_info.php*>)
	B_DIALOG_INPUT(<*Full Name*>)
		<INPUT TYPE="TEXT" NAME="Name" VALUE="<? pgetHVar($users,'Name'); ?>" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Title*>)
		<SELECT NAME="Title">
		    <OPTION VALUE="Mr."<? if(getVar($users,'Title')== "Mr.") { ?> SELECTED<? } ?>><? putGS('Mr.'); ?></OPTION>
		    <OPTION VALUE="Mrs."<? if(getVar($users,'Title')== "Mrs.") { ?> SELECTED<? } ?>><? putGS('Mrs.'); ?></OPTION>
		    <OPTION VALUE="Ms."<? if(getVar($users,'Title')== "Ms.") { ?> SELECTED<? } ?>><? putGS('Ms.'); ?></OPTION>
		    <OPTION VALUE="Dr."<? if(getVar($users,'Title')== "Dr.") { ?> SELECTED<? } ?>><? putGS('Dr.'); ?></OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Gender*>)
		<INPUT TYPE=RADIO NAME=Gender VALUE="M"<? if(getVar($users,'Gender')== "M") { ?> CHECKED<? } ?>><? putGS('Male'); ?>
		<INPUT TYPE=RADIO NAME=Gender VALUE="F"<? if(getVar($users,'Gender')== "F") { ?> CHECKED<? } ?>><? putGS('Female'); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Age*>)
		<SELECT NAME="Age">
		    <OPTION VALUE="0-17"<? if(getVar($users,'Age')== "0-17") { ?> SELECTED<? } ?>><? putGS('under 18'); ?></OPTION>
		    <OPTION VALUE="18-24"<? if(getVar($users,'Age')== "18-24") { ?> SELECTED<? } ?>>18-24</OPTION>
		    <OPTION VALUE="25-39"<? if(getVar($users,'Age')== "25-39") { ?> SELECTED<? } ?>>25-39</OPTION>
		    <OPTION VALUE="40-49"<? if(getVar($users,'Age')== "40-49") { ?> SELECTED<? } ?>>40-49</OPTION>
		    <OPTION VALUE="50-65"<? if(getVar($users,'Age')== "50-65") { ?> SELECTED<? } ?>>50-65</OPTION>
		    <OPTION VALUE="65-"<? if(getVar($users,'Age')== "65-") { ?> SELECTED<? } ?>><? putGS('65 or over'); ?></OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*E-Mail*>)
		<INPUT TYPE="TEXT" NAME="EMail" VALUE="<? pgetHVar($users,'EMail'); ?>" SIZE="32" MAXLENGTH="128">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*City*>)
		<INPUT TYPE="TEXT" NAME="City" VALUE="<? pgetHVar($users,'City'); ?>" SIZE="32" MAXLENGTH="60">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Street Address*>)
		<INPUT TYPE="TEXT" NAME="StrAddress" VALUE="<? pgetHVar($users,'StrAddress'); ?>" SIZE="50" MAXLENGTH="255">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Postal Code*>)
		<INPUT TYPE="TEXT" NAME="PostalCode" VALUE="<? pgetHVar($users,'PostalCode'); ?>" SIZE="10" MAXLENGTH="10">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*State*>)
		<INPUT TYPE="TEXT" NAME="State" VALUE="<? pgetHVar($users,'State'); ?>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Country*>)
		<SELECT NAME="CountryCode">
<?
    query ("SELECT * FROM Countries where IdLanguage = 1", 'countries'); 
    fetchRow($countries);
    ?>dnl
		<OPTION VALUE=""<? if(getVar($users,'CountryCode')== "") { ?> SELECTED<? } ?>>-</OPTION>
<?
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($countries);
	pComboVar(getHVar($countries,'Code'),getHVar($users,'CountryCode'),getHVar($countries,'Name'));
    }
    ?>dnl
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Phone*>)
		<INPUT TYPE="TEXT" NAME="Phone" VALUE="<? pgetHVar($users,'Phone'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Fax*>)
		<INPUT TYPE="TEXT" NAME="Fax" VALUE="<? pgetHVar($users,'Fax'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Contact Person*>)
		<INPUT TYPE="TEXT" NAME="Contact" VALUE="<? pgetHVar($users,'Contact'); ?>" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Second Phone*>)
		<INPUT TYPE="TEXT" NAME="Phone2" VALUE="<? pgetHVar($users,'Phone2'); ?>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Employer*>)
		<INPUT TYPE="TEXT" NAME="Employer" VALUE="<? pgetHVar($users,'Employer'); ?>" SIZE="30" MAXLENGTH="30">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Employer Type*>)
		<SELECT NAME="EmployerType">
		<OPTION VALUE=""<? if(getVar($users,'EmployerType')== "") { ?> SELECTED<? } ?>></OPTION>
		<OPTION VALUE="Corporate"<? if(getVar($users,'EmployerType')== "Corporate") { ?> SELECTED<? } ?>><? putGS('Corporate'); ?></OPTION>
		<OPTION VALUE="NGO"<? if(getVar($users,'EmployerType')== "NGO") { ?> SELECTED<? } ?>><? putGS('Non-Governmental Organisation'); ?></OPTION>
		<OPTION VALUE="Government Agency"<? if(getVar($users,'EmployerType')== "Government Agency") { ?> SELECTED<? } ?>><? putGS('Government Agency'); ?></OPTION>
		<OPTION VALUE="Academic"<? if(getVar($users,'EmployerType')== "Academic") { ?> SELECTED<? } ?>><? putGS('Academic'); ?></OPTION>
		<OPTION VALUE="Media"<? if(getVar($users,'EmployerType')== "Media") { ?> SELECTED<? } ?>><? putGS('Media'); ?></OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Position*>)
		<INPUT TYPE="TEXT" NAME="Position" VALUE="<? pgetHVar($users,'Position'); ?>" SIZE="30" MAXLENGTH="30">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<? pencHTML($User); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/users/*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
