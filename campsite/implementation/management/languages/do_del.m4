B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({DeleteLanguages})

B_HEAD
	X_EXPIRES
	X_TITLE({Deleting Language})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to delete languages.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Deleting Language})
B_HEADER_BUTTONS
X_HBUTTON({Languages}, {languages/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault Language 0>dnl

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Languages WHERE Id=?Language" q_lang>dnl
<!sql if $NUM_ROWS>dnl

<P>
B_MSGBOX({Deleting language})
	X_MSGBOX_TEXT({
<!sql set del 1>dnl
<!sql query "SELECT COUNT(*) FROM Publications WHERE IdDefaultLanguage=?Language" q_pub>dnl
<!sql if @q_pub.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_pub.0> publication(s) left.</LI>
<!sql endif>dnl
<!sql query "SELECT COUNT(*) FROM Issues WHERE IdLanguage=?Language" q_iss>dnl
<!sql if @q_iss.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_iss.0> issues(s) left.</LI>
<!sql endif>dnl
<!sql query "SELECT COUNT(*) FROM Sections WHERE IdLanguage=?Language" q_sect>dnl
<!sql if @q_sect.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_sect.0> section(s) left.</LI>
<!sql endif>dnl
<!sql query "SELECT COUNT(*) FROM Articles WHERE IdLanguage=?Language" q_art>dnl
<!sql if @q_art.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_art.0> articles(s) left.</LI>
<!sql endif>dnl
<!sql query "SELECT COUNT(*) FROM Dictionary WHERE IdLanguage=?Language" q_kwd>dnl
<!sql if @q_kwd.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_kwd.0> keywords(s) left.</LI>
<!sql endif>dnl
<!sql query "SELECT COUNT(*) FROM Classes WHERE IdLanguage=?Language" q_cls>dnl
<!sql if @q_cls.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_cls.0> classes(s) left.</LI>
<!sql endif>dnl
<!sql query "SELECT COUNT(*) FROM Countries WHERE IdLanguage=?Language" q_country>dnl
<!sql if @q_country.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_country.0> countries left.</LI>
<!sql endif>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql if $del>dnl
<!sql query "DELETE FROM Languages WHERE Id=?Language">dnl
<!sql endif>dnl
<!sql if $AFFECTED_ROWS>
	<!sql query "DELETE FROM TimeUnits WHERE IdLanguage=?Language">
	<!sql if $AFFECTED_ROWS = 0>
		<LI>The language <B><!sql print ~q_lang.Name></B> could not be deleted.</LI>
	<!sql else>
		<LI>The language <B><!sql print ~q_lang.Name></B> has been deleted.</LI>
X_AUDIT({102}, {Language ~q_lang.Name deleted})
	<!sql endif>
<!sql else>
		<LI>The language <B><!sql print ~q_lang.Name></B> could not be deleted.</LI>
<!sql endif>
	})
	B_MSGBOX_BUTTONS
<!sql if $AFFECTED_ROWS>
		<A HREF="X_ROOT/languages/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>
		<A HREF="X_ROOT/languages/"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such language</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
