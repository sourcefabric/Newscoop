B_HTML
B_DATABASE

<!sql query "SELECT Id, Name FROM Languages WHERE 1=0" q_lang>dnl
CHECK_BASIC_ACCESS
CHECK_ACCESS({ManagePub})

B_HEAD
	X_EXPIRES
	X_TITLE({Change Publication Information})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to edit publication information.})
<!sql endif>dnl
<!sql query "SELECT Id, Name FROM Languages WHERE 1=0" q_lang>dnl
<!sql query "SELECT Unit, Name FROM TimeUnits WHERE 1=0" q_unit>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
B_HEADER({Change Publication Information})
B_HEADER_BUTTONS
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl

B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
E_CURRENT

<P>
B_DIALOG({Change publication information}, {POST}, {do_edit.xql})
	B_DIALOG_INPUT({Name:})
		<INPUT TYPE="TEXT" NAME="cName" VALUE="<!sql print ~q_pub.Name>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Site:})
		<INPUT TYPE="TEXT" NAME="cSite" VALUE="<!sql print ~q_pub.Site>" SIZE="32" MAXLENGTH="128">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Default language:})
	    <SELECT NAME="cLanguage">
<!sql query "SELECT Id, Name FROM Languages" q_lang>dnl
<!sql print_loop q_lang>dnl
		<OPTION VALUE="<!sql print ~q_lang.Id>"<!sql if (@q_lang.Id == @q_pub.IdDefaultLanguage)> SELECTED<!sql endif>><!sql print ~q_lang.Name>
<!sql done>dnl	
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Pay time:})
		<INPUT TYPE="TEXT" NAME="cPayTime" VALUE="<!sql print ~q_pub.PayTime>" SIZE="5" MAXLENGTH="5"> days
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Time Unit:})
	    <SELECT NAME="cTimeUnit">
<!sql query "SELECT Unit, Name FROM TimeUnits WHERE IdLanguage=1" q_unit>dnl
<!sql print_loop q_unit>dnl
		<OPTION VALUE="<!sql print ~q_unit.Unit>"<!sql if (@q_unit.Unit == @q_pub.TimeUnit)> SELECTED<!sql endif>><!sql print ~q_unit.Name>
<!sql done>dnl	
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Unit Cost:})
		<INPUT TYPE="TEXT" NAME="cUnitCost" VALUE="<!sql print ~q_pub.UnitCost>" SIZE="20" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT({Currency:})
		<INPUT TYPE="TEXT" NAME="cCurrency" VALUE="<!sql print ~q_pub.Currency>" SIZE="20" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<!sql print ~Pub>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/pub/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
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
