B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Translate Article})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT Name FROM Articles WHERE 1=0" q_alist>dnl
<!sql query "SELECT Id, Name FROM Languages WHERE 1=0" q_lang>dnl
E_HEAD

B_STYLE
E_STYLE

<!sql if $access>dnl
B_BODY

<!sql setdefault Pub 0>dnl
<!sql setdefault Issue 0>dnl
<!sql setdefault Section 0>dnl
<!sql setdefault Language 0>dnl
<!sql setdefault Article 0>dnl
B_HEADER({Translate Article})
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
<P>
B_DIALOG({Translate article}, {POST}, {do_translate.xql})
	B_DIALOG_INPUT({Article:})
<!sql query "SELECT Name FROM Articles WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND Number=?Article" q_alist>dnl
<!sql set comma 0>dnl
<!sql print_loop q_alist>dnl
<!sql if $comma>, <!sql else><!sql set comma 1><!sql endif><!sql print ~q_alist.Name>dnl
<!sql done>dnl
<!sql free q_alist>dnl
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Name:})
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Language:})
		<SELECT NAME="cLanguage">
<!sql query "SELECT Id, Name FROM Languages ORDER BY Name" q_lang>dnl
<!sql print_loop q_lang>dnl
<!sql query "SELECT COUNT(*) FROM Articles WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND Number=?Article AND IdLanguage=?q_lang.Id" q_xart>dnl
<!sql if @q_xart.0 == 0>dnl
			<OPTION VALUE="<!sql print ~q_lang.Id>"><!sql print ~q_lang.Name>
<!sql endif>dnl
<!sql free q_xart>dnl
<!sql done>dnl
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Keywords:})
		<INPUT TYPE="TEXT" NAME="cKeywords" SIZE="32" MAXLENGTH="255">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<!sql print ~Pub>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<!sql print ~Issue>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<!sql print ~Section>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<!sql print ~Article>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<!sql print ~Language>">
		<INPUT TYPE="HIDDEN" NAME="cType" VALUE="<!sql print ~q_art.Type>">
		<INPUT TYPE="HIDDEN" NAME="cOnFrontPage" VALUE="<!sql print ~q_art.OnFrontPage>">
		<INPUT TYPE="HIDDEN" NAME="cOnSection" VALUE="<!sql print ~q_art.OnSection>">
		<INPUT TYPE="HIDDEN" NAME="cPublic" VALUE="<!sql print ~q_art.Public>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<!sql setdefault Back "">dnl
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<!sql print ~Back>">
<!sql if $Back != "">dnl
		<A HREF="<!sql print $Back>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<!sql else>dnl
		<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>&Section=<!sql print #Section>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<!sql endif>dnl
	E_DIALOG_BUTTONS
E_DIALOG
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
