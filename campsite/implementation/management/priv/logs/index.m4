INCLUDE_PHP_LIB(<*logs*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ViewLogs*>)

B_HEAD
	X_TITLE(<*Logs*>)
<?php  if ($access==0) { ?>dnl
	X_AD(<*You do not have the right to view logs.*>)
<?php  }
    query ("SELECT Id, Name FROM Events WHERE 1=0", 'ee');
    query ("SELECT TStamp, IdEvent, User, Text FROM Log WHERE 1=0", 'log'); 
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Logs*>)
B_HEADER_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<?php  todefnum('sEvent'); ?>dnl
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*index.php*>)
		<TD><?php  putGS('Event'); ?>:</TD>
		<TD><SELECT NAME="sEvent" class="input_select"><OPTION VALUE="0"><?php 
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
		<TD>SUBMIT(<*Search*>, <*Search*>)</TD>
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<P><?php 
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
<?php  if ($sEvent == 0) { ?>dnl
		X_LIST_TH(<*Event*>, <*20%*>)
<?php  } ?>dnl
		X_LIST_TH(<*Description*>)
	E_LIST_HEADER
<?php 
    for ($loop=0;$loop<$nr;$loop++) {
	fetchRow($log);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM(CENTER)
			<?php  pgetHVar($log,'TStamp'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<?php  pgetHVar($log,'User'); ?>&nbsp;
		E_LIST_ITEM
<?php  if ($sEvent ==0) { ?>dnl
		B_LIST_ITEM
			<?php  query ("SELECT Name FROM Events WHERE Id=".getVar($log,'IdEvent'), 'ev');
			$nrev=$NUM_ROWS;
			for($loop2=0;$loop2<$nrev;$loop2++) {
			    fetchRowNum($ev);
			    print decURL(encHTML(getNumVar($ev,0)));
			}
			?>&nbsp;
		E_LIST_ITEM
<?php  } ?>dnl
		B_LIST_ITEM
			<?php  pdecURL(getHVar($log,'Text')); ?>
		E_LIST_ITEM
	E_LIST_TR
<?php 
    $i--;
    }
}
 ?>dnl
	B_LIST_FOOTER
<?php  if ($LogOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?sEvent=<?php  print encURL($sEvent); ?>&LogOffs=<?php  print ($LogOffs - $lpp); ?>*>)
<?php  } 
    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?sEvent=<?php  print encURL($sEvent); ?>&LogOffs=<?php  print ($LogOffs + $lpp); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No events.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

