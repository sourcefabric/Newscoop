B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageClasses})

B_HEAD
	X_EXPIRES
	X_TITLE({Translate Class})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add dictionary classes.})
<!sql endif>dnl
<!sql query "SELECT Name FROM Classes WHERE 1=0" c>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Translate Class})
B_HEADER_BUTTONS
X_HBUTTON({Dictionary Classes}, {classes/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault Class 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Classes WHERE Id=?Class" c>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Languages.Id, Languages.Name FROM Languages LEFT JOIN Classes ON Classes.Id = ?Class AND Classes.IdLanguage = Languages.Id WHERE Classes.Id IS NULL ORDER BY Name" languages>
<!sql if $NUM_ROWS>dnl
<P>
B_DIALOG({Translate keyword}, {POST}, {do_translate.xql})
	B_DIALOG_INPUT({Keyword class:})
<!sql set comma 0>dnl
<!sql print_loop c>dnl
<!sql if $comma>, <!sql endif>dnl
<!sql print ~c.Name>dnl
<!sql set comma 1>dnl	
<!sql done>dnl
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Translation:})
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Language:})
		<SELECT NAME="cLang">
			<!sql print_rows languages "<OPTION VALUE="~languages.Id">~languages.Name">
		</SELECT>
		<!sql free q>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="cId" VALUE="<!sql print ~Class>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/classes/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
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
	<LI>No such keyword class.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
