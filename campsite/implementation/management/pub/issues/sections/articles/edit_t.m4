B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Edit Article Details})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT IdLanguage FROM Articles WHERE 1=0" q_al>dnl
<!sql query "SELECT Name FROM Languages WHERE 1=0" q_ls>dnl
<!sql query "SHOW COLUMNS FROM Articles LIKE 'XXYYZZ'" q_fld>dnl

E_HEAD

B_STYLE
E_STYLE

<!sql if $access>dnl
SET_ACCESS({dla}, {DeleteArticle})

B_BODY


<!sql setdefault Pub 0>dnl
<!sql setdefault Issue 0>dnl
<!sql setdefault Section 0>dnl
<!sql setdefault Language 0>dnl
<!sql setdefault sLanguage 0>dnl
<!sql setdefault Article 0>dnl
<!sql setdefault LockOk 0>dnl
B_HEADER({Edit Article Details})
B_HEADER_BUTTONS
X_HBUTTON({Articles}, {pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>&Section=<!sql print #Section>})
X_HBUTTON({Sections}, {pub/issues/sections/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>})
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Articles WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND Number=?Article AND IdLanguage=?sLanguage" q_art>dnl
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
<!sql query "SELECT Name FROM Languages WHERE Id=?sLanguage" q_slang>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
X_CURRENT({Issue:}, {<B><!sql print ~q_iss.Number>. <!sql print ~q_iss.Name> (<!sql print ~q_lang.Name>)</B>})
X_CURRENT({Section:}, {<B><!sql print ~q_sect.Number>. <!sql print ~q_sect.Name></B>})
X_CURRENT({Article:}, {<B><!sql print ~q_art.Name> (<!sql print ~q_slang.Name>)</B>})
X_CURRENT({Field:}, {<B><!sql print ?eField></B>})
E_CURRENT
<!sql free q_lang>dnl

CHECK_XACCESS({ChangeArticle})
<!sql query "SELECT (?xaccess != 0) or ((?q_art.IdUser = ?Usr.Id) and ('?q_art.Published' = 'N'))" q_xperm>dnl
<!sql if @q_xperm.0>dnl

<!sql set edit_ok 0>dnl
<!sql if @q_art.LockUser == 0>dnl
<!sql set LockOk 1>dnl
<!sql endif>dnl

<!sql if $LockOk>dnl
	<!sql query "UPDATE Articles SET LockUser=?Usr.Id, LockTime=NOW() WHERE IdPublication=?q_art.IdPublication AND NrIssue=?q_art.NrIssue AND NrSection=?q_art.NrSection AND Number=?q_art.Number AND IdLanguage=?q_art.IdLanguage">dnl
	<!sql set edit_ok 1>dnl
<!sql else>dnl
	<!sql if @q_art.LockUser == @Usr.Id>dnl
		<!sql query "UPDATE Articles SET LockTime=NOW() WHERE IdPublication=?q_art.IdPublication AND NrIssue=?q_art.NrIssue AND NrSection=?q_art.NrSection AND Number=?q_art.Number AND IdLanguage=?q_art.IdLanguage">dnl
		<!sql set edit_ok 1>dnl
	<!sql else>dnl
<P>
B_MSGBOX({Article is locked})
<!sql query "SELECT *, NOW() AS Now FROM Users WHERE Id=?q_art.LockUser" q_luser>dnl
	X_MSGBOX_TEXT({<LI>This article has been locked by <B><!sql print ~q_luser.Name> (<!sql print ~q_luser.UName>)</B> at
		<B><!sql print ~q_art.LockTime></B></LI>
		<LI>Now is <B><!sql print ~q_luser.Now></B></LI>
		<LI>Are you sure you want to unlock it?</LI>
	})
	B_MSGBOX_BUTTONS
		<A HREF="<!sql print $REQUEST_URI>&LockOk=1"><IMG SRC="X_ROOT/img/button/yes.gif" BORDER="0" ALT="Yes"></A>
		<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>&Section=<!sql print #Section>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
	E_MSGBOX_BUTTONS
<!sql free q_luser>dnl
E_MSGBOX
<P>
	<!sql endif>dnl
<!sql endif>dnl

<!sql if $edit_ok>dnl
<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
<TR><TD>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
<TR><TD>
<!sql if @q_art.Published == "Y">dnl
X_NEW_BUTTON({Unpublish}, {X_ROOT/pub/issues/sections/articles/status.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #q_art.Number>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>&Back=<!sql print #REQUEST_URI>})
<!sql elsif @q_art.Published == "S">dnl
X_NEW_BUTTON({Publish}, {X_ROOT/pub/issues/sections/articles/status.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #q_art.Number>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>&Back=<!sql print #REQUEST_URI>})
<!sql else>dnl
X_NEW_BUTTON({Submit}, {X_ROOT/pub/issues/sections/articles/status.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #q_art.Number>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>&Back=<!sql print #REQUEST_URI>})
<!sql endif>dnl
</TD>
<TD>
X_NEW_BUTTON({Images}, {X_ROOT/pub/issues/sections/articles/images/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>})
</TD><TD>
X_NEW_BUTTON({Unlock}, {X_ROOT/pub/issues/sections/articles/do_unlock.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>})
</TD></TR>
<TR><TD>
X_NEW_BUTTON({Preview}, {javascript:void(window.open('X_ROOT/pub/issues/sections/articles/preview.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>', 'fpreview', 'resizable=yes,scrollbars=yes,toolbar=yes,width=680,height=560'))})
</TD><TD>
X_NEW_BUTTON({Translate}, {X_ROOT/pub/issues/sections/articles/translate.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #q_art.Number>&Language=<!sql print #Language>&Back=<!sql print #REQUEST_URI>})
<!sql if $dla>dnl
</TD><TD>
X_NEW_BUTTON({Delete}, {X_ROOT/pub/issues/sections/articles/del.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>&Back=<!sql print #REQUEST_URI>})
</TD><TD>
X_NEW_BUTTON({Edit details}, {X_ROOT/pub/issues/sections/articles/edit.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>})
</TD><TD>
<!sql endif>dnl
</TD></TR>
</TABLE>
</TD><TD ALIGN="RIGHT">
	B_SEARCH_DIALOG({GET}, {edit.xql})
		<TD>Language:</TD>
		<TD><SELECT NAME="sLanguage">
<!sql query "SELECT IdLanguage FROM Articles WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND Number=?Article" q_al>dnl
<!sql print_loop q_al>dnl
<!sql query "SELECT Name FROM Languages WHERE Id=?q_al.IdLanguage" q_ls>dnl
<!sql print_loop q_ls>dnl
<OPTION VALUE="<!sql print ~q_al.IdLanguage>"<!sql if @q_al.IdLanguage == $sLanguage> SELECTED<!sql endif>><!sql print ~q_ls.Name>dnl
<!sql done>dnl
<!sql free q_ls>dnl
<!sql done>dnl
</SELECT></TD>
		<TD><INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/search.gif" BORDER="0"></TD>
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<!sql print ~Pub>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<!sql print ~Issue>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<!sql print ~Section>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<!sql print ~Article>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<!sql print ~Language>">
	E_SEARCH_DIALOG
</TD></TR>
</TABLE>


B_DIALOG({Edit field: <!sql print ?eField>}, {POST}, {do_edit_t.xql})
	<tr><td><TEXTAREA rows=15 cols=50 NAME="cField"></TEXTAREA></td></tr>

<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<!sql print ~Pub>">
<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<!sql print ~Issue>">
<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<!sql print ~Section>">
<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<!sql print ~Article>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<!sql print ~Language>">
<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<!sql print ~sLanguage>">
<INPUT TYPE="HIDDEN" NAME="eField" VALUE="<!sql print ~eField>">
<INPUT TYPE="HIDDEN" NAME="query" VALUE="">

<!sql set fld "">
<!sql set ftyp "">

</FORM>

<FORM NAME="fields">


	B_DIALOG_BUTTONS
<SCRIPT>
	function escape_mysql(str)
	{{
		a = str.replace(/\\/g, "\\\\"); 
		b = a.replace(/\'/g, "\\'");
		e = b.replace(/\"/g, "\\\""); 
		return e;
	}}
	function do_submit()
	{{
		f = [ <!sql print $fld> ];
		t = [ <!sql print $ftyp> ];
		a = 0;
		document.dialog.query.value = "";
		for (i = 0; i < f.length; i++) {
			if (a == 1) {
				document.dialog.query.value += ", ";
			}
			document.dialog.query.value += f[i] + " = '" + escape_mysql(document.fields.elements[i].value) + "'";
			a = 1;
		}
		document.dialog.submit();
	}}
</SCRIPT>
		X_HR
		<A HREF="javascript:void(do_submit())"><IMG SRC="X_ROOT/img/button/save.gif" BORDER="0" ALT="OK"></A>
	E_DIALOG_BUTTONS
E_DIALOG


<!sql endif>dnl

<!sql else>dnl
    X_XAD({You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users.}, {pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>&Section=<!sql print #Section>})
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
