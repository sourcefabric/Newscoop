B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ViewLogs*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Logs*>)
<? if ($access==0) { ?>dnl
	X_AD(<*You do not have the right to view logs.*>)
<? }
    query ("SELECT Id, Name FROM Events WHERE 1=0", 'ee');
    query ("SELECT TStamp, IdEvent, User, Text FROM Log WHERE 1=0", 'log'); 
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Logs*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? todefnum('sEvent'); ?>dnl
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*index.php*>)
		<TD><? putGS('Event'); ?>:</TD>
		<TD><SELECT NAME="sEvent"><OPTION VALUE="0"><?
		    query ("SELECT Id, Name FROM Events ORDER BY Id", 'ee');
		    $nr=$NUM_ROWS;
		    for ($loop=0;$loop<$nr;$loop++) {
			fetchRow($ee);
			print '<OPTION VALUE="';
			pgetHVar($ee,'Id');
			if (getVar($ee,'Id') == $sEvent)
			    print ' SELECTED';
			print '">';
			pgetHVar($ee,'Name');
		    }
		    ?></SELECT></TD>
		<TD><INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/search.gif" BORDER="0"></TD>
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<P><?
    todefnum('LogOffs');
    if ($LogOffs<0)  $LogOffs=0;
    $lpp=20;
    if ($sEvent!=0)
	$ww="WHERE IdEvent = $sEvent";
    else
	$ww='';
    query ("SELECT TStamp, IdEvent, User, Text FROM Log $ww ORDER BY TStamp DESC LIMIT $LogOffs, ".($lpp+1), 'log');
    if ($NUM_ROWS) {
	$nr=$NUM_ROWS;
	$i=$lpp;
	$color=0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Date/Time*>, <*15%*>)
		X_LIST_TH(<*User*>, <*1%*>)
<? if ($sEvent == 0) { ?>dnl
		X_LIST_TH(<*Event*>, <*10%*>)
<? } ?>dnl
		X_LIST_TH(<*Description*>)
	E_LIST_HEADER
<?
    for ($loop=0;$loop<$nr;$loop++) {
	fetchRow($log);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM(CENTER)
			<? pgetHVar($log,'TStamp'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<? pgetHVar($log,'User'); ?>&nbsp;
		E_LIST_ITEM
<? if ($sEvent ==0) { ?>dnl
		B_LIST_ITEM
			<? query ("SELECT Name FROM Events WHERE Id=".getVar($log,'IdEvent'), 'ev');
			$nrev=$NUM_ROWS;
			for($loop2=0;$loop2<$nrev;$loop2++) {
			    fetchRowNum($ev);
			    print decURL(encHTML(getNumVar($ev,0)));
			}
			?>&nbsp;
		E_LIST_ITEM
<? } ?>dnl
		B_LIST_ITEM
			<? pdecURL(getHVar($log,'Text')); ?>
		E_LIST_ITEM
	E_LIST_TR
<?
    $i--;
    }
}
 ?>dnl
	B_LIST_FOOTER
<? if ($LogOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?sEvent=<? print encURL($sEvent); ?>&LogOffs=<? print ($LogOffs - $lpp); ?>*>)
<? } 
    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?sEvent=<? print encURL($sEvent); ?>&LogOffs=<? print ($LogOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No events.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

