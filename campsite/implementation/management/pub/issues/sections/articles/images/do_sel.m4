B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({AddImage})

B_HEAD
	X_EXPIRES
	X_TITLE({Selecting images})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add images})
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
<!sql setdefault Pub1 0>dnl
<!sql setdefault Issue1 0>dnl
<!sql setdefault Section1 0>dnl
<!sql setdefault Article1 0>dnl
<!sql setdefault Image 0>dnl
<!sql setdefault Language 0>dnl
<!sql setdefault sLanguage 0>dnl
<!sql setdefault Number 1>

B_HEADER({Selecting Image})
B_HEADER_BUTTONS
X_HBUTTON({Images}, {pub/issues/sections/articles/images/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>&Section=<!sql print #Section>})
X_HBUTTON({Articles}, {pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>&Section=<!sql print #Section>})
X_HBUTTON({Sections}, {pub/issues/sections/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>})
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Description, Photographer, Place, Date, ContentType FROM Images WHERE IdPublication=?Pub1 AND NrIssue=?Issue1 AND NrSection=?Section1 AND NrArticle=?Article1 AND Number=?Image" q_img>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Articles WHERE IdPublication=?Pub1 AND NrIssue=?Issue1 AND NrSection=?Section1 AND Number=?Article1" q_art>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Sections WHERE IdPublication=?Pub1 AND NrIssue=?Issue1 AND IdLanguage=?Language AND Number=?Section1" q_sect>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Issues WHERE IdPublication=?Pub1 AND Number=?Issue1 AND IdLanguage=?Language" q_iss>dnl
<!sql if $NUM_ROWS>dnl
<!sql query "SELECT * FROM Publications WHERE Id=?Pub1" q_pub>dnl
<!sql if $NUM_ROWS>dnl

<!sql query "SELECT Name FROM Languages WHERE Id=?Language" q_lang>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
X_CURRENT({Issue:}, {<B><!sql print ~q_iss.Number>. <!sql print ~q_iss.Name> (<!sql print ~q_lang.Name>)</B>})
X_CURRENT({Section:}, {<B><!sql print ~q_sect.Number>. <!sql print ~q_sect.Name></B>})
X_CURRENT({Article:}, {<B><!sql print ~q_art.Name></B>})
X_CURRENT({Image:}, {<B><!sql print ~q_img.Description> (<!sql print ~q_img.Photographer>, <!sql print ~q_img.Place>, <!sql print ~q_img.Date>)</B>})
E_CURRENT
<!sql free q_lang>dnl

<P>
B_MSGBOX({Selecting image})
<!sql set AFFECTED_ROWS 0>dnl

<!sql query "SELECT Number FROM Images WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND NrArticle=?Article" q0>dnl
<!sql if $NUM_ROWS != 0>
	<!sql query "SELECT max(Number)+1 FROM Images WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND NrArticle=?Article" q0>dnl
	<!sql set Number @q0.0>
<!sql endif>
<!sql free q0>

<!sql query "SELECT * FROM Images WHERE IdPublication=?Pub1 AND NrIssue=?Issue1 AND NrSection=?Section1 AND NrArticle=?Article1 AND Number=?Image" q0>dnl
<!sql query "SELECT Image FROM Images WHERE IdPublication=?Pub1 AND NrIssue=?Issue1 AND NrSection=?Section1 AND NrArticle=?Article1 AND Number=?Image into outfile '/tmp/blob?Article?Number'">dnl

<!sql query "lock tables Images write">
<!sql query "load data infile '/tmp/blob?Article?Number' INTO TABLE Images (Image)">
<!sql query "UPDATE Images set IdPublication=?Pub,  NrIssue=?Issue, NrSection=?Section, NrArticle=?Article, Number=?Number, Description='@q0.Description', Photographer='@q0.Photographer', Place='@q0.Place', Date='@q0.Date', ContentType='@q0.ContentType' where Number=0">
<!sql set ar $AFFECTED_ROWS>
<!sql query "unlock tables">

<!sql if ?ar>dnl
	X_MSGBOX_TEXT({<LI>The image <B><!sql print ~q_img.Description></B> has been successfully added.</LI>})
X_AUDIT({42}, {Image ~q_img.Description added})
<!sql else>dnl
	X_MSGBOX_TEXT({<LI>The image <B><!sql print ~q_img.Description></B> could not be added.</LI>})
<!sql endif>dnl
	B_MSGBOX_BUTTONS
	<A HREF="/cgi-bin/cleanb?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>&Number=<!sql print #Number>&NrArticle=<!sql print #Article>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
		</FORM>
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

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such image.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
