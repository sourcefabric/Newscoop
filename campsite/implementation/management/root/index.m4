INCLUDE_PHP_LIB(<*./priv*>)
<HTML>
    <HEAD>
        X_EXPIRES
B_DATABASE<**>dnl
<?
    query ("SELECT Id, IdDefaultLanguage FROM Publications WHERE Site='$HTTP_HOST'", 'q_pub');
    if ($NUM_ROWS) {
	fetchRow($q_pub);
	query ("SELECT IdPublication, Number, IdLanguage, FrontPage FROM Issues WHERE IdPublication=".getVar($q_pub,'Id')." AND Published='Y' AND IdLanguage=".getSVar($q_pub,'IdDefaultLanguage')." ORDER BY Number DESC LIMIT 1", 'q_iss');
	fetchRow($q_iss);
	if ($NUM_ROWS == 0) {
	    query ("SELECT IdPublication, Number, IdLanguage, FrontPage FROM Issues WHERE IdPublication=".getVar($q_pub,'Id')." AND Published='Y' ORDER BY Number DESC LIMIT 1", 'q_iss');
	    fetchRow($q_iss);	    
	}
	if ($NUM_ROWS) { ?>dnl
	X_REFRESH(<*0; URL=http://<? p( $HTTP_HOST); ?><? pgetVar($q_iss,'FrontPage'); ?>?IdLanguage=<? pgetUVar($q_iss,'IdLanguage'); ?>&IdPublication=<? pgetUVar($q_pub,'Id'); ?>&NrIssue=<? pgetUVar($q_iss,'Number'); ?>*>)dnl
<? } else { ?>dnl
		Current issue not found.
<? } ?>dnl
<? } ?>
E_DATABASE<**>dnl
    </HEAD>
</HTML>
