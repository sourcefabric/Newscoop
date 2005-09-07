INCLUDE_PHP_LIB(<*$ADMIN_DIR/infotype*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageClasses*>)

B_HEAD
	X_TITLE(<*Deleting infotype*>)
<?php  if ($access == 0) { ?>dnl
		X_AD(<*You do not have the right to delete glossary infotypes.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting infotype*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary infotypes*>, <*infotype/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    todefnum('Infotype');
    todefnum('Lang');
?>dnl
<P>
<?php 
    query ("DELETE FROM Classes  WHERE Id=$Infotype AND IdLanguage=$Lang");
    $affnr=$AFFECTED_ROWS;
    query ("SELECT COUNT(*) FROM Classes WHERE Id=$Infotype", 'q_cnt');
    fetchRowNum($q_cnt);
    if (getNumVar($q_cnt,0) == 0)
	query ("DELETE FROM KeywordClasses WHERE IdClasses=$Infotype");
?>dnl
B_MSGBOX(<*Deleting infotype*>)
<?php  if ($affnr > 0) { ?>
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The infotype has been deleted.'); ?></LI>*>)
X_AUDIT(<*82*>, <*getGS('Infotype $1 deleted',encHTML($cName))*>)
<?php  } else { ?>
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The infotype could not be deleted.'); ?></LI>*>)
<?php  } ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/infotype/*>)
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML


