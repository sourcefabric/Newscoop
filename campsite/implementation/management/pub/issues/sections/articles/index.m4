B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Articles})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT Id, Name FROM Languages WHERE 1=0" ls>dnl
<!sql query "SELECT * FROM Articles WHERE 1=0" q_art>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({aaa}, {AddArticle})
SET_ACCESS({caa}, {ChangeArticle})
SET_ACCESS({daa}, {DeleteArticle})

B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
<!sql setdefault Issue 0>dnl
<!sql setdefault Section 0>dnl
<!sql setdefault Language 0>dnl
<!sql setdefault sLanguage 0>dnl
B_HEADER({Articles})
B_HEADER_BUTTONS
X_HBUTTON({Sections}, {pub/issues/sections/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>})
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql if $sLanguage == "">dnl
<!sql set sLanguage 0>dnl
<!sql endif>dnl

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
<!sql free l>dnl

<P><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
<!sql if $aaa != 0>
	<TD>X_NEW_BUTTON({Add new article}, {add.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Language=<!sql print #Language>&Back=<!sql print #REQUEST_URI>})</TD>
<!sql endif>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG({GET}, {index.xql})
		<TD>Language:</TD>
		<TD><SELECT NAME="sLanguage"><OPTION><!sql query "SELECT Id, Name FROM Languages ORDER BY Name" ls><!sql print_loop ls><OPTION VALUE="<!sql print ~ls.Id>"<!sql if @ls.Id == $sLanguage> SELECTED<!sql endif>><!sql print ~ls.Name><!sql done><!sql free ls></SELECT></TD>
		<TD><INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/search.gif" BORDER="0"></TD>
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<!sql print ~Pub>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<!sql print ~Issue>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<!sql print ~Section>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<!sql print ~Language>">
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<!sql if $sLanguage>dnl
<!sql set ll "AND IdLanguage=?sLanguage">dnl
<!sql set oo ", IdLanguage">dnl
<!sql else>dnl
<!sql set ll "">dnl
<!sql set oo "">dnl
<!sql endif>dnl

<!sql set kwdid "ssssssssss">dnl
<P><!sql setdefault ArtOffs 0><!sql if $ArtOffs < 0><!sql set ArtOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Articles WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section $ll ORDER BY Number $oo DESC LIMIT $ArtOffs, 11" q_art>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Name<BR><SMALL>(click to edit)</SMALL>})
		X_LIST_TH({Type}, {1%})
		X_LIST_TH({Language}, {1%})
		X_LIST_TH({Status}, {1%})
		X_LIST_TH({Images}, {1%})
		X_LIST_TH({Preview}, {1%})
		X_LIST_TH({Translate}, {1%})
<!sql if $daa != 0>dnl
		X_LIST_TH({Delete}, {1%})
<!sql endif>dnl
	E_LIST_HEADER
<!sql print_loop q_art>dnl
<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM
			<!sql if @q_art.Number == $kwdid>&nbsp;<!sql endif><A HREF="X_ROOT/pub/issues/sections/articles/edit.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #q_art.Number>&Language=<!sql print #Language>&sLanguage=<!sql print #q_art.IdLanguage>"><!sql print ~q_art.Name>&nbsp;</A>
		E_LIST_ITEM
		B_LIST_ITEM
		<!sql print ~q_art.Type>
		E_LIST_ITEM
		B_LIST_ITEM
<!sql query "SELECT Name FROM Languages WHERE Id=?q_art.IdLanguage" q_ail>dnl
		<!sql print ~q_ail.Name>
<!sql free q_ail>dnl
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
<!sql if @q_art.Published == "Y">dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/status.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #q_art.Number>&Language=<!sql print #Language>&sLanguage=<!sql print #q_art.IdLanguage>&Back=<!sql print #REQUEST_URI>">Published</A>
<!sql elsif @q_art.Published == "N">dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/status.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #q_art.Number>&Language=<!sql print #Language>&sLanguage=<!sql print #q_art.IdLanguage>&Back=<!sql print #REQUEST_URI>">New</A>
<!sql else>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/status.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #q_art.Number>&Language=<!sql print #Language>&sLanguage=<!sql print #q_art.IdLanguage>&Back=<!sql print #REQUEST_URI>">Submitted</A>
<!sql endif>dnl
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
<!sql if (@q_art.Number != $kwdid)>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/images/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #q_art.Number>&Language=<!sql print #Language>">Images</A>
<!sql else>dnl
		&nbsp;
<!sql endif>dnl	
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			<A HREF="javascript:void(window.open('X_ROOT/pub/issues/sections/articles/preview.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #q_art.Number>&Language=<!sql print #Language>&sLanguage=<!sql print #q_art.IdLanguage>', 'fpreview', 'resizable=yes,scrollbars=yes,toolbar=yes,width=680,height=560'))">Preview</A>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
<!sql if (@q_art.Number != $kwdid)>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/translate.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #q_art.Number>&Language=<!sql print #Language>&Back=<!sql print #REQUEST_URI>">Translate</A>
<!sql else>dnl
		&nbsp;
<!sql endif>dnl	
		E_LIST_ITEM
	<!sql if $daa != 0> 
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete article <!sql print ~q_art.Name>}, {icon/x.gif}, {pub/issues/sections/articles/del.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #q_art.Number>&Language=<!sql print #Language>&sLanguage=<!sql print #q_art.IdLanguage>&Back=<!sql print #REQUEST_URI>})
		E_LIST_ITEM
	<!sql endif>
		<!sql if (@q_art.Number != $kwdid)>dnl
		<!sql setexpr kwdid @q_art.Number>dnl
		<!sql endif>dnl
	E_LIST_TR
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($ArtOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>&ArtOffs=<!sql eval ($ArtOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>&ArtOffs=<!sql eval ($ArtOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No articles.</LI>
</BLOCKQUOTE>
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

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
