B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*AddArticle*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new article*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add articles.*>)
<?php  }
    query ("SHOW TABLES LIKE 'Z'", 'q_tbl');
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php  
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Language');
    todefnum('Wiz');
?>dnl
B_HEADER(<*Add new article*>)
B_HEADER_BUTTONS
<?php 
    if ($Wiz == 0) { ?>X_HBUTTON(<*Articles*>, <*pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)<?php  } ?>
X_HBUTTON(<*Sections*>, <*pub/issues/sections/<?php  if ($Wiz) { ?>add_article.php<?php  } ?>?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/<?php  if ($Wiz) { ?>add_article.php<?php  } ?>?Pub=<?php  p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/<?php  if ($Wiz) { ?>add_article.php<?php  } ?>*>)
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

<P>
B_DIALOG(<*Add new article*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="64" MAXLENGTH="140">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Type*>)
			<SELECT NAME="cType">
<?php 
    query ("SHOW TABLES LIKE 'X%'", 'q_tbl');
    $nr=$NUM_ROWS;

		for($loop=0;$loop<$nr;$loop++) { 
			fetchRowNum($q_tbl);
			print '<OPTION>'.encHTML ( substr ( getNumVar ($q_tbl,0) ,1 ) );
	        }
		?>dnl
			</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
			<SELECT NAME="cLanguage">
<?php 
    query ("SELECT Id, Name FROM Languages", 'q_lng');
	$nr=$NUM_ROWS;
		for($loop=0;$loop<$nr;$loop++) { 
			fetchRow($q_lng);
			pcomboVar(getHVar($q_lng,'Id'), $Language,getHVar($q_lng,'Name'));
	        }
?>dnl
			</SELECT>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cFrontPage"*>)
		<?php  putGS('Show article on front page'); ?>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cSectionPage"*>)
		<?php  putGS('Show article on section page'); ?>
	E_DIALOG_INPUT
	X_DIALOG_TEXT(<* <?php putGS("Enter keywords, comma separated");?>*>)
	B_DIALOG_INPUT(<*Keywords*>)
		<INPUT TYPE="TEXT" NAME="cKeywords" SIZE="64" MAXLENGTH="255">
	E_DIALOG_INPUT
	<?php 
	## added by sebastian
	if (function_exists ("incModFile"))
	incModFile ();
	?>
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
<?php  todef('Back');

    if ($Back != "") { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*<?php  p($Back); ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
<?php  } ?>dnl
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

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such section.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
