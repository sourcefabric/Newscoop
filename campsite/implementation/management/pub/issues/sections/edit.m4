INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues/sections*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSection*>)

B_HEAD
 X_TITLE(<*Configure section*>)
<?php  if ($access == 0) { ?>dnl
 X_AD(<*You do not have the right to change section details*>)
<?php  } ?>dnl

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
?>dnl
B_HEADER(<*Configure section*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php p($Pub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php
    query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
    if ($NUM_ROWS) {
 query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
 if ($NUM_ROWS) {
  query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
  if ($NUM_ROWS) {
   query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_language');
   fetchRow($q_sect);
   fetchRow($q_iss);
   fetchRow($q_pub);
   fetchRow($q_language);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
X_CURRENT(<*Issue*>, <*<?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_language,'Name'); ?>)*>)
X_CURRENT(<*Section*>, <*<?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?>*>)
E_CURRENT

<P>
B_DIALOG(<*Configure section*>, <*POST*>, <*do_edit.php*>)
 B_DIALOG_INPUT(<*Name*>)
  <INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="64" value="<?php  pgetHVar($q_sect,'Name'); ?>">
 E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Section Template*>)
		<SELECT NAME="cSectionTplId" class="input_select">
			<OPTION VALUE="0">---</OPTION>
<?php 
	query ("SELECT Id, Name FROM Templates ORDER BY Level ASC, Name ASC", 'q_sect_tpl');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_sect_tpl);
		pcomboVar(getVar($q_sect_tpl,'Id'),getVar($q_sect,'SectionTplId'),getVar($q_sect_tpl,'Name'));
	}
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Article Template*>)
		<SELECT NAME="cArticleTplId" class="input_select">
			<OPTION VALUE="0">---</OPTION>
<?php 
	query ("SELECT Id, Name FROM Templates ORDER BY Level ASC, Name ASC", 'q_art_tpl');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRow($q_art_tpl);
		pcomboVar(getVar($q_art_tpl,'Id'),getVar($q_sect,'ArticleTplId'),getVar($q_art_tpl,'Name'));
	}
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Short Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cShortName" SIZE="32" MAXLENGTH="32" value="<?php  pgetHVar($q_sect,'ShortName'); ?>">
	E_DIALOG_INPUT
 B_DIALOG_INPUT(<*Subscriptions*>)
  <SELECT NAME="cSubs" class="input_select">
   <OPTION VALUE="n"> --- </OPTION>
   <OPTION VALUE="a"><?php  putGS("Add section to all subscriptions."); ?></OPTION>
   <OPTION VALUE="d"><?php  putGS("Delete section from all subscriptions."); ?></OPTION>
  </SELECT>
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
  <INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
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

