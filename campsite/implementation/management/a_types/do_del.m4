B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({DeleteArticleTypes})

B_HEAD
	X_EXPIRES
	X_TITLE({Deleting Article Type})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to delete article types.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Deleting Article Type})
B_HEADER_BUTTONS
X_HBUTTON({Article Types}, {a_types/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql setdefault AType "">dnl

<P>
B_MSGBOX({Deleting article type})
	X_MSGBOX_TEXT({
<!sql set del 1>dnl
<!sql query "SELECT COUNT(*) FROM Articles WHERE Type='?AType'" q_art>dnl
<!sql if @q_art.0 != 0>dnl
<!sql set del 0>dnl
	<LI>There are <!sql print ~q_art.0> articles(s) left.</LI>
<!sql endif>dnl
<!sql if $del>dnl
<!sql query "DROP TABLE X?AType">dnl
<!sql endif>dnl
<!sql if $del>dnl
	<LI>The article type <B><!sql print ~AType></B> has been deleted.</LI>
X_AUDIT({62}, {Article type ~AType deleted})
<!sql else>dnl
	<LI>The article type <B><!sql print ~AType></B> could not be deleted.</LI>
<!sql endif>dnl
	})
	B_MSGBOX_BUTTONS
<!sql if $del>dnl
		<A HREF="X_ROOT/a_types/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>dnl
		<A HREF="X_ROOT/a_types/"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
