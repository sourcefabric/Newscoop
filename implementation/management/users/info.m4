B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageUsers})

B_HEAD
	X_EXPIRES
	X_TITLE({Change User Account Information})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to change user account information.})
<!sql endif>dnl
<!sql query "SELECT * FROM Countries WHERE 1=0" countries>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Change User Account Information})
B_HEADER_BUTTONS
X_HBUTTON({Users}, {users/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault User 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Users WHERE Id=?User" users>dnl
<!sql if $NUM_ROWS>dnl

B_CURRENT
X_CURRENT({User account:}, {<B><!sql print ~users.UName></B>})
E_CURRENT

<P>
B_DIALOG({Change user account information}, {POST}, {do_info.xql})
	B_DIALOG_INPUT({Full name:})
		<INPUT TYPE="TEXT" NAME="Name" VALUE="<!sql print ~users.Name>" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Title:})
		<SELECT NAME="Title">
		    <OPTION VALUE="Mr."<!sql if (@users.Title == "Mr.")> SELECTED<!sql endif>>Mr.</OPTION>
		    <OPTION VALUE="Mrs."<!sql if (@users.Title == "Mrs.")> SELECTED<!sql endif>>Mrs.</OPTION>
		    <OPTION VALUE="Ms."<!sql if (@users.Title == "Ms.")> SELECTED<!sql endif>>Ms.</OPTION>
		    <OPTION VALUE="Dr."<!sql if (@users.Title == "Dr.")> SELECTED<!sql endif>>Dr.</OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Gender:})
		<INPUT TYPE=RADIO NAME=Gender VALUE="M"<!sql if (@users.Gender == "M")> CHECKED<!sql endif>>Male
		<INPUT TYPE=RADIO NAME=Gender VALUE="F"<!sql if (@users.Gender == "F")> CHECKED<!sql endif>>Female
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Age:})
		<SELECT NAME="Age">
		    <OPTION VALUE="0-17"<!sql if (@users.Age == "0-17")> SELECTED<!sql endif>>under 18</OPTION>
		    <OPTION VALUE="18-24"<!sql if (@users.Age == "18-24")> SELECTED<!sql endif>>18-24</OPTION>
		    <OPTION VALUE="25-39"<!sql if (@users.Age == "25-39")> SELECTED<!sql endif>>25-39</OPTION>
		    <OPTION VALUE="40-49"<!sql if (@users.Age == "40-49")> SELECTED<!sql endif>>40-49</OPTION>
		    <OPTION VALUE="50-65"<!sql if (@users.Age == "50-65")> SELECTED<!sql endif>>50-65</OPTION>
		    <OPTION VALUE="65-"<!sql if (@users.Age == "65-")> SELECTED<!sql endif>>65 or over</OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({E-Mail:})
		<INPUT TYPE="TEXT" NAME="EMail" VALUE="<!sql print ~users.EMail>" SIZE="32" MAXLENGTH="128">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({City:})
		<INPUT TYPE="TEXT" NAME="City" VALUE="<!sql print ~users.City>" SIZE="32" MAXLENGTH="60">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Street Address:})
		<INPUT TYPE="TEXT" NAME="StrAddress" VALUE="<!sql print ~users.StrAddress>" SIZE="50" MAXLENGTH="255">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Postal Code:})
		<INPUT TYPE="TEXT" NAME="PostalCode" VALUE="<!sql print ~users.PostalCode>" SIZE="10" MAXLENGTH="10">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({State:})
		<INPUT TYPE="TEXT" NAME="State" VALUE="<!sql print ~users.State>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Country:})
		<SELECT NAME="CountryCode">
<!sql query "SELECT * FROM Countries where IdLanguage = 1" countries>dnl
		<OPTION VALUE=""<!sql if (@users.CountryCode == "")> SELECTED<!sql endif>>-</OPTION>
<!sql print_loop countries>dnl
		<OPTION VALUE="<!sql print ~countries.Code>"<!sql if (@countries.Code == @users.CountryCode)> SELECTED<!sql endif>><!sql print ~countries.Name></OPTION>
<!sql done>dnl
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Phone:})
		<INPUT TYPE="TEXT" NAME="Phone" VALUE="<!sql print ~users.Phone>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Fax:})
		<INPUT TYPE="TEXT" NAME="Fax" VALUE="<!sql print ~users.Fax>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Contact Person:})
		<INPUT TYPE="TEXT" NAME="Contact" VALUE="<!sql print ~users.Contact>" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Second Phone:})
		<INPUT TYPE="TEXT" NAME="Phone2" VALUE="<!sql print ~users.Phone2>" SIZE="20" MAXLENGTH="20">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Employer:})
		<INPUT TYPE="TEXT" NAME="Employer" VALUE="<!sql print ~users.Employer>" SIZE="30" MAXLENGTH="30">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Employer Type:})
		<SELECT NAME="EmployerType">
		<OPTION VALUE=""<!sql if (@users.EmployerType == "")> SELECTED<!sql endif>></OPTION>
		<OPTION VALUE="Corporate"<!sql if (@users.EmployerType == "Corporate")> SELECTED<!sql endif>>Corporate</OPTION>
		<OPTION VALUE="NGO"<!sql if (@users.EmployerType == "NGO")> SELECTED<!sql endif>>Non-Governmental Organisation</OPTION>
		<OPTION VALUE="Government Agency"<!sql if (@users.EmployerType == "Government Agency")> SELECTED<!sql endif>>Government Agency</OPTION>
		<OPTION VALUE="Academic"<!sql if (@users.EmployerType == "Academic")> SELECTED<!sql endif>>Academic</OPTION>
		<OPTION VALUE="Media"<!sql if (@users.EmployerType == "Media")> SELECTED<!sql endif>>Media</OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Position:})
		<INPUT TYPE="TEXT" NAME="Position" VALUE="<!sql print ~users.Position>" SIZE="30" MAXLENGTH="30">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<!sql print ~User>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/users/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such user account.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
