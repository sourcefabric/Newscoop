B_HTML
INCLUDE_PHP_LIB(<*..*>)dnl
B_DATABASE

CHECK_BASIC_ACCESS
<?
	todefnum('What');
	if ($What != 0) {
?>dnl
CHECK_ACCESS(<*ManageTempl*>)dnl
<? } ?>dnl

<?
	todef('query');
	todef('Path');
	todef('Name');
	todef('cField');
 ?>

B_HEAD
	X_EXPIRES
	X_TITLE(<*Templates management*>)

<?
    if ($access == 0) {
	if ($What) { ?>dnl
	X_AD(<*You do not have the right to change default templates.*>)
<? } else { ?>dnl
	X_LOGOUT
<? }
    }
?>dnl
E_HEAD

<? if ($access) {

SET_ACCESS(<*mta*>, <*ManageTempl*>)
SET_ACCESS(<*dta*>, <*DeleteTempl*>)
?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Edit template*>)
B_HEADER_BUTTONS

X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Path*>, <*<B><? pencHTML(decURL($Path)); ?></B>*>)
X_CURRENT(<*Template*>, <*<B><? pencHTML(decURL($Name)); ?></B>*>)
E_CURRENT

<P>

B_MSGBOX(<*Edit template*>)
<?
	if($dta){
		$filename = "$DOCUMENT_ROOT".decURL($Path)."$Name";
		$fd = fopen ($filename, "w");
		$nField = str_replace("\\r", "\r", $cField);
		$nField = str_replace("\\n", "\n", $nField);
		$nField = decS($nField);
		$res = fwrite ($fd, $nField);
		if($res >  0){ ?>dnl
			X_MSGBOX_TEXT(<* <LI><?putGS('The template has been saved.'); ?></LI> *>)
		<? }
		else { ?>dnl
			X_MSGBOX_TEXT(<* <LI><? putGS('The template could not be saved'); ?></LI> *>)
		<? }
		fclose ($fd);
	?>dnl
	X_AUDIT(<*113*>, <*getGS('Template $1 was changed',encHTML(decS($Path)).encHTML(decS($Name)) )*>)	
		B_MSGBOX_BUTTONS
		<? if ($res > 0) { ?>dnl
			REDIRECT(<*Done*>, <*Done*>, <*<? pencHTML(decS($Path)); ?>*>)
		<? } else { ?>dnl
			REDIRECT(<*OK*>, <*OK*>, <*<? pencHTML(decS($Path)); ?>*>)
		<? } ?>dnl
		E_MSGBOX_BUTTONS
	<?}
	else{?>
		X_MSGBOX_TEXT(<* <LI><?putGS('You do not have the right to modify templates.'); ?></LI> *>)
		B_MSGBOX_BUTTONS
			REDIRECT(<*OK*>, <*OK*>, <*<? pencHTML(decS($Path)); ?>*>)
		E_MSGBOX_BUTTONS
	<?}
?>dnl
	
E_MSGBOX

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML


