B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageUserTypes})

B_HEAD
	X_TITLE({Add New User Type})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add user types.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Add New User Type})
B_HEADER_BUTTONS
X_HBUTTON({User Types}, {u_types/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<P>
B_DIALOG({Add new user type}, {POST}, {do_add.xql})
	B_DIALOG_INPUT({Name:})
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_PACKEDINPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cReader">})
		User is a reader
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cManagePub">})
		User may add/change publications
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cDeletePub">})
		User may delete publications
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cManageIssue">})
		User may add/change issues
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cDeleteIssue">})
		User may delete issues
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cManageSection">})
		User may add/change sections
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cDeleteSection">})
		User may delete sections
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cAddArticle">})
		User may add articles
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cChangeArticle">})
		User may change articles
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cDeleteArticle">})
		User may delete articles
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cAddImage">})
		User may add images
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cChangeImage">})
		User may change images
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cDeleteImage">})
		User may delete images
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cManageTempl">})
		User may add templates
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cDeleteTempl">})
		User may delete templates
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cManageUsers">})
		User may add/change user accounts and passwords
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cDeleteUsers">})
		User may delete user accounts
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cManageSubscriptions">})
		User may manage user subscriptions
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cManageUserTypes">})
		User may manage account types
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cManageArticleTypes">})
		User may add/change article types
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cDeleteArticleTypes">})
		User may delete article types
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cManageLanguages">})
		User may add languages and manage language information
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cDeleteLanguages">})
		User may delete languages
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cManageClasses">})
		User may manage dictionary classes
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cManageDictionary">})
		User may add/change dictionary entries
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cDeleteDictionary">})
		User may delete dictionary entries
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cMailNotify">})
		User will be notified on several events
	E_DIALOG_INPUT
	B_DIALOG_INPUT({<INPUT TYPE="CHECKBOX" NAME="cViewLogs">})
		User may view audit logs
	E_DIALOG_INPUT
	E_DIALOG_PACKEDINPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<!sql setdefault Back "">dnl
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<!sql print ~Back>">
<!sql if $Back != "">dnl
		<A HREF="<!sql print $Back>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0"></A>
<!sql else>dnl
		<A HREF="X_ROOT/u_types/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0"></A>
<!sql endif>dnl
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
