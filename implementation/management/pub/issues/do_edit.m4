B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageIssue})

B_HEAD
	X_EXPIRES
	X_TITLE({Updating Issue})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add issues.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault cName "">dnl
<!sql setdefault cLang 0>dnl

<!sql setdefault Pub 0>dnl
<!sql setdefault Issue 0>dnl
<!sql setdefault Language 0>dnl

B_HEADER({Changing Issue's details})
B_HEADER_BUTTONS
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #cPub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Issues WHERE IdPublication=?Pub AND Number=?Issue AND IdLanguage=?Language" publ>dnl
<!sql if $NUM_ROWS>dnl
<!sql query "SELECT * FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl

<!sql query "SELECT Id, Name FROM Languages WHERE Id=?Language" q_lang>dnl

B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
X_CURRENT({Issue:}, {<B><!sql print ~publ.Number>. <!sql print ~publ.Name> (<!sql print ~q_lang.Name>)</B>})
E_CURRENT   

<!sql set correct 1><!sql set created 0>dnl
<P>
B_MSGBOX({Changing Issue's details})
	X_MSGBOX_TEXT({

<!sql query "SELECT TRIM('?cName')" q_x>dnl
<!sql if ($cLang = 0)>dnl
<!sql set correct 0>dnl
		<LI>You must select a language.</LI>
<!sql endif>dnl
<!sql if (@q_x.0 == "" || @q_x.0 == " ")>dnl
<!sql set correct 0>dnl
		<LI>You must complete the <B>Name</B> field.</LI>
<!sql endif>dnl

<!sql if $correct>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "UPDATE Issues SET Name='?q_x.0', IdLanguage=?cLang WHERE IdPublication=?Pub AND Number=?Issue">dnl
<!sql setexpr created ($AFFECTED_ROWS != 0)>dnl
<!sql endif>dnl
<!sql if $created>dnl
		<LI>The issue <B><!sql print ~cName></B> has been successfuly changed.</LI>
X_AUDIT({11}, {Issue ?cName updated in publication ?publ.Name})
<!sql else>dnl
<!sql if ($correct != 0)>dnl
		<LI>The issue could not be changed.</LI>
<!--LI>Please check if another issue with the same number/language does not already exist.</LI-->
<!sql endif>dnl
<!sql endif>dnl
		})
<!sql if $correct && $created>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/issues/?Pub=<!sql print #Pub>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<!sql else>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/issues/edit.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
<!sql endif>dnl
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
