INCLUDE_PHP_LIB(<*../../../..*>)dnl
B_DATABASE<**>dnl
<?    function printRows($q,$id,$s) {
	$nr=$GLOBALS['NUM_ROWS'];
	for($loop=0;$loop<$nr;$loop++) {
	    $arr=mysql_fetch_array($q,MYSQL_ASSOC);
		print $arr[$id]."\n";
		print $arr[$s]."\n";
	}
    }
    todefnum('IdLanguage');
    todefnum('IdPublication');
    todefnum('NrIssue');
    todefnum('NrSection');
    if ($IdLanguage) {
	if ($IdPublication) {
	    if ($NrIssue) {
		if ($NrSection) {
		    query ("SELECT Number, Name FROM Articles WHERE IdPublication=$IdPublication AND NrIssue=$NrIssue AND NrSection=$NrSection AND IdLanguage=$IdLanguage", 'q_art');
		    printRows($q_art,'Number','Name');
		} else {
		    query ("SELECT Number, Name FROM Sections WHERE IdPublication=$IdPublication AND NrIssue=$NrIssue AND IdLanguage=$IdLanguage", 'q_sect');
		    printRows($q_sect,'Number','Name');
		}
	    } else {
		query ("SELECT Number, Name FROM Issues WHERE IdPublication=$IdPublication AND IdLanguage=$IdLanguage ORDER BY Number DESC", 'q_iss');
		printRows($q_iss,'Number','Name');
	    }
	} else {
	    query ("SELECT Id, Name FROM Publications ORDER BY Id", 'q_pub');
	    printRows($q_pub,'Id','Name');
	}
    } else {	
	query ("SELECT Id, Name FROM Languages ORDER BY Id", 'q_lang');
	printRows($q_lang,'Id','Name');
    }
?>dnl
E_DATABASE<**>dnl