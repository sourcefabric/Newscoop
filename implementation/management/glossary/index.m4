INCLUDE_PHP_LIB(<*$ADMIN_DIR/glossary*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_TITLE(<*Glossary*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  } 
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'ls');
    query ("SELECT Id, IdLanguage, Keyword FROM Dictionary WHERE 1=0", 'Dict');
?>dnl
E_HEAD

<?php  if ($access) { 
SET_ACCESS(<*mda*>, <*ManageDictionary*>)
SET_ACCESS(<*dda*>, <*DeleteDictionary*>)
?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Glossary*>)
B_HEADER_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<?php 
    todef('sKeyword');
    todef('sLang');
?>dnl
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<?php  if ($mda != 0) { ?>
	<TD>X_NEW_BUTTON(<*Add new keyword*>, <*add.php*>)</TD>
	<?php  } ?>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*index.php*>)
		<TD><?php  putGS('Keyword'); ?>:</TD>
		<TD><INPUT TYPE="TEXT" class="input_text" NAME="sKeyword" VALUE="<?php  print encHTML(decS($sKeyword)); ?>" SIZE="16" MAXLENGTH="32"></TD>
		<TD><SELECT NAME="sLang" class="input_select"><OPTION><?php 
		    query ("SELECT Id, Name FROM Languages ORDER BY Name", 'ls');

		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($ls);
			pcomboVar(getVar($ls,'Id'),$sLang,getVar($ls,'Name'));
		    } ?>
		    </SELECT></TD>
		<TD>SUBMIT(<*Search*>, <*Search*>)</TD>
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<?php 
    if ($sKeyword != "")
	$kk= "Keyword LIKE '$sKeyword%'";
    else
	$kk= "";

    if ($sLang != "")
	$ll="IdLanguage = $sLang";
    else
	$ll= "";
	
    $ww= "";
    $aa='';
    
    if ($sLang != "")
	$ww= "WHERE ";

    if ($sKeyword != "") {
	if ($ww != "")
	    $aa= " AND ";
	$ww= "WHERE ";
    }

    $kwdid= "xxxxxx";
    ?>dnl

<P><?php 
    todefnum('DictOffs');
    if ($DictOffs < 0) $DictOffs= 0;
    $lpp = 20;

    query ("SELECT Id, IdLanguage, Keyword FROM Dictionary $ww$ll$aa$kk ORDER BY Id, IdLanguage LIMIT $DictOffs, ".($lpp+1), 'Dict');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= $lpp;
	$color= 0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Keyword*>)
		X_LIST_TH(<*Language*>)
	<?php  if ($mda != 0) { ?>
		X_LIST_TH(<*Translate*>, <*1%*>)
	<?php  } ?>
		X_LIST_TH(<*Infotypes*>, <*1%*>)
	<?php  if ($dda != 0) { ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
	<?php  } ?>
	E_LIST_HEADER
	<?php  
	    for($loop=0;$loop<$nr;$loop++) {
		fetchRow($Dict);
		if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<?php  if (getVar($Dict,'Id') == $kwdid) { ?>&nbsp; <?php  } print getHVar($Dict,'Keyword'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
<?php  
    query ("SELECT Name FROM Languages WHERE Id=".getVar($Dict,'IdLanguage'), 'l');

    $nr2=$NUM_ROWS;
    for ($loop2=0;$loop2<$nr2;$loop2++) {
	fetchRow($l);
	pgetHVar($l,'Name');
	print '&nbsp;';
    }
    ?>&nbsp;
		E_LIST_ITEM
	<?php  if ($mda != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
<?php  if (getVar($Dict,'Id') != $kwdid) { ?>dnl
			<A HREF="X_ROOT/glossary/translate.php?Keyword=<?php  pgetUVar($Dict,'Id'); ?>"><?php  putGS("Translate"); ?></A>
<?php  } ?>&nbsp;
		E_LIST_ITEM
	<?php  } ?>
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/glossary/keyword/?Keyword=<?php  pgetHVar($Dict,'Id'); ?>&Language=<?php  pgetHVar($Dict,'IdLanguage'); ?>"><?php  putGS("Infotypes"); ?></A>
		E_LIST_ITEM

	<?php  if ($dda != 0) { ?> 
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*Delete keyword <?php  pgetHVar($Dict,'Keyword'); ?>*>, <*icon/x.gif*>, <*glossary/del.php?Keyword=<?php  pgetVar($Dict,'Id'); ?>&Language=<?php  pgetVar($Dict,'IdLanguage'); ?>*>)
		E_LIST_ITEM
	<?php  } ?>
<?php 
    if (getVar($Dict,'Id') != $kwdid)
	$kwdid= getVar($Dict,'Id');
?>dnl
	E_LIST_TR
	<?php 
	    $i--;
	    }
	}
	?>dnl    
	B_LIST_FOOTER
<?php 
    if ($DictOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?sKeyword=<?php  print encURL($sKeyword); ?>&sLang=<?php  print encURL($sLang); ?>&DictOffs=<?php  print ($DictOffs - $lpp); ?>*>)
<?php  }
    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?sKeyword=<?php  print encURL($sKeyword); ?>&sLang=<?php  print encURL($sLang); ?>&DictOffs=<?php  print ($DictOffs + $lpp); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No keywords.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

