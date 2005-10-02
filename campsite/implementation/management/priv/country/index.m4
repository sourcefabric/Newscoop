INCLUDE_PHP_LIB(<*country*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*Countries*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  }
    query ("SELECT * FROM Countries WHERE 1=0", 'q_countries');
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'ls');
?>dnl
E_HEAD

<?php  if ($access) { 
SET_ACCESS(<*mca*>, <*ManageCountries*>)
SET_ACCESS(<*dca*>, <*DeleteCountries*>)
}
?>dnl

B_STYLE
E_STYLE

B_BODY

<?php  todefnum('sLanguage'); ?>dnl
B_HEADER(<*Countries*>)
B_HEADER_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<P><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<?php  if ($mca != 0) { ?>
	<TD>X_NEW_BUTTON(<*Add new country*>, <*add.php?Back=<?php print urlencode($_SERVER['REQUEST_URI']); ?>*>)</TD>
	<?php  } ?>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*index.php*>)
		<TD><?php  putGS('Language') ?>:</TD>
		<TD><SELECT NAME="sLanguage" class="input_select"><OPTION><?php 
		    query ("SELECT Id, Name FROM Languages ORDER BY Name", 'ls');
		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($ls);
			pcomboVar(getVar($ls,'Id'),$sLanguage,getVar($ls,'Name'));
		    }
		?></SELECT></TD>
		<TD>SUBMIT(<*Search*>, <*Search*>)</TD>
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<?php 
    if ($sLanguage) {
	$ll= " AND IdLanguage=$sLanguage";
        $oo= ", IdLanguage";
    }
    else {
	$ll= "";
	$oo= "";
    }

    $kwdid= "ssssssssss";
    todefnum('CtrOffs');
    if ($CtrOffs < 0) $CtrOffs= 0;
    $lpp=20;
    query ("SELECT * FROM Countries WHERE Code != \"\"$ll ORDER BY Code$oo LIMIT $CtrOffs, ".($lpp+1), 'q_countries');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= $lpp;
	if($nr < $lpp) $i = $nr;
	$color= 0; ?>dnl
B_LIST
	B_LIST_HEADER
<?php  if ($mca != 0) { ?>dnl
		X_LIST_TH(<*Name <SMALL>(click to edit)</SMALL>*>)
<?php  } else { ?>dnl
		X_LIST_TH(<*Name*>)
<?php  } ?>dnl
		X_LIST_TH(<*Language*>, <*1%*>)
		X_LIST_TH(<*Code*>, <*1%*>)
<?php  if ($mca != 0) { ?>dnl
		X_LIST_TH(<*Translate*>, <*1%*>)
<?php  }
if ($dca != 0) { ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
<?php  } ?>dnl
	E_LIST_HEADER
<?php  for ($loop=0;$loop<$i;$loop++) {
    fetchRow($q_countries); ?>dnl
	B_LIST_TR
<?php  if ($mca != 0) { ?>dnl
		B_LIST_ITEM
			<?php  if (getVar($q_countries,'Code') == $kwdid) print '&nbsp;'; ?><A HREF="X_ROOT/country/edit.php?Code=<?php  encURL(pgetUVar($q_countries,'Code')); ?>&Language=<?php  pgetUVar($q_countries,'IdLanguage'); ?>"><?php  pgetHVar($q_countries,'Name'); ?>&nbsp;</A>
		E_LIST_ITEM
<?php  } else { ?>dnl
		B_LIST_ITEM
			<?php  if (getVar($q_countries,'Code') == $kwdid) print '&nbsp;'; ?><?php  pgetHVar($q_countries,'Name'); ?>&nbsp;
		E_LIST_ITEM
<?php  } ?>dnl
		B_LIST_ITEM
<?php  query ("SELECT Name FROM Languages WHERE Id=".getVar($q_countries,'IdLanguage'), 'q_ail');
    fetchRow($q_ail);
    pgetHVar($q_ail,'Name'); ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
<?php  if (getVar($q_countries,'Code') != $kwdid)
	pgetHVar($q_countries,'Code');
    else
	print '&nbsp;'; ?>dnl
		E_LIST_ITEM
	<?php  if ($mca != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
<?php  if (getVar($q_countries,'Code') != $kwdid) { ?>dnl
			<A HREF="X_ROOT/country/translate.php?Code=<?php  encURL(pgetUVar($q_countries,'Code')); ?>&Language=<?php  pgetUVar($q_countries,'IdLanguage'); ?>"><?php  putGS("Translate"); ?></A>
<?php  } else { ?>dnl
&nbsp;
<?php  } ?>dnl
		E_LIST_ITEM
	<?php  }
	if ($dca != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*Delete country <?php  pgetHVar($q_countries,'Name'); ?>*>, <*/x.gif*>, <*country/del.php?Code=<?php  encURL(pgetUVar($q_countries,'Code')); ?>&Language=<?php  pgetUVar($q_countries,'IdLanguage'); ?>*>)
		E_LIST_ITEM
	<?php  } ?>
	E_LIST_TR
<?php  $kwdid=getVar($q_countries,'Code');
} ?>dnl
	B_LIST_FOOTER
<?php  if ($CtrOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?sLanguage=<?php  print encURL($sLanguage); ?>&CtrOffs=<?php  print ($CtrOffs - $lpp); ?>*>)
<?php  } ?>dnl
<?php  if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?sLanguage=<?php  print encURL($sLanguage); ?>&CtrOffs=<?php  print ($CtrOffs + $lpp); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No countries.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY

E_DATABASE
E_HTML

