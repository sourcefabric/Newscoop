INCLUDE_PHP_LIB(<**>)
<HTML>
B_DATABASE<**>dnl
<?php 
    query ("SELECT Id, IdDefaultLanguage, Name FROM Publications WHERE Site='$HTTP_HOST'", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);
    ?>dnl
    <HEAD>
        <TITLE><?php  pgetHVar($q_pub,'Name'); ?></TITLE>
    </HEAD>
<?php 
    query ("SELECT * FROM Articles WHERE IdPublication=".getSVar($q_pub,'Id')." AND Published='Y' ORDER BY Number DESC LIMIT 200", 'q_art');
?>dnl
<BODY>
<?php 
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++){
	fetchRow($q_art);
	query ("SELECT COUNT(*) FROM Issues WHERE IdPublication=".getSVar($q_pub,'Id')." AND IdLanguage=".getSVar($q_art,'IdLanguage')." AND Published='Y'", 'q_iss');
	fetchRowNum($q_iss);
	if (getNumVar($q_iss,0)){
	    print "\t<P><A HREF=\"/jump.php?IdPublication=".getUVar($q_art,'IdPublication')."&NrIssue=".getUVar($q_art,'NrIssue')."&NrSection=".getUVar($q_art,'NrSection')."&Number=".getUVar($q_art,'Number')."&IdLanguage=".getUVar($q_art,'IdLanguage')."\">".getHVar($q_art,'Name')."</A>\n";
	}
    }
?>dnl
</BODY>
<?php  } ?>dnl
E_DATABASE<**>dnl
</HTML>
