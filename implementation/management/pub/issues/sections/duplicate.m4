B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Duplicate section*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  } ?>dnl

E_HEAD

B_STYLE
E_STYLE

<?php 	if ($access) {
?>dnl
B_BODY
<?php 
	todefnum('Language');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
?>
B_HEADER(<*Duplicate section*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
	query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	    if ($NUM_ROWS) {
		query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
		if ($NUM_ROWS) {
		    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');

		    fetchRow($q_sect);
		    fetchRow($q_iss);
		    fetchRow($q_pub);
		    fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B>*>)
E_CURRENT

CHECK_XACCESS(<*ManageSection*>)
<?php  if ($xaccess == 0) { ?>dnl
		X_AD(<*You do not have the right to add sections.*>)
<?php 	} else {
		$sxaccess = true;
	}
?>
CHECK_XACCESS(<*AddArticle*>)
<?php 	if ($xaccess == 0) { ?>dnl
		X_AD(<*You do not have the right to add articles.*>)
<?php 	}
	$xaccess = $sxaccess && $xaccess;

	if ($xaccess) {
?>
<P><CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	B_DIALOG_INPUT(<*Name*>)
		<INPUT DISABLED TYPE="TEXT" NAME="cName" SIZE="64" MAXLENGTH="64" VALUE="<?php  pgetHVar($q_sect,'Name'); ?>">
	E_DIALOG_INPUT
</TABLE></CENTER>

<P><DIV><TABLE><TR><TH WIDTH="150"></TH><TH><?php  putGS("Select destination"); ?></TH></TR></TABLE></DIV></P>

<?php  } ?>dnl

<?php  } ?>dnl

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

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such section.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

E_BODY

E_DATABASE
E_HTML
