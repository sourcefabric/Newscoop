B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Adding New Translation})
<!sql if $access == 0>dnl
	X_LOGOUT
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
<!sql setdefault Article 0>dnl
B_HEADER({Adding New Translation})
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
<!sql query "SELECT * FROM Articles WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND Number=?Article" q_art>dnl
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
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
X_CURRENT({Issue:}, {<B><!sql print ~q_iss.Number>. <!sql print ~q_iss.Name> (<!sql print ~q_lang.Name>)</B>})
X_CURRENT({Section:}, {<B><!sql print ~q_sect.Number>. <!sql print ~q_sect.Name></B>})
E_CURRENT
<!sql free q_lang>dnl

CHECK_XACCESS({ChangeArticle})
<!sql query "SELECT (?xaccess != 0) or ((?q_art.IdUser = ?Usr.Id) and ('?q_art.Published' = 'N'))" q_xperm>dnl
<!sql if @q_xperm.0>dnl
<!sql setdefault cName "">dnl
<!sql setdefault cOnFrontPage "">dnl
<!sql setdefault cOnSection "">dnl
<!sql setdefault cPublic "">dnl
<!sql setdefault cType "">dnl
<!sql setdefault cLanguage "">dnl
<!sql setdefault cKeywords "">dnl
<!sql set correct 1><!sql set created 0>dnl
<P>
B_MSGBOX({Adding new translation})
	X_MSGBOX_TEXT({
<!sql query "SELECT TRIM('?cName'), TRIM('?cType')" q_var>dnl
<!sql if (@q_var.0 == "" || @q_var.0 == " ")>dnl
<!sql set correct 0>dnl
	<LI>You must complete the <B>Name</B> field.</LI>
<!sql endif>dnl
<!sql if (@q_var.1 == "" || @q_var.1 == " ")>dnl
<!sql set correct 0>dnl
	<LI>You must select an article type.</LI>
<!sql endif>dnl
<!sql if $correct>dnl
<!sql if $cLanguage == ""><!sql set cLanguage 0><!sql endif>dnl
<!sql query "SELECT COUNT(*) FROM Languages WHERE Id=?cLanguage" q_xlang>dnl
<!sql if @q_xlang.0 == 0>dnl
<!sql set correct 0>dnl
	<LI>You must select a language.</LI>
<!sql endif>dnl
<!sql endif>dnl
<!sql if $correct>dnl
	<!sql set AFFECTED_ROWS 0>dnl
	<!sql query "INSERT IGNORE INTO Articles SET IdPublication=?Pub, NrIssue=?Issue, NrSection = ?Section, Number = ?Article, IdLanguage = ?cLanguage, Type = '?cType', Name = '?cName', Keywords = '?cKeywords', OnFrontPage = '?cOnFrontPage', OnSection = '?cOnSection', UploadDate = NOW(), IdUser = @Usr.Id, Public = '?cPublic'">dnl
	<!sql if $AFFECTED_ROWS>dnl
		<!sql set AFFECTED_ROWS 0>dnl
		<!sql query "INSERT IGNORE INTO X?cType SET NrArticle=?Article, IdLanguage=?cLanguage">
		<!sql if $AFFECTED_ROWS>dnl
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
<!sql setdefault Back "">dnl
<!sql if $created>dnl
		<A HREF="X_ROOT/pub/issues/sections/articles/edit.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #cLanguage>"><IMG SRC="X_ROOT/img/button/yes.gif" BORDER="0" ALT="Yes"></A>
<!sql if $Back != "">dnl
		<A HREF="<!sql print $Back>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
<!sql else>dnl
		<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
<!sql endif>dnl
<!sql else>dnl
		<A HREF="X_ROOT/pub/issues/sections/articles/translate.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Language=<!sql print #Language>&Article=<!sql print #Article><!sql if $Back != "">&Back=<!sql print #Back><!sql endif>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<!sql else>dnl
    X_XAD({You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users.}, {pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>&Section=<!sql print #Section>})
<!sql endif>dnl

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
