B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageArticleTypes*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add New Field*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add article type fields.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<? todef('AType'); ?>dnl
B_HEADER(<*Add new field*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Fields*>, <*a_types/fields/?AType=<? print encHTML($AType); ?>*>)
X_HBUTTON(<*Article Types*>, <*a_types/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Article type*>, <*<B><? print encHTML($AType); ?></B>*>)
E_CURRENT

<P>
B_DIALOG(<*Add new field*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Type*>)
		<SELECT NAME="cType">
			<OPTION VALUE="1"><? putGS('Text'); ?>
			<OPTION VALUE="2"><? putGS('Date'); ?>
			<OPTION VALUE="3"><? putGS('Article body'); ?>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="AType" VALUE="<? print encHTML($AType); ?>">
		SUBMIT(<*OK*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/a_types/fields/?AType=<? print encURL($AType); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
