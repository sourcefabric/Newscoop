B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({AddArticle})

B_HEAD
	X_EXPIRES
	X_TITLE({Add New Article})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add articles.})
<!sql endif>dnl
<!sql query "SHOW TABLES LIKE 'Z'" q_tbl>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
<!sql setdefault Issue 0>dnl
<!sql setdefault Section 0>dnl
<!sql setdefault Language 0>dnl
<!sql setdefault Wiz 0>dnl
B_HEADER({Add New Article})
B_HEADER_BUTTONS
<!sql if $Wiz == 0>X_HBUTTON({Articles}, {pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>&Section=<!sql print #Section>})<!sql endif>
X_HBUTTON({Sections}, {pub/issues/sections/<!sql if $Wiz>add_article.xql<!sql endif>?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>})
X_HBUTTON({Issues}, {pub/issues/<!sql if $Wiz>add_article.xql<!sql endif>?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/<!sql if $Wiz>add_article.xql<!sql endif>})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

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

<P>
B_DIALOG({Add new article}, {POST}, {do_add.xql})
	B_DIALOG_INPUT({Name:})
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Type:})
			<SELECT NAME="cType">
<!sql query "SHOW TABLES LIKE 'X%'" q_tbl>dnl
<!sql print_loop q_tbl>dnl
<!sql query "SELECT SUBSTRING('?q_tbl.0', 2)" q_tbm>dnl
<!sql print_rows q_tbm "				<OPTION>~q_tbm.0\n">dnl
<!sql free q_tbm>dnl
<!sql done>dnl
			</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Language:})
			<SELECT NAME="cLanguage">
<!sql query "SELECT Id, Name FROM Languages" q_lng>dnl
<!sql query "SELECT IdDefaultLanguage from Publications where Id=?Pub" q_deflang>dnl
<!sql print_loop q_lng>
				<OPTION VALUE="<!sql print ~q_lng.Id>"<!sql if ~q_lng.Id == ~q_deflang.IdDefaultLanguage> selected<!sql endif>><!sql print ~q_lng.Name>
<!sql done>
<!sql free q_lng>dnl
			</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cFrontPage"})
		Show article on front page
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cSectionPage"})
		Show article on section page
	E_DIALOG_INPUT
	X_DIALOG_TEXT({Enter keywords, comma separated:})
	B_DIALOG_INPUT({Keywords:})
		<INPUT TYPE="TEXT" NAME="cKeywords" SIZE="32" MAXLENGTH="255">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<!sql print ~Pub>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<!sql print ~Issue>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<!sql print ~Section>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<!sql print ~Language>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<!sql setdefault Back "">dnl
<!sql if $Back != "">dnl
		<A HREF="<!sql print $Back>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<!sql else>dnl
		<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>&Section=<!sql print #Section>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<!sql endif>dnl
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

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
