B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteArticleTypes*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting article type*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete article types.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting article type*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Article Types*>, <*a_types/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? todef('AType'); ?>dnl

<P>
B_MSGBOX(<*Deleting article type*>)
	X_MSGBOX_TEXT(<*
<?
    $del= 1;
    query ("SELECT COUNT(*) FROM Articles WHERE Type='$AType'", 'q_art');
    fetchRowNum($q_art);
    if (getNumVar($q_art,0) != 0) {
	$del= 0; ?>dnl
	<LI><? putGS('There are $1 article(s) left.',encHTML(getNumVar($q_art,0))); ?></LI>
    <? }
    
    if ($del)
	query ("DROP TABLE X$AType");
    if ($del) { ?>dnl
	<LI><? putGS('The article type $1 has been deleted.','<B>'.encHTML($AType).'</B>'); ?></LI>
X_AUDIT(<*62*>, <*getGS('The article type $1 has been deleted.',$AType)*>)
<? } else { ?>dnl
	<LI><? putGS('The article type $1 could not be deleted.','<B>'.encHTML($AType).'</B>'); ?></LI>
<? } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<? if ($del) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/a_types/*>)
<? } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/a_types/*>)
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

