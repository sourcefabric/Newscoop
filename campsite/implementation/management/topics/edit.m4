INCLUDE_PHP_LIB(<*$ADMIN_DIR/topics*>)
B_DATABASE

<?php 
    query ("SELECT Id, Name FROM Topics WHERE 1=0", 'q_cat');
?>dnl
CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTopics*>)

B_HEAD
	X_TITLE(<*Change topic name*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change topic name.*>)
<?php  }
    query ("SELECT Id, Name FROM Topics WHERE 1=0", 'q_cat');
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
	todefnum('IdCateg');
	todefnum('EdCateg');
?>
B_HEADER(<*Change topic name*>)
B_HEADER_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT * FROM Topics WHERE Id=$EdCateg", 'q_cat');
    if ($NUM_ROWS) { 
	fetchRow($q_cat);
?>dnl
B_CURRENT
X_CURRENT(<*Topic*>, <*<?php  pgetHVar($q_cat,'Name'); ?>*>)
E_CURRENT

<P>
B_DIALOG(<*Change topic name*>, <*POST*>, <*do_edit.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" VALUE="<?php  pgetHVar($q_cat,'Name'); ?>" SIZE="32" MAXLENGTH="255">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="IdCateg" VALUE="<?php  p($IdCateg); ?>">
		<INPUT TYPE="HIDDEN" NAME="EdCateg" VALUE="<?php  p($EdCateg); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/topics/index.php?IdCateg=<?php p($IdCateg);?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such topic.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
