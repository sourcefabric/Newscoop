B_HTML
B_DATABASE

<!sql setdefault Issue 0>dnl
<!sql setdefault Pub 0>dnl
<!sql setdefault What 0>dnl
CHECK_BASIC_ACCESS
<!sql if $What != 0>dnl
CHECK_ACCESS({ManageTempl})
<!sql endif>dnl

B_HEAD
	X_EXPIRES
	X_TITLE({<!sql if $What>Select Template<!sql else>Templates Management<!sql endif>})
<!sql if $access == 0>dnl
<!sql if $What>dnl
	X_AD({You do not have the right to change default templates.})
<!sql else>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl

SET_ACCESS({mta}, {ManageTempl})
SET_ACCESS({dta}, {DeleteTempl})

B_STYLE
E_STYLE

B_BODY
B_HEADER({<!sql if $What>Select Template<!sql else>Templates<!sql endif>})
B_HEADER_BUTTONS
<!sql if $What>dnl
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/})
<!sql endif>dnl
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql if $What><!sql query "SELECT Name FROM Issues WHERE IdPublication=?Pub AND Number=?Issue AND IdLanguage=?Language" q_iss><!sql endif>dnl
<!sql if ($NUM_ROWS != 0 || $What == 0)>dnl
<!sql set NUM_ROWS 0>dnl
<!sql if $What><!sql query "SELECT Name FROM Publications WHERE Id=?Pub" q_pub><!sql endif>dnl
<!sql if ($NUM_ROWS != 0 || $What == 0)>dnl
<!sql query "SELECT SUBSTRING_INDEX('?REQUEST_URI', '?', 1), SUBSTRING_INDEX('?REQUEST_URI', '?', -1)" q_url>dnl
B_CURRENT
<!sql if $What>dnl
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
X_CURRENT({Issue:}, {<B><!sql print #Issue>. <!sql print ~q_iss.Name> (<!sql query "SELECT Name FROM Languages WHERE Id=?Language" q_language><!sql print_rows q_language "~q_language.0"><!sql free q_language>)</B>})
<!sql endif>dnl
X_CURRENT({Path:}, {<B><!sql print ~q_url.0></B>})
E_CURRENT
<P>
<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="0">
<TR>
<!sql if @q_url.0 != "LOOK_PATH/">dnl
<!sql if $What>dnl
<TD>X_NEW_BUTTON({Go up}, {../?What=<!sql print #What>&Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>})</TD>
<!sql else>dnl
<TD>X_NEW_BUTTON({Go up}, {..})</TD>
<!sql endif>dnl
<!sql endif>dnl
<!sql if $What == 0>dnl
<!sql if ?mta != 0>
<TD>X_NEW_BUTTON({Create new folder}, {X_ROOT/templates/new_dir.xql?Path=<!sql print #q_url.0>})</TD>
<TD>X_NEW_BUTTON({Upload template}, {X_ROOT/templates/upload_templ.xql?Path=<!sql print #q_url.0>})</TD>
<!sql endif>
<!sql else>dnl
<TD>
<!sql if $What == 1>dnl
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
	<TR>
		<TD><IMG SRC="X_ROOT/img/tol.gif" BORDER="0"></TD>
		<TD>Select the template for displaying the front page.</TD>
	</TR>
	</TABLE>
<!sql else>dnl
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
	<TR>
		<TD><IMG SRC="X_ROOT/img/tol.gif" BORDER="0"></TD>
		<TD>Select the template for displaying a single article.</TD>
	</TR>
	</TABLE>
<!sql endif>dnl
	</TD>
<!sql endif>dnl
</TABLE>
<P>
<!sql if $What>dnl
<!sql exec X_SCRIPT_BIN/stempl "@q_url.0" "@q_url.1">dnl
<!sql else>dnl
<!sql exec X_SCRIPT_BIN/list "@q_url.0" "?mta" "?dta">dnl
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

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
