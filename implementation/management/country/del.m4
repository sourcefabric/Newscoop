B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({DeleteCountries})

B_HEAD
	X_EXPIRES
	X_TITLE({Delete Country})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to delete countries.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Code "">dnl
<!sql setdefault Language 0>dnl
B_HEADER({Delete Article})
B_HEADER_BUTTONS
X_HBUTTON({Countries}, {country/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Countries WHERE Code='?Code' AND IdLanguage=?Language" q_ctr>dnl
<!sql if $NUM_ROWS>dnl

<!sql query "SELECT Name FROM Languages WHERE Id=?Language" q_lang>dnl

<P>
B_MSGBOX({Delete country})
	X_MSGBOX_TEXT({<LI>Are you sure you want to delete the country <B><!sql print ~q_ctr.Name> (<!sql print ~q_lang.Name>)</B>?</LI>})
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.xql">
		<INPUT TYPE="HIDDEN" NAME="Code" VALUE="<!sql print #Code>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<!sql print #Language>">
		<INPUT TYPE="IMAGE" NAME="Yes" SRC="X_ROOT/img/button/yes.gif" BORDER="0">
		<A HREF="X_ROOT/country/"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such country.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
