B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManagePub})

B_HEAD
	X_EXPIRES
	X_TITLE({Changing Publication Information})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to change publication information.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>
<!sql setdefault cName "">dnl
<!sql setdefault cSite "">dnl
<!sql setdefault cLanguage 0>dnl
<!sql setdefault cPayTime 0>dnl
<!sql setdefault cTimeUnit 0>dnl
<!sql setdefault cUnitCost 0>dnl
<!sql setdefault cCurrency 0>dnl
B_HEADER({Changing Publication Information})
B_HEADER_BUTTONS
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set correct 1><!sql set created 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl

B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
E_CURRENT

<P>
B_MSGBOX({Changing publication information})
	X_MSGBOX_TEXT({
<!sql query "SELECT TRIM('?cName'), TRIM('?cSite'), TRIM('?cUnitCost'), TRIM('?cCurrency')" q_tr>dnl
<!sql if (@q_tr.0 == "" || @q_tr.0 == " ")>dnl
<!sql set correct 0>dnl
		<LI>You must complete the <B>Name</B> field.</LI>
<!sql endif>dnl
<!sql if (@q_tr.1 == "" || @q_tr.1 == " ")>dnl
<!sql set correct 0>dnl
		<LI>You must complete the <B>Site</B> field.</LI>
<!sql endif>dnl
<!sql if (@q_tr.2 == "" || @q_tr.2 == " ")>dnl
<!sql set correct 0>dnl
		<LI>You must complete the <B>Unit Cost</B> field.</LI>
<!sql endif>dnl
<!sql if (@q_tr.3 == "" || @q_tr.3 == " ")>dnl
<!sql set correct 0>dnl
		<LI>You must complete the <B>Currency</B> field.</LI>
<!sql endif>dnl
<!sql if $correct>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "UPDATE Publications SET Name='?q_tr.0', Site='?q_tr.1', IdDefaultLanguage=?cLanguage, PayTime='?cPayTime', TimeUnit='?cTimeUnit', UnitCost='?cUnitCost', Currency='?q_tr.3' WHERE Id=?Pub">dnl
<!sql setexpr created ($AFFECTED_ROWS != 0)>dnl
<!sql endif>dnl
<!sql if $created>dnl
		<LI>The publication <B><!sql print ~q_tr.0></B> has been successfuly updated.</LI>
X_AUDIT({3}, {Publication ?q_tr.0 changed})
<!sql else>dnl
<!sql if ($correct != 0)>dnl
		<LI>The publication information could not be updated.</LI><LI>Please check if another publication with the same or the same site name does not already exist.</LI>
<!sql endif>dnl
<!sql endif>dnl
		})
	B_MSGBOX_BUTTONS
<!sql if $correct && $created>dnl
		<A HREF="X_ROOT/pub/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>
		<A HREF="X_ROOT/pub/edit.xql?Pub=<!sql print #Pub>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
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
