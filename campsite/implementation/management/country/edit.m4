B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageCountries*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Edit country name*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change country names.*>)
<? }
    query ("SELECT Name FROM Countries WHERE 1=0", 'q_clist');
    ?>dnl
E_HEAD

B_STYLE
E_STYLE

<? if ($access) { ?>dnl
B_BODY

<? 
    todef('Code');
    todefnum('Language');
?>dnl
B_HEADER(<*Edit country name*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Countries*>, <*country/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? 
    query ("SELECT * FROM Countries WHERE Code = '$Code' and IdLanguage = $Language", 'q_country');
    fetchRow($q_country);
    if ($NUM_ROWS) { ?>dnl

<P>
B_DIALOG(<*Edit country name*>, <*POST*>, <*do_edit.php*>)
	B_DIALOG_INPUT(<*Country*>)
<?
   query ("SELECT Name FROM Countries WHERE Code='$Code'", 'q_clist');
   $comma= 0;
   $nr=$NUM_ROWS;
   for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_clist);
	if ($comma)
	    print ',';
	else
	    $comma= 1;
	pgetHVar($q_clist,'Name');
    }
?>dnl
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64" VALUE="<? pgetHVar($q_country,'Name'); ?>">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE=HIDDEN NAME=Code VALUE="<? print encHTML(decS($Code)); ?>">
		<INPUT TYPE=HIDDEN NAME=Language VALUE="<? print $Language; ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/country/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such country name.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

