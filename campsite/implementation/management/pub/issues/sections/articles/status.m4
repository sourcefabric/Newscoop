B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Change Article Status})
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
<!sql setdefault Article 0>dnl
<!sql setdefault Language 0>dnl
<!sql setdefault sLanguage 0>dnl
B_HEADER({Change Article Status})
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

CHECK_XACCESS({ChangeArticle})
<!sql query "SELECT (?xaccess != 0) or ((?q_art.IdUser = ?Usr.Id) and ('?q_art.Published' = 'N'))" q_xperm>dnl
<!sql if @q_xperm.0>dnl
<p>
B_MSGBOX({Change article status})
	X_MSGBOX_TEXT({<LI>Change the status of article <B><!sql print ~q_art.Name> (<!sql print ~q_slang.Name>)</B> from <B><!sql if @q_art.Published == "Y">Published<!sql elsif @q_art.Published == "S">Submitted<!sql else>New<!sql endif></B> to:</LI>})
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_status.xql"><br>
		<!sql set check 0>
		<TABLE><!sql if @q_art.Published != "Y"><TR><TD ALIGN=LEFT><INPUT CHECKED TYPE="RADIO" NAME='Status' value='Y'> <B>Published</B></TD></TR> <!sql set check 1><!sql endif>
		<!sql if @q_art.Published != "S"><TR><TD ALIGN=LEFT><INPUT <!sql if ?check == 0>CHECKED<!sql set check 1><!sql endif> TYPE="RADIO" NAME='Status' value='S'> <B>Submitted</B></TD></TR> <!sql endif>
		<!sql if @q_art.Published != "N"><TR><TD ALIGN=LEFT><INPUT <!sql if ?check == 0>CHECKED<!sql set check 1><!sql endif> TYPE="RADIO" NAME='Status' value='N'> <B>New</B></TD></TR><!sql endif></TABLE>

		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<!sql print ~Pub>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<!sql print ~Issue>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<!sql print ~Section>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<!sql print ~Article>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<!sql print ~Language>">
		<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<!sql print ~sLanguage>"><P>
		<INPUT TYPE="IMAGE" NAME="Yes" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<!sql setdefault Back "">dnl
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<!sql print ~Back>">
<!sql if $Back != "">dnl
		<A HREF="<!sql print $Back>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<!sql else>dnl
		<A HREF="X_ROOT/pub/issues/sections/articles/edit.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>&Section=<!sql print #Section>&Article=<!sql print #Article>&sLanguage=<!sql print #sLanguage>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<!sql endif>dnl
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX



<P>
<!sql else>dnl
    X_XAD({You do not have the right to change this article status.  You may only edit your own articles and once submitted an article can only changed by authorized users.}, {pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>&Section=<!sql print #Section>})
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
