B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageIssue})

B_HEAD
	X_EXPIRES
	X_TITLE({Copying Previous Issue})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add issues.})
<!sql endif>dnl
<!sql query "SELECT * FROM Issues WHERE 1=0" q_iss>dnl
<!sql query "SELECT * FROM Sections WHERE 1=0" q_sect>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault cOldNumber 0>dnl
<!sql setdefault cNumber 0>dnl
<!sql setdefault cPub 0>dnl
B_HEADER({Copying Previous Issue})
B_HEADER_BUTTONS
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #cPub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?cPub" publ>dnl
<!sql if $NUM_ROWS>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~publ.Name></B>})
E_CURRENT

<P>
B_MSGBOX({Copying previous issue})
	X_MSGBOX_TEXT({
<!sql query "SELECT * FROM Issues WHERE IdPublication=?cPub AND Number=?cOldNumber" q_iss>dnl
<!sql print_loop q_iss>dnl
	<!sql query "INSERT IGNORE INTO Issues SET IdPublication=?cPub, Number=?cNumber, IdLanguage=?q_iss.IdLanguage, Name='?q_iss.Name', FrontPage='?q_iss.FrontPage', SingleArticle='?q_iss.SingleArticle'">dnl
	<!sql query "SELECT * FROM Sections WHERE IdPublication=?cPub AND NrIssue=?cOldNumber AND IdLanguage=?q_iss.IdLanguage" q_sect>dnl
	<!sql print_loop q_sect>dnl
		<!sql query "INSERT IGNORE INTO Sections SET IdPublication=?cPub, NrIssue=?cNumber, IdLanguage=?q_iss.IdLanguage, Number=?q_sect.Number, Name='?q_sect.Name'">dnl
	<!sql done>dnl
<!sql done>dnl
X_AUDIT({11}, {New issue ?cNumber from ?cOldNumber in publication ?publ.Name})
	<LI>Copying done.</LI>
	})
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/issues/?Pub=<!sql print #cPub>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
