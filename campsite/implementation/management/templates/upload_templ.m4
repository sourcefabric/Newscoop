B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTempl*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Upload template*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to upload templates.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Upload template*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Templates*>, <*templates/?Path=<? pencURL(decS($Path)); ?>*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? todef('Path'); ?>dnl

B_CURRENT
X_CURRENT(<*Path*>, <*<B><? pencHTML(decURL($Path)); ?></B>*>)
E_CURRENT

<P>

B_DIALOG(<*Upload template*>, <*POST*>, <*do_upload_templ.php*>, <*multipart/form-data*>)
	B_DIALOG_INPUT(<*Template charset*>)
		<INPUT TYPE="HIDDEN" NAME="Path" VALUE="<? pencHTML(decS($Path)); ?>">
		<INPUT TYPE="HIDDEN" NAME="UNIQUE_ID" VALUE="1">
<?
	echo "<SELECT NAME=\"Charset\"><OPTION VALUE=\"\">-- ".getGS("Select a language/character set");
	echo " --<OPTION VALUE=\"UTF-8\">".getGS("All languages")."/UTF-8";
	query("SELECT CodePage, OrigName, Code FROM Languages", 'q_lang');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_lang);
		$codePage = getVar($q_lang,'CodePage');
		$code = getVar($q_lang,'Code');
		$origName = getVar($q_lang,'OrigName');
		echo "\t<OPTION VALUE=\"$codePage\"";
		if ($TOL_Language == $code)
			echo " SELECTED";
		echo ">$origName/$codePage\n";
	}
	echo "</SELECT>\n";
?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*File*>)
		<P><INPUT TYPE="FILE" NAME="File" SIZE="32" MAXLENGTH="128">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<? todef('Back');
    if ($Back != "") { ?>dnl
		<A HREF="<? p($Back); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } else { ?>dnl
		 <A HREF="<? pencHTML(decS($Path)); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } ?>dnl
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
