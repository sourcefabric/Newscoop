<HTML>
INCLUDE_PHP_LIB(<**>)
B_DATABASE<**>

<HEAD>
    <META HTTP-EQUIV="Expires" CONTENT="now">
    <TITLE>Dictionary</TITLE>
</HEAD>

<BODY>

<?php  
    query("SELECT * FROM KeywordClasses WHERE IdDictionary=$keyword AND IdClasses=$class AND IdLanguage=$IdLanguage", 'kc');
    $nr=$NUM_ROWS;
    
    for($loop=0;$loop<$nr;$loop++){
	fetchRow($kc);
	query ("SELECT * FROM Dictionary WHERE Id=$keyword AND IdLanguage=$IdLanguage", 'kwd');
	query ("SELECT * FROM Classes WHERE Id=$class AND IdLanguage=$IdLanguage", 'cls');
	fetchRow($kwd);
	fetchRow($cls);
	?>dnl


<H1><?php  pgetHVar($kwd,'Keyword'); ?></H1>
<HR NOSHADE SIZE="1" COLOR="BLACK">
<H2><?php  pgetHVar($cls,'Name'); ?></H1>

<BLOCKQUOTE>
<?php  pgetHVar($kc,'Definition'); ?>
</BLOCKQUOTE>

<?php 
    query ("SELECT * FROM KeywordClasses WHERE IdDictionary=$keyword AND IdClasses != $class AND IdLanguage=$IdLanguage", 'kc2');
    $heading= 1;
    $nr2=$NUM_ROWS;
    for($loop2=0;$loop2<$nr2;$loop2++) {
	fetchRow($kc2);
	if ($heading) { ?>dnl
<H2>See also</H2>
<UL>
<?php 
	$heading= 0;
	}

	query ("SELECT * FROM Classes WHERE Id=".getSVar($kc,'IdClasses')." AND IdLanguage=$IdLanguage", 'cls');
	$nr3=$NUM_ROWS;
	for($loop3=0;$loop3<$nr3;$loop3++){
	    fetchRow($cls);
	    print "<LI><A HREF=\"$SCRIPT_NAME?class=".getUVar($cls,'Id&keyword')."=".encURL($keyword)."&IdLanguage=".encURL($IdLanguage)."\">".getHVar($cls,'Name')."</A></LI>";
	} //cls
    } //kc2
?>dnl
</UL>

<?php  
} //kc
?>dnl

</BODY>

E_DATABASE<**>
</HTML>
