B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageClasses*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting infotype*>)
<? if ($access == 0) { ?>dnl
		X_AD(<*You do not have the right to delete glossary infotypes.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting infotype*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Glossary infotypes*>, <*infotype/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('Class');
    todefnum('Lang');
?>dnl
<P>
<?
    query ("DELETE FROM Classes  WHERE Id=$Class AND IdLanguage=$Lang");
    $affnr=$AFFECTED_ROWS;
    query ("SELECT COUNT(*) FROM Classes WHERE Id=$Class", 'q_cnt');
    fetchRowNum($q_cnt);
    if (getNumVar($q_cnt,0) == 0)
	query ("DELETE FROM KeywordClasses WHERE IdClasses=$Class");
?>dnl
B_MSGBOX(<*Deleting infotype*>)
<? if ($affnr > 0) { ?>
	X_MSGBOX_TEXT(<*<LI><? putGS('The infotype has been deleted.'); ?></LI>*>)
X_AUDIT(<*82*>, <*getGS('Infotype $1 deleted',encHTML($cName))*>)
<? } else { ?>
	X_MSGBOX_TEXT(<*<LI><? putGS('The infotype could not be deleted.'); ?></LI>*>)
<? } ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/infotype/*>)
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML


