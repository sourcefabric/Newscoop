B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({DeleteIssue})

B_HEAD
	X_EXPIRES
	X_TITLE({Deleting Issue})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to delete issues.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
<!sql setdefault Issue 0>dnl
<!sql setdefault Language 0>dnl
B_HEADER({Deleting Issue})
B_HEADER_BUTTONS
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Issues WHERE IdPublication=?Pub AND Number=?Issue AND IdLanguage=?Language" q_iss>dnl
<!sql if $NUM_ROWS>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl

B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
E_CURRENT

<P>
B_MSGBOX({Deleting issue})
	X_MSGBOX_TEXT({
<!sql setdefault del 1>dnl
<!sql query "SELECT COUNT(*) FROM Sections WHERE IdPublication=?Pub AND NrIssue=?Issue AND IdLanguage=?Language" q_sect>dnl
<!sql if @q_sect.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_sect.0> section(s) left.</LI>
<!sql endif>dnl
<!sql query "SELECT COUNT(*) FROM Articles WHERE IdPublication=?Pub AND NrIssue=?Issue AND IdLanguage=?Language" q_art>dnl
<!sql if @q_art.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_art.0> articles(s) left.</LI>
<!sql endif>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql if $del>dnl
<!sql query "DELETE FROM Issues WHERE IdPublication=?Pub AND Number=?Issue AND IdLanguage=?Language">dnl
<!sql endif>dnl
<!sql if $AFFECTED_ROWS>dnl
	<LI>The issue <B><!sql print ~q_iss.Name></B> has ben deleted.</LI>
X_AUDIT({12}, {Issue ?q_iss.Name from publication ?q_pub.Name deleted})
<!sql else>dnl
	<LI>The issue <B><!sql print ~q_iss.Name></B> could not be deleted.</LI>
<!sql endif>dnl
	})
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/issues/?Pub=<!sql print #Pub>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
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

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
