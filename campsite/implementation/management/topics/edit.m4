B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

<?
    query ("SELECT Id, Name FROM Topics WHERE 1=0", 'q_cat');
?>dnl
CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTopics*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Change topic name*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change topic name.*>)
<? }
    query ("SELECT Id, Name FROM Topics WHERE 1=0", 'q_cat');
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
	todefnum('IdCateg');
	todefnum('EdCateg');
?>
B_HEADER(<*Change topic name*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT * FROM Topics WHERE Id=$EdCateg", 'q_cat');
    if ($NUM_ROWS) { 
	fetchRow($q_cat);
?>dnl
B_CURRENT
X_CURRENT(<*Topic*>, <*<B><? pgetHVar($q_cat,'Name'); ?></B>*>)
E_CURRENT

<P>
B_DIALOG(<*Change topic name*>, <*POST*>, <*do_edit.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" VALUE="<? pgetHVar($q_cat,'Name'); ?>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="IdCateg" VALUE="<? p($IdCateg); ?>">
		<INPUT TYPE="HIDDEN" NAME="EdCateg" VALUE="<? p($EdCateg); ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/topics/index.php?IdCateg=<?p($IdCateg);?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such topic.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
