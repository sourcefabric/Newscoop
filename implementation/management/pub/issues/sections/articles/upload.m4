INCLUDE_PHP_LIB(<*../../../..*>)dnl
B_DATABASE()dnl
<?
    todefnum('UserId');
    if ($UserId == "")
	$UserId= 0;

    todefnum('UserKey');
    if ($UserKey == "")
	$UserKey= 0;

    todefnum('IdPublication');
    if ($IdPublication == "")
	$IdPublication= 0;

    todefnum('NrIssue');
    if ($NrIssue == "")
	$NrIssue= 0;

    todefnum('NrSection');
    if ($NrSection == "")
	$NrSection= 0;

    todefnum('NrArticle');
    if ($NrArticle == "")
	$NrArticle= 0;

    todefnum('IdLanguage');
    if ($IdLanguage == "")
	$IdLanguage= 0;
    
    todef('Field');

    todef('Content');

    query ("SELECT COUNT(*) FROM Users WHERE Id=$UserId AND KeyId=$UserKey", 'Usr');
    fetchRowNum($Usr);
    if (getNumVar($Usr,0) != 0) {
	query ("SELECT Type, LockUser FROM Articles WHERE IdPublication=$IdPublication AND NrIssue=$NrIssue AND NrSection=$NrSection AND Number=$NrArticle AND IdLanguage=$IdLanguage", 'q_art');
	if ($NUM_ROWS) {
	    fetchRow($q_art);
	    if (getVar($q_art,'LockUser') == $UserId) {
		query ("UPDATE X".getSVar($q_art,'Type')." SET F$Field='".$Content."' WHERE NrArticle=$NrArticle AND IdLanguage=$IdLanguage");
		if ($AFFECTED_ROWS > 0)
		    print "TOLOK\n\n";
		else
		    print "TOLERR\n\n";
	    } else {
		print "TOLLOCK\n\n";
	    }
	} else {
	    print "TOLERR\n\n";
	}
    } else {
	print "TOLERR\n\n";
    }
?>dnl
E_DATABASE
