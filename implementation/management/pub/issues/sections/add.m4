B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSection*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new section*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add sections.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?>dnl
B_HEADER(<*Add new section*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_language');

	    fetchRow($q_iss);
	    fetchRow($q_pub);
	    fetchRow($q_language);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_language,'Name'); ?>)</B>*>)
E_CURRENT

<?php 
    query ("SELECT MAX(Number) + 1 FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language", 'q_nr');
    fetchRowNum($q_nr);
    
    if (getNumVar($q_nr,0) == "")
	$nr= 1;
    else
	$nr=getNumVar($q_nr,0);
?>dnl
<P>
B_DIALOG(<*Add new section*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Number*>)
		<INPUT TYPE="TEXT" NAME="cNumber" VALUE="<?php  p($nr); ?>" SIZE="5" MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Subscriptions*>)
		<INPUT TYPE="checkbox" NAME="cSubs"> <?php  putGS("Add section to all subscriptions."); ?>
	E_DIALOG_INPUT

<?php 
	## added by sebastian
	if (function_exists ("incModFile"))
		incModFile ();
?>

	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
