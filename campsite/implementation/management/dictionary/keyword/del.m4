B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({DeleteDictionary})

B_HEAD
	X_EXPIRES
	X_TITLE({Unlink Class From Keyword})
<!sql if $access == 0>dnl
        X_AD({You do not have the right to unlink classes from keywords.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Keyword 0>dnl
<!sql setdefault Class 0>dnl
<!sql setdefault Language 0>dnl
B_HEADER({Unlink Class From Keyword})
B_HEADER_BUTTONS
X_HBUTTON({Keyword Classes}, {dictionary/keyword/?Keyword=<!sql print #Keyword>&Language=<!sql print #Language>})
X_HBUTTON({Dictionary}, {dictionary/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Keyword FROM Dictionary WHERE Id=?Keyword AND IdLanguage=?Language" q_kwd>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Classes WHERE Id=?Class AND IdLanguage=?Language" q_cls>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Languages WHERE Id=?Language" q_lang>dnl
<!sql if $NUM_ROWS>dnl
B_CURRENT
X_CURRENT({Keyword:}, {<B><B><!sql print ~q_kwd.Keyword></B>})
X_CURRENT({Language:}, {<B><!sql print ~q_lang.Name></B>})
E_CURRENT

<P>
B_MSGBOX({Unlink class from keyword})
	X_MSGBOX_TEXT({<LI>Are you sure you want to unlink the class <B><!sql print ~q_cls.Name></B> from the keyword <B><!sql print ~q_kwd.Keyword></B>?</LI>})
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.xql">
		<INPUT TYPE="HIDDEN" NAME="Keyword" VALUE="<!sql print ~Keyword>">
		<INPUT TYPE="HIDDEN" NAME="Class" VALUE="<!sql print ~Class>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<!sql print ~Language>">
		<INPUT TYPE="IMAGE" NAME="Yes" SRC="X_ROOT/img/button/yes.gif" BORDER="0">
		<A HREF="X_ROOT/dictionary/keyword/?Keyword=<!sql print #Keyword>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such language.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such class.</LI>
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
