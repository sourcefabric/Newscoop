B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageUsers})

B_HEAD
	X_EXPIRES
	X_TITLE({Add New User Account})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to create user accounts.})
<!sql endif>dnl
<!sql query "SELECT Name, Code FROM Countries WHERE 1=0" countries>dnl
<!sql query "SELECT Name FROM UserTypes WHERE 1=0" q>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Add New User Account})
B_HEADER_BUTTONS
X_HBUTTON({Users}, {users/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault cName "">dnl
<!sql setdefault cTitle "">dnl
<!sql setdefault cGender "">dnl
<!sql setdefault cAge "">dnl
<!sql setdefault cUName "">dnl
<!sql setdefault cPass1 "">dnl
<!sql setdefault cPass2 "">dnl
<!sql setdefault cEMail "">dnl
<!sql setdefault cCity "">dnl
<!sql setdefault cStrAddress "">dnl
<!sql setdefault cState "">dnl
<!sql setdefault cCountryCode "">dnl
<!sql setdefault cPhone "">dnl
<!sql setdefault cFax "">dnl
<!sql setdefault cContact "">dnl
<!sql setdefault cPhone2 "">dnl
<!sql setdefault cPostalCode "">dnl
<!sql setdefault cEmployer "">dnl
<!sql setdefault cEmployerType "">dnl
<!sql setdefault cPosition "">dnl
<!sql setdefault cType "">dnl

define({X_SLCTD}, {<!sql if ("$1" == "$2")>SELECTED<!sql endif>})dnl
define({X_CHKD}, {<!sql if ("$1" == "$2")>CHECKED<!sql endif>})dnl

<P>
B_DIALOG({Add new user account}, {POST}, {do_add.xql})
	B_DIALOG_INPUT({Full name:})
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64" VALUE="<!sql print ~cName>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Title:})
		<SELECT NAME="cTitle">
		<OPTION VALUE="Mr." X_SLCTD({~cTitle}, {Mr.})>Mr.</OPTION>
		<OPTION VALUE="Mrs." X_SLCTD({~cTitle}, {Mrs.})>Mrs.</OPTION>
		<OPTION VALUE="Ms." X_SLCTD({~cTitle}, {Ms.})>Ms.</OPTION>
		<OPTION VALUE="Dr." X_SLCTD({~cTitle}, {Dr.})>Dr.</OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Gender:})
		<INPUT TYPE=RADIO NAME=cGender VALUE="M" X_CHKD({~cGender}, {M})>Male
		<INPUT TYPE=RADIO NAME=cGender VALUE="F" X_CHKD({~cGender}, {F})>Female
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Age:})
		<SELECT NAME="cAge">
		<OPTION VALUE="0-17"  X_SLCTD({x$cAge}, {x0-17})>under 18</OPTION>
		<OPTION VALUE="18-24" X_SLCTD({x$cAge}, {x18-24})>18-24</OPTION>
		<OPTION VALUE="25-39" X_SLCTD({x$cAge}, {x25-39})>25-39</OPTION>
		<OPTION VALUE="40-49" X_SLCTD({x$cAge}, {x40-49})>40-49</OPTION>
		<OPTION VALUE="50-65" X_SLCTD({x$cAge}, {x50-65})>50-65</OPTION>
		<OPTION VALUE="65-"   X_SLCTD({x$cAge}, {x65-})>65 or over</OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({User name:})
		<INPUT TYPE="TEXT" NAME="cUName" SIZE="32" MAXLENGTH="32" VALUE="<!sql print ~cUName>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Password:})
		<INPUT TYPE="PASSWORD" NAME="cPass1" SIZE="32" MAXLENGTH="32" VALUE="<!sql print ~cPass1>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Confirm password:})
		<INPUT TYPE="PASSWORD" NAME="cPass2" SIZE="32" MAXLENGTH="32" VALUE="<!sql print ~cPass2>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({E-Mail:})
		<INPUT TYPE="TEXT" NAME="cEMail" SIZE="32" MAXLENGTH="128" VALUE="<!sql print ~cEMail>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({City:})
		<INPUT TYPE="TEXT" NAME="cCity" SIZE="32" MAXLENGTH="60" VALUE="<!sql print ~cCity>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Street Address:})
		<INPUT TYPE="TEXT" NAME="cStrAddress" SIZE="50" MAXLENGTH="255" VALUE="<!sql print ~cStrAddress>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Postal Code:})
		<INPUT TYPE="TEXT" NAME="cPostalCode" SIZE="10" MAXLENGTH="10" VALUE="<!sql print ~cPostalCode>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({State:})
		<INPUT TYPE="TEXT" NAME="cState" SIZE="32" MAXLENGTH="32" VALUE="<!sql print ~cState>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Country:})
		<SELECT NAME="cCountryCode">
<!sql query "SELECT Name, Code FROM Countries WHERE IdLanguage = 1" countries>dnl
<!sql print_loop countries>dnl
		<OPTION VALUE="<!sql print ~countries.Code>"<!sql if ($countries.Code == $cCountryCode)> SELECTED<!sql endif>><!sql print ~countries.Name></OPTION>
<!sql done>dnl
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Phone:})
		<INPUT TYPE="TEXT" NAME="cPhone" SIZE="20" MAXLENGTH="20" VALUE="<!sql print ~cPhone>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Fax:})
		<INPUT TYPE="TEXT" NAME="cFax" SIZE="20" MAXLENGTH="20" VALUE="<!sql print ~cFax>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Contact Person:})
		<INPUT TYPE="TEXT" NAME="cContact" SIZE="32" MAXLENGTH="64" VALUE="<!sql print ~cContact>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Second Phone:})
		<INPUT TYPE="TEXT" NAME="cPhone2" SIZE="20" MAXLENGTH="20" VALUE="<!sql print ~cPhone2>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Employer:})
		<INPUT TYPE="TEXT" NAME="cEmployer" SIZE="30" MAXLENGTH="30" VALUE="<!sql print ~cEmployer>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Employer Type:})
		<SELECT NAME="cEmployerType">
		<OPTION></OPTION>
		<OPTION VALUE="Corporate" X_SLCTD({$cEmployerType}, {Corporate})>Corporate</OPTION>
		<OPTION VALUE="NGO" X_SLCTD({$cEmployerType}, {NGO})>Non-Governmental Organisation</OPTION>
		<OPTION VALUE="Government Agency" X_SLCTD({$cEmployerType}, {Covernment Agency})>Government Agency</OPTION>
		<OPTION VALUE="Academic" X_SLCTD({$cEmployerType}, {Academic})>Academic</OPTION>
		<OPTION VALUE="Media" X_SLCTD({$cEmployerType}, {Media})>Media</OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Position:})
		<INPUT TYPE="TEXT" NAME="cPosition" SIZE="30" MAXLENGTH="30" VALUE="<!sql print ~cPosition>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Type:})
		<!sql query "SELECT Name FROM UserTypes ORDER BY Name" q>
		<SELECT NAME="cType">
			<!sql print_loop q>
			    <OPTION X_SLCTD({~cType}, {~q.Name})><!sql print ~q.0>
			<!sql done>
		</SELECT>
		<!sql free q>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<!sql setdefault Back "">dnl
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<!sql print ~Back>">
<!sql if $Back != "">dnl
		<A HREF="<!sql print $Back>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<!sql else>dnl
		<A HREF="X_ROOT/users/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<!sql endif>dnl
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
