B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({DeleteDictionary})

B_HEAD
	X_EXPIRES
	X_TITLE({Deleting Keyword})
<!sql if $access == 0>dnl
		X_AD({You do not have the right to delete keywords.})
<!sql query "SELECT 1" s>dnl
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Deleting Keyword})
B_HEADER_BUTTONS
X_HBUTTON({Dictionary}, {dictionary/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault Keyword 0>dnl
<!sql setdefault Language 0>dnl

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Keyword FROM Dictionary WHERE Id=?Keyword AND IdLanguage=?Language" q_dic>dnl
<!sql if $NUM_ROWS>dnl
<!sql query "SELECT COUNT(*) FROM KeywordClasses WHERE IdDictionary=?Keyword AND IdLanguage=?Language" q_kwdcls>dnl
<!sql if @q_kwdcls.0 == 0>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "DELETE FROM Dictionary WHERE Id=?Keyword AND IdLanguage=?Language">dnl
<P>
B_MSGBOX({Deleting keyword})
<!sql if $AFFECTED_ROWS>
	X_MSGBOX_TEXT({<LI>The keyword has been deleted.</LI>})
X_AUDIT({82}, {Keyword ~q_dic.Keyword deleted})
<!sql else>
	X_MSGBOX_TEXT({<LI>The keyword could not be deleted.</LI>})
<!sql endif>
	B_MSGBOX_BUTTONS
<!sql if $AFFECTED_ROWS>
		<A HREF="X_ROOT/dictionary/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>
		<A HREF="X_ROOT/dictionary/"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>
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
