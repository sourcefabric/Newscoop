B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageIssue})

B_HEAD
	X_EXPIRES
	X_TITLE({Changing Issue Status})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to change issues.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
<!sql setdefault Issue 0>dnl
<!sql setdefault Language 0>dnl
B_HEADER({Changing Issue Status})
B_HEADER_BUTTONS
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Number, Name, Published FROM Issues WHERE IdPublication=?Pub AND Number=?Issue AND IdLanguage=?Language" q_iss>dnl
<!sql if $NUM_ROWS>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl

<!sql query "SELECT Name FROM Languages WHERE Id=?Language" q_lang>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
E_CURRENT

<P>
B_MSGBOX({Changing issue status})
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "UPDATE Issues SET PublicationDate=IF(Published = 'N', NOW(), PublicationDate), Published=IF(Published = 'N', 'Y', 'N') WHERE IdPublication=?Pub AND Number=?Issue AND IdLanguage=?Language">dnl
<!sql if $AFFECTED_ROWS>dnl
	X_MSGBOX_TEXT({<LI>Status of the issue <B><!sql print ~q_iss.Number>. <!sql print ~q_iss.Name> (<!sql print ~q_lang.Name>)</B> has been changed from <B><!sql if @q_iss.Published == "Y">Published<!sql else>Not published<!sql endif></B> to <B><!sql if @q_iss.Published == "Y">Not published<!sql else>Published<!sql endif></B>.</LI>})
X_AUDIT({14}, {Issue ?q_iss.Number. ?q_iss.Name (?q_lang.Name) Published: ?q_iss.Published changed status})
<!sql else>dnl
	X_MSGBOX_TEXT({<LI>Status of the issue <B><!sql print ~q_iss.Number>. <!sql print ~q_iss.Name> (<!sql print ~q_lang.Name>)</B> could not be changed.</LI>})
<!sql endif>dnl
	B_MSGBOX_BUTTONS
<!sql if $AFFECTED_ROWS>dnl
		<A HREF="X_ROOT/pub/issues/?Pub=<!sql print #Pub>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>dnl
		<A HREF="X_ROOT/pub/issues/status.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
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

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
