B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*AddArticle*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new article*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add articles.*>)
<? }
    
    query ("SELECT * FROM Issues WHERE 1=0", 'q_iss');
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<? todefnum('Pub'); ?>dnl
B_HEADER(<*Add new article*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/add_article.php*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT

<P>
X_BULLET(<*Select the issue*>)

<? $IssNr= "xxxxxxxxx"; ?>dnl
<P><?
    todefnum('IssOffs');
    if ($IssOffs < 0)
	$IssOffs= 0;
        $lpp=10;
    
    query ("SELECT Name, IdLanguage, Number, Name, if(Published='Y', PublicationDate, 'No') as Pub FROM Issues  WHERE IdPublication=$Pub ORDER BY Number DESC LIMIT $IssOffs, ".($lpp+1), 'q_iss');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= $lpp;
                        if($nr < $lpp) $i = $nr;
	$color= 0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Nr*>, <*1%*>)
		X_LIST_TH(<*Name<BR><SMALL>(click to select the issue)</SMALL>*>)
		X_LIST_TH(<*Language*>)
		X_LIST_TH(<*Published<BR><SMALL>(yyyy-mm-dd)</SMALL>*>, <*1%*>)
	E_LIST_HEADER

<?
    for($loop=0;$loop<$i;$loop++) {
	fetchRow($q_iss); ?>dnl
	B_LIST_TR
		B_LIST_ITEM(<*RIGHT*>)
 <? if ($IssNr != getVar($q_iss,'Number')) {
		pgetHVar($q_iss,'Number');
	    }
	    else {
		p('&nbsp;');
	    }
	?>dnl
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/add_article.php?Pub=<? p($Pub); ?>&Issue=<? pgetUVar($q_iss,'Number'); ?>&Language=<? pgetUVar($q_iss,'IdLanguage'); ?>"><? pgetHVar($q_iss,'Name'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM
 <?
	    query ("SELECT Name FROM Languages WHERE Id=".getSVar($q_iss,'IdLanguage'), 'language');
	    $nr2=$NUM_ROWS;
	    for($loop2=0;$loop2<$nr2;$loop2++) {
		fetchRow($language);
		pgetHVar($language,'Name');
	    }
	    ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<? pgetHVar($q_iss,'Pub'); ?>
		E_LIST_ITEM
	E_LIST_TR
<?
    $IssNr=getVar($q_iss,'Number');
}
?>dnl
	B_LIST_FOOTER
<? if ($IssOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*add_article.php?Pub=<? p($Pub); ?>&IssOffs=<? p($IssOffs - $lpp); ?>*>)
<? }

    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*add_article.php?Pub=<? p($Pub); ?>&IssOffs=<? p($IssOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No issues.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
