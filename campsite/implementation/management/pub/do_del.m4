B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({DeletePub})

B_HEAD
	X_EXPIRES
	X_TITLE({Deleting Publication})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to delete publications.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Deleting Publication})
B_HEADER_BUTTONS
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault Pub 0>dnl
<!sql setdefault del 1>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl
<P>
B_MSGBOX({Deleting publication})
	X_MSGBOX_TEXT({
<!sql query "SELECT COUNT(*) FROM Issues WHERE IdPublication=?Pub" q_iss>dnl
<!sql if @q_iss.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_iss.0> issues(s) left.</LI>
<!sql endif>dnl
<!sql query "SELECT COUNT(*) FROM Sections WHERE IdPublication=?Pub" q_sect>dnl
<!sql if @q_sect.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_sect.0> section(s) left.</LI>
<!sql endif>dnl
<!sql query "SELECT COUNT(*) FROM Articles WHERE IdPublication=?Pub" q_art>dnl
<!sql if @q_art.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_art.0> article(s) left.</LI>
<!sql endif>dnl
<!sql query "SELECT COUNT(*) FROM Images WHERE IdPublication=?Pub" q_img>dnl
<!sql if @q_img.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_img.0> image(s) left.</LI>
<!sql endif>dnl
<!sql query "SELECT COUNT(*) FROM Subscriptions WHERE IdPublication=?Pub" q_subs>dnl
<!sql if @q_subs.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_subs.0> subscription(s) left.</LI>
<!sql endif>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql if $del>dnl
<!sql query "DELETE FROM Publications WHERE Id=?Pub">dnl
<!sql endif>dnl
<!sql if $AFFECTED_ROWS>dnl
	<LI>The publication <B><!sql print ~q_pub.Name></B> has been deleted.</LI>
X_AUDIT({2}, {Publication ?q_pub.Name deleted})
<!sql else>dnl
	<LI>The publication <B><!sql print ~q_pub.Name></B> could not be deleted.</LI>
<!sql endif>dnl
	})
	B_MSGBOX_BUTTONS
<!sql if $AFFECTED_ROWS>dnl
		<A HREF="X_ROOT/pub/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>dnl
		<A HREF="X_ROOT/pub/"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
</BLOCKQUOTE>
<!sql endif>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
