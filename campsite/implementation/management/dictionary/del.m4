B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({DeleteDictionary})

B_HEAD
	X_EXPIRES
	X_TITLE({Delete Keyword})
<!sql if $access == 0>dnl
		X_AD({You do not have the right to delete keywords.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Delete Keyword})
B_HEADER_BUTTONS
X_HBUTTON({Dictionary}, {dictionary/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault Keyword 0>dnl
<!sql setdefault Language 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Keyword FROM Dictionary WHERE Id=?Keyword AND IdLanguage=?Language" q_dict>dnl
<!sql if $NUM_ROWS>dnl
<!sql query "SELECT COUNT(*) FROM KeywordClasses WHERE IdDictionary=?Keyword AND IdLanguage=?Language" q_kwdcls>dnl
<!sql if @q_kwdcls.0 == 0>dnl
<P>
B_MSGBOX({Delete keyword})
	X_MSGBOX_TEXT({<LI>Are you sure you want to delete the keyword <B><!sql print ~q_dict.Keyword></B>?</LI>})
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.xql">
		<INPUT TYPE="HIDDEN" NAME="Keyword" VALUE="<!sql print ~Keyword>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<!sql print ~Language>">
		<INPUT TYPE="IMAGE" NAME="Yes" SRC="X_ROOT/img/button/yes.gif" BORDER="0">
		<A HREF="X_ROOT/dictionary/"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<!sql else>dnl
<BLOCKQUOTE>
	<LI>You must delete keyword classes first.</LI>
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
