B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({DeleteArticle})

B_HEAD
	X_EXPIRES
	X_TITLE({Deleting Article})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to delete articles.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
<!sql setdefault Issue 0>dnl
<!sql setdefault Section 0>dnl
<!sql setdefault Article 0>dnl
<!sql setdefault Language 0>dnl
<!sql setdefault sLanguage 0>dnl
B_HEADER({Deleting Article})
B_HEADER_BUTTONS
X_HBUTTON({Articles}, {pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>&Section=<!sql print #Section>})
X_HBUTTON({Sections}, {pub/issues/sections/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>})
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Articles WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND Number=?Article AND IdLanguage=?sLanguage" q_art>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Sections WHERE IdPublication=?Pub AND NrIssue=?Issue AND IdLanguage=?Language AND Number=?Section" q_sect>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Issues WHERE IdPublication=?Pub AND Number=?Issue AND IdLanguage=?Language" q_iss>dnl
<!sql if $NUM_ROWS>dnl
<!sql query "SELECT * FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl

<!sql query "SELECT Name FROM Languages WHERE Id=?Language" q_lang>dnl
<!sql query "SELECT Name FROM Languages WHERE Id=?sLanguage" q_slang>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
X_CURRENT({Issue:}, {<B><!sql print ~q_iss.Number>. <!sql print ~q_iss.Name> (<!sql print ~q_lang.Name>)</B>})
X_CURRENT({Section:}, {<B><!sql print ~q_sect.Number>. <!sql print ~q_sect.Name></B>})
E_CURRENT

<P>
B_MSGBOX({Delete article})
	X_MSGBOX_TEXT({
<!sql set del 1>dnl
<!sql query "SELECT COUNT(*) FROM Articles WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND Number=?Article" q_nr>dnl
<!sql if @q_nr.0 == "1">dnl
<!sql query "SELECT COUNT(*) FROM Images WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND NrArticle=?Article" q_img>dnl
<!sql if @q_img.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_img.0> images(s) left.</LI>
<!sql endif>dnl
<!sql endif>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql if $del>dnl
<!sql query "DELETE FROM Articles WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND Number=?Article AND IdLanguage=?sLanguage">dnl
<!sql endif>dnl
<!sql if $AFFECTED_ROWS>dnl
<!sql query "DELETE FROM X?q_art.Type WHERE NrArticle=?Article AND IdLanguage=?sLanguage">dnl
<!sql query "DELETE FROM ArticleIndex WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND NrArticle=?Article AND IdLanguage=?sLanguage">dnl
<!sql set del 1>dnl
<!sql else>dnl
<!sql set del 0>dnl
<!sql endif>dnl
<!sql if $del>dnl
		<LI>The article <B><!sql print ~q_art.Name> (<!sql print ~q_slang.Name>)</B> has been deleted.</LI>
X_AUDIT({32}, {Article ?q_art.Name (?q_slang.Name) deleted from ?q_sect.Number. ?q_sect.Name from ?q_iss.Number. ?q_iss.Name (?q_lang.Name) of ?q_pub.Name})
<!sql else>dnl
		<LI>The article <B><!sql print ~q_art.Name> (<!sql print ~q_slang.Name>)</B> could not be deleted.</LI>
<!sql endif>dnl
	})
	B_MSGBOX_BUTTONS
<!sql if $del>dnl
		<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>&Section=<!sql print #Section>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>dnl
		<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>&Section=<!sql print #Section>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such issue.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such section.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such article.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
