<HTML>
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_COOKIE({TOL_Access=all})
	X_COOKIE({TOL_Preview=on})
	X_TITLE({Preview Issue})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl

<!sql setdefault Pub 0>dnl
<!sql setdefault Issue 0>dnl
<!sql setdefault Language 0>dnl

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Issues WHERE IdPublication=?Pub AND Number=?Issue AND IdLanguage=?Language" q_iss>dnl
<!sql if ($NUM_ROWS != 0 && @q_iss.FrontPage != "")>dnl
<FRAMESET ROWS="*,48" BORDER="1">
<FRAME SRC="<!sql print $q_iss.FrontPage>?IdPublication=<!sql print #Pub>&NrIssue=<!sql print #Issue>&IdLanguage=<!sql print #Language>" NAME="body" FRAMEBORDER="1" MARGINWIDTH="0" MARGINHEIGHT="0">
<FRAME NAME="e" SRC="empty.xql" FRAMEBORDER="1" MARGINWIDTH="0" MARGINHEIGHT="0">
</FRAMESET>
<!sql else>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Preview Issue})
X_HEADER_NO_BUTTONS
E_HEADER

<!sql query "SELECT Name FROM Languages WHERE Id=?Language" q_lang>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
X_CURRENT({Issue:}, {<B><!sql print ~q_iss.Number>. <!sql print ~q_iss.Name> (<!sql print ~q_lang.Name>)</B>})
E_CURRENT
<!sql free q_lang>dnl

<BLOCKQUOTE>
	<LI>This issue cannot be previewed. Please make sure it has a <B><I>front page</I></B> template selected.</LI>
</BLOCKQUOTE>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql endif>dnl

E_DATABASE
E_HTML
