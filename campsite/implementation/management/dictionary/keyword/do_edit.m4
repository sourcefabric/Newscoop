B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageDictionary})

B_HEAD
	X_EXPIRES
	X_TITLE({Changing Keyword/Class Definiton})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to change definitions.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Keyword 0>dnl
<!sql setdefault Class 0>dnl
<!sql setdefault Language 0>dnl
<!sql setdefault cDefinition "">dnl
B_HEADER({Changing Keyword/Class Definition})
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
X_CURRENT({Class:}, {<B><B><!sql print ~q_cls.Name></B>})
X_CURRENT({Language:}, {<B><!sql print ~q_lang.Name></B>})
E_CURRENT

<P>
B_MSGBOX({Changing keyword})
	X_MSGBOX_TEXT({
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "UPDATE KeywordClasses SET Definition='?cDefinition' WHERE IdDictionary=?Keyword AND IdClasses=?Class AND IdLanguage=?Language">dnl
<!sql if $AFFECTED_ROWS>dnl
		<LI>The keyword has been changed.</LI>
X_AUDIT({93}, {Keyword ~q_dic.Keyword changed})
<!sql else>dnl
		<LI>The keyword could not be changed.<LI>
<!sql endif>dnl
		})
<!sql if $AFFECTED_ROWS>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/dictionary/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<!sql else>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/dictionary/keyword/?Keyword=<!sql print #Keyword>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
<!sql endif>dnl
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
