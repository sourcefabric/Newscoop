B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteLanguages*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting language*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete languages.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
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

<?php  todefnum('Language');
    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
    if ($NUM_ROWS) { 
	fetchRow($q_lang);
    ?>dnl

<P>
B_MSGBOX(<*Deleting language*>)
	X_MSGBOX_TEXT(<*
<?php  
    $del= 1;
    query ("SELECT COUNT(*) FROM Publications WHERE IdDefaultLanguage=$Language", 'q_pub');
    fetchRowNum($q_pub);
    if (getNumVar($q_pub,0) != 0) {
	$del= 0; ?>dnl
	<LI><?php  putGS('There are $1 publication(s) left.',getNumVar($q_pub)); ?></LI>
    <?php  } 
    
    query ("SELECT COUNT(*) FROM Issues WHERE IdLanguage=$Language", 'q_iss');
    fetchRowNum($q_iss);
    if (getNumVar($q_iss,0) != 0) {
	$del= 0; ?>dnl
	<LI>T<?php  putGS('There are $1 issue(s) left.',getNumVar($q_iss)); ?></LI>
    <?php  } 
    
    query ("SELECT COUNT(*) FROM Sections WHERE IdLanguage=$Language", 'q_sect');
    fetchRowNum($q_sect);
    if (getNumVar($q_sect,0) != 0) {
	$del= 0; ?>dnl
	<LI><?php  putGS('There are $1 section(s) left.',getNumVar($q_sect)); ?></LI>
    <?php  } 
    
    query ("SELECT COUNT(*) FROM Articles WHERE IdLanguage=$Language", 'q_art');
    fetchRowNum($q_art);
    if (getNumVar($q_art,0) != 0) {
	$del= 0; ?>dnl
	<LI><?php  putGS('There are $1 article(s) left.',getNumVar($q_art)); ?></LI>
    <?php  } 
    
    query ("SELECT COUNT(*) FROM Dictionary WHERE IdLanguage=$Language", 'q_kwd');
    fetchRowNum($q_kwd);
    if (getNumVar($q_kwd,0) != 0) {
	$del= 0; ?>dnl
	<LI><?php  putGS('There are $1 keyword(s) left.',getNumVar($q_kwd)); ?></LI>
    <?php  }
    
    query ("SELECT COUNT(*) FROM Classes WHERE IdLanguage=$Language", 'q_cls');
    fetchRowNum($q_cls);
    if (getNumVar($q_cls,0) != 0) {
	$del= 0; ?>dnl
	<LI><?php  putGS('There are $1 classes(s) left.',getNumVar($q_cls)); ?></LI>
    <?php  }
    
    query ("SELECT COUNT(*) FROM Countries WHERE IdLanguage=$Language", 'q_country');
    fetchRowNum($q_country);
    if (getNumVar($q_country,0) != 0) {
	$del= 0; ?>dnl
	<LI><?php  putGS('There are $1 countries left.',getNumVar($q_country)); ?></LI>
    <?php  }

    $AFFECTED_ROWS=0;
    if ($del)
	query ("DELETE FROM Languages WHERE Id=$Language");
    if ($AFFECTED_ROWS > 0) { ?>
		<LI><?php  putGS('The language $1 has been deleted.','<B>'.getHVar($q_lang,'Name').'</B>'); ?></LI>
X_AUDIT(<*102*>, <*getGS('Language $1 deleted',getHVar($q_lang,'Name'))*>)
    <?php  } else { ?>
		<LI><?php  putGS('The language $1 could not be deleted.','<B>'.getHVar($q_lang,'Name').'</B>'); ?></LI>
    <?php  } ?>
	*>)
	B_MSGBOX_BUTTONS
<?php  if ($AFFECTED_ROWS > 0) { ?>
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/languages/*>)
<?php  } else { ?>
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/languages/*>)
<?php  } ?>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such language.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

