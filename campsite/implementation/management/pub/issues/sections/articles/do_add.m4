B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({AddArticle})

B_HEAD
	X_EXPIRES
	X_TITLE({Adding New Article})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add articles.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
<!sql setdefault Issue 0>dnl
<!sql setdefault Section 0>dnl
<!sql setdefault Language 0>dnl
B_HEADER({Adding New Article})
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
<!sql query "SELECT * FROM Sections WHERE IdPublication=?Pub AND NrIssue=?Issue AND IdLanguage=?Language AND Number=?Section" q_sect>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Issues WHERE IdPublication=?Pub AND Number=?Issue AND IdLanguage=?Language" q_iss>dnl
<!sql if $NUM_ROWS>dnl
<!sql query "SELECT * FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl

<!sql query "SELECT Name FROM Languages WHERE Id=?Language" q_lang>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
X_CURRENT({Issue:}, {<B><!sql print ~q_iss.Number>. <!sql print ~q_iss.Name> (<!sql print ~q_lang.Name>)</B>})
X_CURRENT({Section:}, {<B><!sql print ~q_sect.Number>. <!sql print ~q_sect.Name></B>})
E_CURRENT

<!sql setdefault cName "">dnl
<!sql setdefault cFrontPage "">dnl
<!sql setdefault cSectionPage "">dnl
<!sql setdefault cType "">dnl
<!sql setdefault cLanguage 0>dnl
<!sql setdefault cKeywords "">dnl
<!sql if $cFrontPage == "on"><!sql set cFrontPage "Y"><!sql else><!sql set cFrontPage "N"><!sql endif>dnl
<!sql if $cSectionPage == "on"><!sql set cSectionPage "Y"><!sql else><!sql set cSectionPage "N"><!sql endif>dnl
<!sql set correct 1><!sql set created 0>dnl
<P>
B_MSGBOX({Adding new article})
	X_MSGBOX_TEXT({
<!sql query "SELECT TRIM('?cName'), TRIM('?cType'), '?cLanguage'" q_var>dnl
<!sql if (@q_var.0 == "" || @q_var.0 == " ")>dnl
<!sql set correct 0>dnl
	<LI>You must complete the <B>Name</B> field.</LI>
<!sql endif>dnl
<!sql if (@q_var.1 == "" || @q_var.1 == " ")>dnl
<!sql set correct 0>dnl
	<LI>You must select an article type.</LI>
<!sql endif>dnl
<!sql if (@q_var.2 == "" || @q_var.2 == "0")>dnl
<!sql set correct 0>dnl
	<LI>You must select a language.</LI>
<!sql endif>dnl
<!sql if $correct>dnl
	<!sql query "UPDATE AutoId SET ArticleId=LAST_INSERT_ID(ArticleId + 1)">dnl
	<!sql set AFFECTED_ROWS 0>dnl
	<!sql query "INSERT IGNORE INTO Articles SET IdPublication=?Pub, NrIssue=?Issue, NrSection = ?Section, Number = LAST_INSERT_ID(), IdLanguage=?cLanguage, Type='?cType', Name='?cName', Keywords='?cKeywords', OnFrontPage='?cFrontPage', OnSection='?cSectionPage', UploadDate=NOW(), IdUser=@Usr.Id, Public='Y'">dnl
	<!sql if $AFFECTED_ROWS>dnl
		<!sql set AFFECTED_ROWS 0>dnl
		<!sql query "INSERT IGNORE INTO X?cType SET NrArticle=LAST_INSERT_ID(), IdLanguage=?cLanguage">
		<!sql if $AFFECTED_ROWS>dnl
			<!sql query "SELECT LAST_INSERT_ID()" lii>dnl
			<!sql set created 1>dnl
		<!sql endif>dnl
	<!sql endif>
<!sql endif>dnl
<!sql if $correct>dnl
<!sql if $created>dnl
	<LI>The article <B><!sql print ~cName></B> has been created</LI>
X_AUDIT({31}, {Article ?cName added to ?q_sect.Number. ?q_sect.Name from ?q_iss.Number. ?q_iss.Name of ?q_pub.Name})
<!sql else>dnl
	<LI>The article <B><!sql print ~cName></B> could not be created</LI>
<!sql endif>dnl
<!sql endif>dnl
	})
<!sql if $created>dnl
	X_MSGBOX_TEXT({<LI>Do you want to edit the article?</LI>})
<!sql endif>dnl
	B_MSGBOX_BUTTONS
<!sql if $created>dnl
		<A HREF="X_ROOT/pub/issues/sections/articles/edit.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #lii.0>&Language=<!sql print #Language>&sLanguage=<!sql print #cLanguage>"><IMG SRC="X_ROOT/img/button/yes.gif" BORDER="0" ALT="Yes"></A>
		<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
<!sql else>dnl
<!sql setdefault HTTP_REFERER "">dnl
<!sql if $HTTP_REFERER != "">dnl
		<A HREF="<!sql print $HTTP_REFERER>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql else>dnl
		<A HREF="X_ROOT/pub/issues/sections/articles/add.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
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

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
