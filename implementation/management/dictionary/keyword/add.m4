B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageDictionary})

B_HEAD
	X_EXPIRES
	X_TITLE({Add Keyword Class})
<!sql if $access == 0>dnl
        X_AD({You do not have the right to add keyword classes.})
<!sql endif>dnl
<!sql query "SELECT Id, Name FROM Classes WHERE 1=0" q_cls>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Keyword 0>dnl
<!sql setdefault Language 0>dnl
B_HEADER({Add Keyword Class})
B_HEADER_BUTTONS
X_HBUTTON({Keyword Classes}, {dictionary/keyword/?Keyword=<!sql print #Keyword>&Language=<!sql print #Language>})
X_HBUTTON({Dictionary}, {dictionary/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql query "SELECT Keyword FROM Dictionary WHERE Id=?Keyword AND IdLanguage=?Language" q_dict>dnl
<!sql query "SELECT Name FROM Languages WHERE Id=?Language" q_lang>dnl
B_CURRENT
X_CURRENT({Keyword:}, {<B><!sql print ~q_dict.Keyword></B>})
X_CURRENT({Language}, {<B><!sql print ~q_lang.Name></B>})
E_CURRENT

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Id, Name FROM Classes WHERE IdLanguage=?Language" q_cls>dnl
<!sql if $NUM_ROWS>dnl
<P>
B_DIALOG({Add keyword class}, {POST}, {do_add.xql})
	B_DIALOG_INPUT({Class:})
	    <SELECT NAME="cClass" SIZE="5">
<!sql print_loop q_cls>dnl
<!sql query "SELECT COUNT(*) FROM KeywordClasses WHERE IdDictionary=?Keyword AND IdClasses=?q_cls.Id AND IdLanguage=?Language" q_kwdcls>dnl
<!sql if @q_kwdcls.0 == 0>dnl
	    <OPTION VALUE="<!sql print ~q_cls.Id>"><!sql print ~q_cls.Name>
<!sql endif>dnl
<!sql free q_kwdcls>dnl
<!sql done>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Keyword" VALUE="<!sql print ~Keyword>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<!sql print ~Language>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/dictionary/keyword/?Keyword=<!sql print #Keyword>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No classes available.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
