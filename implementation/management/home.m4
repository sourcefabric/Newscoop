B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Home})
<!sql if $access == 0>dnl
		X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT * FROM Articles WHERE 1=0" q_art>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({aaa}, {AddArticle})
SET_ACCESS({mpa}, {ManagePub})
SET_ACCESS({muta}, {ManageUserTypes})
SET_ACCESS({mda}, {ManageDictionary})
SET_ACCESS({mca}, {ManageClasses})
SET_ACCESS({mcoa}, {ManageCountries})
SET_ACCESS({mata}, {ManageArticleTypes})
SET_ACCESS({mua}, {ManageUsers})
SET_ACCESS({mla}, {ManageLanguages})
SET_ACCESS({mta}, {ManageTempl})
SET_ACCESS({vla}, {ViewLogs})
SET_ACCESS({caa}, {ChangeArticle})

B_STYLE
E_STYLE

B_BODY

<!sql if $caa>dnl
<!sql setdefault What 0>dnl
<!sql else>dnl
<!sql setdefault What 1>dnl
<!sql endif>dnl

B_HEADER({Home})
B_HEADER_BUTTONS
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<TABLE BORDER="0" CELLSPACING="4" CELLPADDING="2" WIDTH="100%">
<TR><TD COLSPAN="2" BGCOLOR=#D0D0B0>Welcome <B><!sql print ~Usr.Name></B>!</TD></TR>
<TR>
    <TD VALIGN="TOP">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
<!sql if $aaa != 0>dnl
	X_HITEM({pub/add_article.xql}, {Add new article})
<!sql endif>dnl
<!sql if $mpa != 0>dnl
	X_HITEM({pub/add.xql?Back=<!sql print #REQUEST_URI>}, {Add new publication})
<!sql endif>dnl
<!sql if $mta != 0>dnl
	X_HITEM({templates/upload_templ.xql?Path=LOOK_PATH/&Back=<!sql print #REQUEST_URI>}, {Upload new template})
<!sql endif>dnl
<!sql if $mua != 0>dnl
	X_HITEM({users/add.xql?Back=<!sql print #REQUEST_URI>}, {Add new user account})
<!sql endif>dnl
<!sql if $muta != 0>dnl
	X_HITEM({u_types/add.xql?Back=<!sql print #REQUEST_URI>}, {Add new user type})
<!sql endif>dnl
<!sql if $mata != 0>dnl
	X_HITEM({a_types/add.xql?Back=<!sql print #REQUEST_URI>}, {Add new article type})
<!sql endif>dnl
<!sql if $mcoa != 0>dnl
	X_HITEM({country/add.xql?Back=<!sql print #REQUEST_URI>}, {Add new country})
<!sql endif>dnl
<!sql if $mla != 0>dnl
	X_HITEM({languages/add.xql?Back=<!sql print #REQUEST_URI>}, {Add new language})
<!sql endif>dnl
<!sql if $vla != 0>dnl
	X_HITEM({logs/}, {View logs})
<!sql endif>dnl
	X_HITEM({users/chpwd.xql}, {Change your password})
</TABLE>
	</TD>
	<TD VALIGN="TOP">

<!sql if $What>dnl

X_BULLET({Your articles:})

<!sql setdefault ArtOffs 0><!sql if $ArtOffs < 0><!sql set ArtOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Articles WHERE Iduser=?Usr.Id ORDER BY Number DESC, IdLanguage LIMIT $ArtOffs, 11" q_art>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Name<BR><SMALL>(click to edit article)</SMALL>})
		X_LIST_TH({Language}, {10%})
		X_LIST_TH({Status}, {10%})
	E_LIST_HEADER
<!sql print_loop q_art>dnl
<!sql if $i>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT IdLanguage FROM Sections WHERE IdPublication=?q_art.IdPublication AND NrIssue=?q_art.NrIssue AND IdLanguage=?q_art.IdLanguage" q_sect>dnl
<!sql if $NUM_ROWS == 0>dnl
<!sql query "SELECT IdLanguage FROM Sections WHERE IdPublication=?q_art.IdPublication AND NrIssue=?q_art.NrIssue LIMIT 1" q_sect>dnl
<!sql endif>dnl
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/articles/edit.xql?Pub=<!sql print #q_art.IdPublication>&Issue=<!sql print #q_art.NrIssue>&Section=<!sql print #q_art.NrSection>&Article=<!sql print #q_art.Number>&sLanguage=<!sql print #q_art.IdLanguage>&Language=<!sql print #q_sect.IdLanguage>"><!sql print ~q_art.Name></A>
		E_LIST_ITEM
<!sql query "SELECT Name FROM Languages WHERE Id=?q_art.IdLanguage" q_lang>dnl
		B_LIST_ITEM
			<!sql print ~q_lang.Name>
		E_LIST_ITEM
		B_LIST_ITEM
<!sql if @q_art.Published == "Y">dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/status.xql?Pub=<!sql print #q_art.IdPublication>&Issue=<!sql print #q_art.NrIssue>&Section=<!sql print #q_art.NrSection>&Article=<!sql print #q_art.Number>&Language=<!sql print #q_sect.IdLanguage>&sLanguage=<!sql print #q_art.IdLanguage>&Back=<!sql print #REQUEST_URI>">Published</A>
<!sql elsif @q_art.Published == "N">dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/status.xql?Pub=<!sql print #q_art.IdPublication>&Issue=<!sql print #q_art.NrIssue>&Section=<!sql print #q_art.NrSection>&Article=<!sql print #q_art.Number>&Language=<!sql print #q_sect.IdLanguage>&sLanguage=<!sql print #q_art.IdLanguage>&Back=<!sql print #REQUEST_URI>">New</A>
<!sql else>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/status.xql?Pub=<!sql print #q_art.IdPublication>&Issue=<!sql print #q_art.NrIssue>&Section=<!sql print #q_art.NrSection>&Article=<!sql print #q_art.Number>&Language=<!sql print #q_sect.IdLanguage>&sLanguage=<!sql print #q_art.IdLanguage>&Back=<!sql print #REQUEST_URI>">Submitted</A>
<!sql endif>dnl
		E_LIST_ITEM
	E_LIST_TR
<!sql free q_lang>dnl
<!sql free q_sect>dnl
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($ArtOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({home.xql?ArtOffs=<!sql eval ($ArtOffs - 10)>&What=1})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({home.xql?ArtOffs=<!sql eval ($ArtOffs + 10)>&What=1})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST

<!sql else>dnl

X_BULLET({Submitted articles:})
<!sql setdefault NArtOffs 0><!sql if $NArtOffs < 0><!sql set NArtOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Articles WHERE Published = 'S' ORDER BY Number DESC, IdLanguage LIMIT $NArtOffs, 11" q_art>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Name<BR><SMALL>(click to edit article)</SMALL>})
		X_LIST_TH({Language}, {10%})
	E_LIST_HEADER
<!sql print_loop q_art>dnl
<!sql if $i>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT IdLanguage FROM Sections WHERE IdPublication=?q_art.IdPublication AND NrIssue=?q_art.NrIssue AND IdLanguage=?q_art.IdLanguage" q_sect>dnl
<!sql if $NUM_ROWS == 0>dnl
<!sql query "SELECT IdLanguage FROM Sections WHERE IdPublication=?q_art.IdPublication AND NrIssue=?q_art.NrIssue LIMIT 1" q_sect>dnl
<!sql endif>dnl
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/articles/edit.xql?Pub=<!sql print #q_art.IdPublication>&Issue=<!sql print #q_art.NrIssue>&Section=<!sql print #q_art.NrSection>&Article=<!sql print #q_art.Number>&sLanguage=<!sql print #q_art.IdLanguage>&Language=<!sql print #q_sect.IdLanguage>"><!sql print ~q_art.Name></A>
		E_LIST_ITEM
<!sql query "SELECT Name FROM Languages WHERE Id=?q_art.IdLanguage" q_lang>dnl
		B_LIST_ITEM
			<!sql print ~q_lang.Name>
		E_LIST_ITEM
	E_LIST_TR
<!sql free q_lang>dnl
<!sql free q_sect>dnl
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($NArtOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({home.xql?NArtOffs=<!sql eval ($NArtOffs - 10)>&What=0})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({home.xql?NArtOffs=<!sql eval ($NArtOffs + 10)>&What=0})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST

<!sql endif>dnl

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
<TR>
<!sql if $What>dnl
<!sql if $caa>dnl
	<TD>
		X_HITEM({home.xql?What=0}, {Submitted articles})
	</TD>
<!sql endif>dnl
<!sql else>dnl
	<TD>
		X_HITEM({home.xql?What=1}, {Your articles})
	</TD>
<!sql endif>dnl
</TR>
</TABLE>

    </TD>
</TR>
</TABLE>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
