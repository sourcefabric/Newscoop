B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageIssue})

B_HEAD
	X_EXPIRES
	X_TITLE({Copy Previous Issue})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add issues.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
B_HEADER({Copy Previous Issue})
B_HEADER_BUTTONS
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?Pub" publ>dnl
<!sql if $NUM_ROWS>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~publ.Name></B>})
E_CURRENT

<!sql query "SELECT MAX(Number) FROM Issues WHERE IdPublication=?Pub" q_nr>dnl
<!sql if @q_nr.0 == "">dnl
<BLOCKQUOTE>
	<LI>No previous issue.</LI>
</BLOCKQUOTE>
<!sql else>dnl
<P>
B_DIALOG({Copy previous issue}, {POST}, {do_add_prev.xql})
	X_DIALOG_TEXT({Copy structure from issue nr <B><!sql print ~q_nr.0></B>})
	B_DIALOG_INPUT({Number:})
		<INPUT TYPE="TEXT" NAME="cNumber" VALUE="<!sql eval (@q_nr.0 + 1)>" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="cOldNumber" VALUE="<!sql print ~q_nr.0>">
		<INPUT TYPE="HIDDEN" NAME="cPub" VALUE="<!sql print ~Pub>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/pub/issues/?Pub=<!sql print #Pub>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<!sql endif>dnl

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
