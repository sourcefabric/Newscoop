B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageCountries})

B_HEAD
	X_EXPIRES
	X_TITLE({Translate Country Name})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to translate country names.})
<!sql endif>dnl
<!sql query "SELECT Name FROM Countries WHERE 1=0" q_clist>dnl
<!sql query "SELECT Id, Name FROM Languages WHERE 1=0" q_lang>dnl
E_HEAD

B_STYLE
E_STYLE

<!sql if $access>dnl
B_BODY

<!sql setdefault Code "">dnl
<!sql setdefault Language 0>dnl
B_HEADER({Translate Country Name})
B_HEADER_BUTTONS
X_HBUTTON({Countries}, {country/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Countries WHERE Code = '$Code' and IdLanguage = $Language" q_country>dnl
<!sql if $NUM_ROWS>dnl

<P>
B_DIALOG({Translate country name}, {POST}, {do_translate.xql})
	B_DIALOG_INPUT({Country:})
<!sql query "SELECT Name FROM Countries WHERE Code='?Code'" q_clist>dnl
<!sql set comma 0>dnl
<!sql print_loop q_clist>dnl
<!sql if $comma>, <!sql else><!sql set comma 1><!sql endif><!sql print ~q_clist.Name>dnl
<!sql done>dnl
<!sql free q_clist>dnl
	B_DIALOG_INPUT({Name:})
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Language:})
		<SELECT NAME="cLanguage">
<!sql query "SELECT Id, Name FROM Languages ORDER BY Name" q_lang>dnl
<!sql print_loop q_lang>dnl
<!sql query "SELECT COUNT(*) FROM Countries WHERE Code='?Code' AND IdLanguage=?q_lang.Id" q_xc>dnl
<!sql if @q_xc.0 == 0>dnl
			<OPTION VALUE="<!sql print ~q_lang.Id>"><!sql print ~q_lang.Name>
<!sql endif>dnl
<!sql free q_xc>dnl
<!sql done>dnl
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE=HIDDEN NAME=cCode VALUE="<!sql print #Code>">
		<INPUT TYPE=HIDDEN NAME=Language VALUE="<!sql print #Language>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/country/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such country name.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
