B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Edit Article})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT Number, Description FROM Images WHERE 1=0" q_img>dnl
<!sql query "SHOW COLUMNS FROM Articles LIKE 'XXYYZZ'" q_fld1>dnl
<!sql query "SELECT Id, Name FROM Classes WHERE 1=0" q_cls>dnl
<!sql set okf 0>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({dla}, {DeleteArticle})

B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
<!sql setdefault Issue 0>dnl
<!sql setdefault Section 0>dnl
<!sql setdefault Language 0>dnl
<!sql setdefault sLanguage 0>dnl
<!sql setdefault Article 0>dnl
<!sql setdefault Field "">dnl
<!sql setdefault eField "">dnl
<!sql setdefault LockOk 0>dnl
B_HEADER({Edit Article})
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
<!sql query "SELECT * FROM Articles WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND IdLanguage=?sLanguage AND Number=?Article" q_art>dnl
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
X_CURRENT({Field:}, {<B><!sql print ~eField></B>})
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
<P><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
<TR><TD>
<!sql if @q_art.Published == "Y">dnl
X_NEW_BUTTON({Unpublish}, {X_ROOT/pub/issues/sections/articles/status.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #q_art.Number>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>&Back=<!sql print #REQUEST_URI>})
<!sql elsif @q_art.Published == "S">dnl
X_NEW_BUTTON({Publish}, {X_ROOT/pub/issues/sections/articles/status.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #q_art.Number>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>&Back=<!sql print #REQUEST_URI>})
<!sql else>dnl
X_NEW_BUTTON({Submit}, {X_ROOT/pub/issues/sections/articles/status.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #q_art.Number>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>&Back=<!sql print #REQUEST_URI>})
<!sql endif>dnl
</TD><TD>
X_NEW_BUTTON({Images}, {X_ROOT/pub/issues/sections/articles/images/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>})
</TD><TD>
X_NEW_BUTTON({Unlock}, {X_ROOT/pub/issues/sections/articles/do_unlock.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>})
</TD></TR>
<TR><TD>
X_NEW_BUTTON({Preview}, {javascript:void(window.open('X_ROOT/pub/issues/sections/articles/preview.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>', 'fpreview','resizable=yes,scrollbars=yes,toolbar=yes,width=680,height=560'))})
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

<P><!sql query "SHOW COLUMNS FROM X?q_art.Type LIKE 'F%'" q_fld1>dnl
<!sql set idx 0>
<!sql print_loop q_fld1>dnl

<!sql query "SELECT SUBSTRING('?q_fld1.0', 2), LOCATE('char', '?q_fld1.1', 1), LOCATE('date', '?q_fld1.1', 1)" q_xx>dnl
<!sql if @q_xx.1 != 0>dnl
	<!sql set type 0>dnl
<!sql elsif @q_xx.2 != 0>dnl
	<!sql set type 1>dnl
<!sql else>dnl
	<!sql set type 2>dnl
<!sql endif>dnl
<!sql set Field #q_xx.0>

<!sql if $eField == "">dnl
<!sql set fedit_ok 1>dnl
<!sql else>dnl
<!sql set fedit_ok 0>dnl
<!sql endif>dnl

<!sql if $fedit_ok == 0>dnl
<!sql if $eField == "F$Field">dnl
<!sql set fedit_ok 1>dnl
<!sql endif>dnl
<!sql endif>dnl

<!sql if $fedit_ok>dnl
<!sql if $type == 2>dnl
<!sql set okf 1>dnl
<!sql query "SELECT F?Field FROM X?q_art.Type WHERE NrArticle=?Article AND IdLanguage=?sLanguage" q_fld>
<!--B_CURRENT-->
<!--X_CURRENT({Field:}, {<B><!sql print ~Field></B>})-->
<!--E_CURRENT-->
<P ALIGN="CENTER">
<APPLET CODE="Test.class" CODEBASE="java/" ARCHIVE="test.jar" WIDTH="720" HEIGHT="420">
<PARAM NAME="port" VALUE="<!sql print $SERVER_PORT>">
<PARAM NAME="script" VALUE="X_ROOT/pub/issues/sections/articles/upload.xql">
<PARAM NAME="debug_" VALUE="">
<PARAM NAME="linkscript" VALUE="http://<!sql print ~SERVER_NAME>:<!sql print ~SERVER_PORT>X_ROOT/pub/issues/sections/articles/list.xql">
<PARAM NAME="clip" VALUE="">
<!-- <SCRIPT Language="JavaScript">
    if(navigator.appName.indexOf("Explorer") != -1) {{
	document.writeln("<PARAM NAME=\"clip\" VALUE=\"Explorer\">");
    }} else if(navigator.appName.indexOf("Netscape") != -1) {{
	document.writeln("<PARAM NAME=\"clip\" VALUE=\"\">");
    }} else {{
	document.writeln("<PARAM NAME=\"clip\" VALUE=\"Unknown\">");
    }}
</SCRIPT> -->
<PARAM NAME="clip" VALUE="">
<PARAM NAME="UserId" VALUE="<!sql print ~Usr.Id>">
<PARAM NAME="UserKey" VALUE="<!sql print ~Usr.KeyId>">
<PARAM NAME="IdPublication" VALUE="<!sql print ~Pub>">
<PARAM NAME="NrIssue" VALUE="<!sql print ~Issue>">
<PARAM NAME="NrSection" VALUE="<!sql print ~Section>">
<PARAM NAME="NrArticle" VALUE="<!sql print ~Article>">
<PARAM NAME="IdLanguage" VALUE="<!sql print ~sLanguage>">
<PARAM NAME="Field" VALUE="<!sql print ?Field>">
<!sql setexpr idx ?idx+1>
<PARAM NAME="idx" VALUE="<!sql print ?idx>">
<PARAM NAME="Content" VALUE="<!sql print #q_fld.0>">
<!sql free q_fld>dnl
<!sql query "SELECT Number, Description FROM Images WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND NrArticle=?Article" q_img>dnl
<!sql set v_i 0>dnl
<!sql print_loop q_img>dnl
<!sql if $okf>dnl
<!sql print "<PARAM NAME=\"image$v_i\" VALUE=\"@q_img.Number, @q_img.Description\">">
<!sql setexpr v_i ($v_i + 1)>dnl
<!sql endif>dnl
<!sql done>dnl
<!sql query "SELECT Id, Name FROM Classes WHERE IdLanguage=?sLanguage ORDER BY Name" q_cls>dnl
<!sql set v_i 0>dnl
<!sql print_loop q_cls>dnl
<!sql if $okf>dnl
<!sql print "<PARAM NAME=\"tol\#$v_i\" VALUE=\"@q_cls.Name\">">
<!sql setexpr v_i ($v_i + 1)>dnl
<!sql endif>dnl
<!sql done>dnl

</APPLET>
<P>
<!sql set okf 0>dnl
<!sql endif>dnl
<!sql endif>dnl

<!sql free q_xx>dnl
<!sql done>dnl
<!sql free q_fld1>

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
