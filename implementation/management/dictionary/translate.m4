B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageDictionary})

B_HEAD
	X_EXPIRES
	X_TITLE({Translate Keyword})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add keywords.})
<!sql endif>dnl
<!sql query "SELECT Keyword FROM Dictionary WHERE 1=0" k>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Translate Keyword})
B_HEADER_BUTTONS
X_HBUTTON({Dictionary}, {dictionary/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault Keyword 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Keyword FROM Dictionary WHERE Id=?Keyword ORDER BY IdLanguage" k>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Languages.Id, Languages.Name FROM Languages LEFT JOIN Dictionary ON Dictionary.Id = ?Keyword AND Dictionary.IdLanguage = Languages.Id WHERE Dictionary.Id IS NULL ORDER BY Name" languages>
<!sql if $NUM_ROWS>dnl
<P>
B_DIALOG({Translate keyword}, {POST}, {do_translate.xql})
	B_DIALOG_INPUT({Keyword:})
<!sql set comma 0>dnl
<!sql print_loop k>dnl
<!sql if $comma>, <!sql endif>dnl
<!sql print ~k.Keyword>dnl
<!sql set comma 1>dnl	
<!sql done>dnl
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Translation:})
		<INPUT TYPE="TEXT" NAME="cKeyword" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Language:})
		<SELECT NAME="cLang">
			<!sql print_rows languages "<OPTION VALUE="~languages.Id">~languages.Name">
		</SELECT>
		<!sql free q>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="cId" VALUE="<!sql print ~Keyword>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/dictionary/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
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
	<LI>No such keyword.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
