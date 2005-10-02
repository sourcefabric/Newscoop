INCLUDE_PHP_LIB(<*templates*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTempl*>)

B_HEAD
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to upload templates.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl

B_HEADER(<*Upload template*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Templates*>, <*templates/?Path=<?php  pencURL(decS($Path)); ?>*>)
E_HEADER_BUTTONS
E_HEADER

<?php
	todef('TOL_Language');
	todef('Path');
?>dnl

B_CURRENT
X_CURRENT(<*Path*>, <*<?php  pencHTML(decURL($Path)); ?>*>)
E_CURRENT

<P>
B_DIALOG(<*Upload template*>, <*POST*>, <*do_upload_templ.php*>, <*multipart/form-data*>)
	B_DIALOG_INPUT(<*Template charset*>)
		<INPUT TYPE="HIDDEN" NAME="Path" VALUE="<?php  pencHTML(decS($Path)); ?>">
		<INPUT TYPE="HIDDEN" NAME="UNIQUE_ID" VALUE="1">
<?php
	echo "<SELECT NAME=\"Charset\" class=\"input_select\">\n";
	echo "<OPTION VALUE=\"\">-- ".getGS("Select a language/character set")." --</OPTION>\n";
	echo "<OPTION VALUE=\"UTF-8\">".getGS("All languages")."/UTF-8</OPTION>\n";
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
		echo ">$origName/$codePage</OPTION>\n";
	}
	echo "</SELECT>\n";
?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*File*>)
		<P><INPUT TYPE="FILE" NAME="File" SIZE="32" MAXLENGTH="128" class="input_file">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		SUBMIT(<*Save*>, <*Save changes*>)
<?php  todef('Back');
    if ($Back != "") { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*<?php  p($Back); ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*<?php echo "/$ADMIN/templates?Path=".encHTML(decS($Path)); ?>*>)
<?php  } ?>dnl
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
