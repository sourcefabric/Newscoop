B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageIssue})

B_HEAD
	X_EXPIRES
	X_TITLE({Changing Issue Template})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to change issue templates.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY
<!sql setdefault What 0>dnl
<!sql setdefault Pubdnl
<!sql setdefault Issue 0>dnl
<!sql setdefault Language 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Issues WHERE IdPublication=?Pub AND Number=?Issue AND IdLanguage=?Language" q_iss>dnl
<!sql if $NUM_ROWS>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl
B_HEADER({Changing Issue Template})
B_HEADER_BUTTONS
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql query "SELECT Name FROM Languages WHERE Id=?Language" q_languages>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
X_CURRENT({Issue:}, {<B><!sql print ~q_iss.Name> (<!sql print_rows q_languages "~q_languages.0">)</B>})
E_CURRENT
<!sql free q_languages>dnl

<P>
<!sql set AFFECTED_ROWS 0>dnl
<!sql if $What == 1>dnl
B_MSGBOX({Changing issue template for front page})
<!sql query "UPDATE Issues SET FrontPage='?Path' WHERE IdPublication=?Pub AND Number=?Issue AND IdLanguage=?Language">
<!sql else>dnl
B_MSGBOX({Changing issue template for single article})
<!sql query "UPDATE Issues SET SingleArticle='?Path' WHERE IdPublication=?Pub AND Number=?Issue AND IdLanguage=?Language">
<!sql endif>dnl
	X_MSGBOX_TEXT({
<!sql if $AFFECTED_ROWS>dnl
	<LI>The template has been successfully changed.</LI>
<!sql if $What == 1>dnl
X_AUDIT({13}, {Issue template for publication ?q_pub.Name changed to ?Path})
<!sql else>dnl
X_AUDIT({36}, {Issue template for single articles from ?q_pub.Name changed to ?Path})
<!sql endif>dnl
<!sql else>dnl
	<LI>The template could not be changed.</LI>
<!sql endif>dnl
	})
	B_DIALOG_BUTTONS
	<A HREF="X_ROOT/pub/issues/?Pub=<!sql print #Pub>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_DIALOG_BUTTONS
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
