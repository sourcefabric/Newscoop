B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageIssue})

B_HEAD
	X_EXPIRES
	X_TITLE({Change Issue Details})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to change issue details.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
<!sql setdefault Issue 0>dnl
<!sql setdefault Language 0>dnl

B_HEADER({Change Issue Details})
B_HEADER_BUTTONS
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #Pub>})
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

<P>
B_DIALOG({Change issue details}, {POST}, {do_edit.xql})
	B_DIALOG_INPUT({Name:})
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64" value="<!sql print @publ.Name>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Language:})
	    <SELECT NAME="cLang">
<!sql query "SELECT Id, Name FROM Languages" q_lang>dnl
<!sql print_loop q_lang>dnl
		<OPTION VALUE="<!sql print ~q_lang.Id>"<!sql if (@q_lang.Id == @publ.IdLanguage)> SELECTED<!sql endif>><!sql print ~q_lang.Name>
<!sql done>dnl	
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<!sql print ~Pub>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<!sql print ~Issue>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<!sql print ~Language>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/pub/issues/?Pub=<!sql print #Pub>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such issue.</LI>
</BLOCKQUOTE>
<!Sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
        <LI>No such publication.</LI>
</BLOCKQUOTE>
<!Sql endif>dnl    

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
