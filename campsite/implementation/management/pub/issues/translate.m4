B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageIssue})

B_HEAD
	X_EXPIRES
	X_TITLE({Add New Translation})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add issues.})
<!sql endif>dnl
<!sql query "SELECT Name FROM Issues WHERE 1=0" q_iss>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
<!sql setdefault Issue 0>dnl
B_HEADER({Add New Translation})
B_HEADER_BUTTONS
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Issues WHERE IdPublication=?Pub AND Number=?Issue" q_iss>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
E_CURRENT

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Languages.Id, Languages.Name FROM Languages LEFT JOIN Issues ON Issues.IdPublication = ?Pub AND Issues.Number=?Issue AND Issues.IdLanguage = Languages.Id WHERE Issues.IdPublication IS NULL ORDER BY Name" q_lang>dnl
<!sql if $NUM_ROWS>dnl
<P>
B_DIALOG({Add new translation}, {POST}, {do_translate.xql})
	B_DIALOG_INPUT({Issue:})
			<!sql set comma 0>dnl
<!sql print_loop q_iss>dnl
<!sql if $comma>, <!sql endif><!sql print ~q_iss.Name><!sql set comma 1>dnl
<!sql done>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Name:})
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Language:})
		<SELECT NAME="cLang">
		<!sql print_rows q_lang "<OPTION VALUE=\"~q_lang.Id\">~q_lang.Name">
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="cPub" VALUE="<!sql print ~Pub>">
		<INPUT TYPE="HIDDEN" NAME="cNumber" VALUE="<!sql print ~Issue>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/pub/issues/?Pub=<!sql print #Pub>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No more languages.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such issue.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
