B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageIssue})

B_HEAD
	X_EXPIRES
	X_TITLE({Add New Issue})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add issues.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
B_HEADER({Add New Issue})
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

<P>
B_HOME_MENU({99%})
	B_HOME_MENU_HEADER
		X_HOME_MENU_TH({Use the structure of the previous issue}, {add_prev.xql?Pub=<!sql print #Pub>})
	E_HOME_MENU_HEADER
	B_HOME_MENU_BODY
		B_HOME_MENU_TD
			X_HOME_MENU_ITEM({<LI>Copy the entire structure in all languages from the previous issue except for content.<LI>You may modify it later if you wish.</LI>})
		E_HOME_MENU_TD
	B_HOME_MENU_BODY
	B_HOME_MENU_HEADER
		X_HOME_MENU_TH({Create a new structure}, {add_new.xql?Pub=<!sql print #Pub>})
	E_HOME_MENU_HEADER
	B_HOME_MENU_BODY
		B_HOME_MENU_TD
			X_HOME_MENU_ITEM({<LI>Create a complete new structure.<LI>You must define an issue type for each language and then sections for them.</LI>})
		E_HOME_MENU_TD
	B_HOME_MENU_BODY
E_HOME_MENU
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
