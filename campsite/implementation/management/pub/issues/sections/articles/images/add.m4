B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({AddImage})

B_HEAD
	X_EXPIRES
	X_TITLE({Add New Image})
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
<!sql setdefault Language 0>dnl
<!sql setdefault sLanguage 0>dnl

B_HEADER({Add New Image})
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
X_CURRENT({Article:}, {<B><!sql print ~q_art.Name></B>})
E_CURRENT
<!sql free q_lang>dnl

<!sql query "SELECT MAX(Number) + 1 FROM Images WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND NrArticle=?Article" q_nr>dnl
<!sql if @q_nr.0 == ""><!sql set nr 1><!sql else><!sql setexpr nr @q_nr.0><!sql endif>dnl
<P>
B_DIALOG({Add new image}, {POST}, {/cgi-bin/upload_i}, {multipart/form-data})
	B_DIALOG_INPUT({Number:})
		<INPUT TYPE="TEXT" NAME="cNumber" VALUE="<!sql print ~nr>" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Description:})
		<INPUT TYPE="TEXT" NAME="cDescription" VALUE="Image <!sql print ~nr>" SIZE="32" MAXLENGTH="128">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Photographer:})
		<INPUT TYPE="TEXT" NAME="cPhotographer" VALUE="<!sql print ~Usr.Name>" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Place:})
		<INPUT TYPE="TEXT" NAME="cPlace" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Date:})
<!sql query "SELECT LEFT(NOW(), 10)" q_now>dnl
		<INPUT TYPE="TEXT" NAME="cDate" VALUE="<!sql print ~q_now.0>" SIZE="10" MAXLENGTH="10"> (YYYY-MM-DD)
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Image:})
		<INPUT TYPE="FILE" NAME="cImage" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<!sql print ~Pub>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<!sql print ~Issue>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<!sql print ~Section>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<!sql print ~Article>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<!sql print ~Language>">
		 <INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<!sql print ~sLanguage>"> 
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/pub/issues/sections/articles/images/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>&Section=<!sql print #Section>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
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
