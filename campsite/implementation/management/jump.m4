INCLUDE_PHP_LIB(<**>)
<HTML>
	<HEAD>
B_DATABASE<**>dnl
<?php 
    todef('HTTP_USER_AGENT', "XXX");
    query ("SELECT ('$HTTP_USER_AGENT' LIKE '%Mozilla%') OR ('$HTTP_USER_AGENT' LIKE '%MSIE%')", 'q_nav');
    fetchRowNum($q_nav);
    query ("SHOW FIELDS FROM Articles LIKE 'XXYYZZ'", 'q_fld');
    
    todefnum('IdPublication');
    todefnum('NrIssue');
    todefnum('NrSection');
    todefnum('Number');
    todefnum('IdLanguage');

    $ok= 0;

    query ("SELECT * FROM Publications WHERE Id=$IdPublication", 'q_pub');
    if ($NUM_ROWS) {
	fetchRow($q_pub);
	query ("SELECT * FROM Issues WHERE IdPublication=$IdPublication AND Number=$NrIssue AND IdLanguage=$IdLanguage", 'q_iss');
	if ($NUM_ROWS) {
	    $ok= 1;
	}
	else{    
	    query ("SELECT * FROM Issues WHERE IdPublication=$IdPublication AND Number=$NrIssue AND IdLanguage=".getSVar($q_pub,'IdDefaultLanguage'), 'q_iss');
	    if ($NUM_ROWS){
		$ok= 1;
	    }
	    else{
		query ("SELECT * FROM Issues WHERE IdPublication=$IdPublication AND Number=$NrIssue LIMIT 1", 'q_iss');
		if ($NUM_ROWS){
		    $ok= 1;
		}
		else{ ?>dnl
	</HEAD>
<BODY>
	No such issue.
</BODY>
		<?php  
		}
	    }
	}
    }
    else{ ?>dnl
	</HEAD>
<BODY>
	No such publication.
</BODY>
<?php  }

if ($ok) {
    query ("SELECT * FROM Articles WHERE IdPublication=$IdPublication AND NrIssue=$NrIssue AND NrSection=$NrSection AND  Number=$Number AND IdLanguage=$IdLanguage", 'q_art');
    if ($NUM_ROWS) {
	fetchRow($q_art);
	fetchRow($q_lang);
	query ("SELECT * FROM Languages WHERE Id=$IdLanguage", 'q_lang');
	if ($NUM_ROWS) { ?>dnl
	<TITLE><?php  pgetHVar($q_art,'Name'); ?></TITLE>
	<META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=<?php  pgetHVar($q_lang,'CodePage'); ?>">
	<META HTTP-EQUIV="Keywords" CONTENT="<?php  pgetHVar($q_art,'Keywords'); ?>">
	</HEAD>
<BODY BGCOLOR=WHITE TEXT=WHITE LINK=WHITE ALINK=WHITE VLINK=WHITE onload="param='IdPublication=<?php  p($IdPublication); ?>&NrIssue=<?php  p($NrIssue); ?>&NrSection=<?php  p($NrSection); ?>&NrArticle=<?php  p($Number); ?>&IdLanguage=<?php  p($IdLanguage); ?>';site='<?php  pgetVar($q_pub,'Site'); ?>'; path='<?php  pgetVar($q_iss,'SingleArticle'); ?>'; document.location.replace('http://' + site + path + '?' + param);">
<?php 
    if (getNumVar($q_nav,0) == 0) {
	query ("SHOW FIELDS FROM X".getSVar($q_art,'Type')." LIKE 'F%'", 'q_fld');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
	    fetchRowNum($q_fld);
	    query ("SELECT REPLACE(REPLACE(".encS(getNumVar($q_fld,0)).", '<', '<!-- '), '>', ' -->') FROM X".getSVar($q_art,'Type')." WHERE NrArticle=$Number AND IdLanguage=$IdLanguage", 'q_fff');
	    fetchRowNum($q_fff);
	    ?>dnl	    
<p><?php  pgetVar($q_fff,0);
	}
    }
?>dnl
</BODY>
<?php  } else { ?>dnl
	</HEAD>
<BODY>
	No such language.
</BODY>
<?php  }
} else { ?>dnl
	</HEAD>
<BODY>
	No such article.
</BODY>
<?php  }
} ?>dnl

E_DATABASE<**>dnl
</HTML>
