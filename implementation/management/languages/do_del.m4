B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteLanguages*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting language*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete languages.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting language*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Languages*>, <*languages/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? todefnum('Language');
    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
    if ($NUM_ROWS) { 
	fetchRow($q_lang);
    ?>dnl

<P>
B_MSGBOX(<*Deleting language*>)
	X_MSGBOX_TEXT(<*
<? 
    $del= 1;
    query ("SELECT COUNT(*) FROM Publications WHERE IdDefaultLanguage=$Language", 'q_pub');
    fetchRowNum($q_pub);
    if (getNumVar($q_pub,0) != 0) {
	$del= 0; ?>dnl
	<LI><? putGS('There are $1 publication(s) left.',getNumVar($q_pub)); ?></LI>
    <? } 
    
    query ("SELECT COUNT(*) FROM Issues WHERE IdLanguage=$Language", 'q_iss');
    fetchRowNum($q_iss);
    if (getNumVar($q_iss,0) != 0) {
	$del= 0; ?>dnl
	<LI>T<? putGS('There are $1 issue(s) left.',getNumVar($q_iss)); ?></LI>
    <? } 
    
    query ("SELECT COUNT(*) FROM Sections WHERE IdLanguage=$Language", 'q_sect');
    fetchRowNum($q_sect);
    if (getNumVar($q_sect,0) != 0) {
	$del= 0; ?>dnl
	<LI><? putGS('There are $1 section(s) left.',getNumVar($q_sect)); ?></LI>
    <? } 
    
    query ("SELECT COUNT(*) FROM Articles WHERE IdLanguage=$Language", 'q_art');
    fetchRowNum($q_art);
    if (getNumVar($q_art,0) != 0) {
	$del= 0; ?>dnl
	<LI><? putGS('There are $1 article(s) left.',getNumVar($q_art)); ?></LI>
    <? } 
    
    query ("SELECT COUNT(*) FROM Dictionary WHERE IdLanguage=$Language", 'q_kwd');
    fetchRowNum($q_kwd);
    if (getNumVar($q_kwd,0) != 0) {
	$del= 0; ?>dnl
	<LI><? putGS('There are $1 keyword(s) left.',getNumVar($q_kwd)); ?></LI>
    <? }
    
    query ("SELECT COUNT(*) FROM Classes WHERE IdLanguage=$Language", 'q_cls');
    fetchRowNum($q_cls);
    if (getNumVar($q_cls,0) != 0) {
	$del= 0; ?>dnl
	<LI><? putGS('There are $1 classes(s) left.',getNumVar($q_cls)); ?></LI>
    <? }
    
    query ("SELECT COUNT(*) FROM Countries WHERE IdLanguage=$Language", 'q_country');
    fetchRowNum($q_country);
    if (getNumVar($q_country,0) != 0) {
	$del= 0; ?>dnl
	<LI><? putGS('There are $1 countries left.',getNumVar($q_country)); ?></LI>
    <? }

    $AFFECTED_ROWS=0;
    if ($del)
	query ("DELETE FROM Languages WHERE Id=$Language");
    if ($AFFECTED_ROWS > 0) { ?>
		<LI><? putGS('The language $1 has been deleted.','<B>'.getHVar($q_lang,'Name').'</B>'); ?></LI>
X_AUDIT(<*102*>, <*getGS('Language $1 deleted',getHVar($q_lang,'Name'))*>)
    <? } else { ?>
		<LI><? putGS('The language $1 could not be deleted.','<B>'.getHVar($q_lang,'Name').'</B>'); ?></LI>
    <? } ?>
	*>)
	B_MSGBOX_BUTTONS
<? if ($AFFECTED_ROWS > 0) { ?>
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/languages/*>)
<? } else { ?>
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/languages/*>)
<? } ?>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such language.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

