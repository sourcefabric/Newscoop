INCLUDE_PHP_LIB(<*$ADMIN_DIR/infotype*>)
B_DATABASE

CHECK_BASIC_ACCESS
B_HEAD
	X_TITLE(<*Glossary infotypes*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  }
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'ls');
    query ("SELECT Id, IdLanguage, Name FROM Classes WHERE 1=0", 'q_cls');
?>dnl
E_HEAD

<?php  if ($access) {
SET_ACCESS(<*mca*>, <*ManageClasses*>)
?>dnl

B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Glossary infotypes*>)
B_HEADER_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<?php  todef('sLang');
    todef('sName');
?>dnl
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<?php  if ($mca != 0) { ?>
	<TD>X_NEW_BUTTON(<*Add new glossary infotype*>, <*add.php*>)</TD>
	<?php  } ?>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*index.php*>)
		<TD><?php  putGS('Infotype'); ?>:</TD>
		<TD><INPUT TYPE="TEXT" class="input_text" NAME="sName" VALUE="<?php  print encHTML(decS($sName)); ?>"></TD>
		<TD><SELECT NAME="sLang" class="input_select"><?php  query ("SELECT Id, Name FROM Languages ORDER BY Name", 'ls');
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
    if ($sName != "")
	$kk= "Name LIKE '$sName%'";
    else
	$kk= "";

    if ($sLang != "")
	$ll= "IdLanguage = $sLang";
    else
	$ll= "";

    $ww= '';
    $aa= '';

    if ($sLang != "")
	$ww= "WHERE ";

    if ($sName != "") {
	if ($ww != "") {
	    $aa= " AND ";
	}
	$ww= "WHERE ";
    }

    $kwdid= "xxxxxx";

?>dnl

<P><?php 
    todefnum('ClsOffs');
    if ($ClsOffs < 0) $ClsOffs= 0;
    $lpp=20;

    query ("SELECT Id, IdLanguage, Name FROM Classes $ww$ll$aa$kk ORDER BY Id, IdLanguage LIMIT $ClsOffs, ".($lpp+1), 'q_cls');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= $lpp;
	$color= 0;
    ?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Infotype*>)
		X_LIST_TH(<*Language*>)
	<?php  if ($mca != 0) { ?> 
		X_LIST_TH(<*Translate*>, <*1%*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
	<?php  } ?>
	E_LIST_HEADER
<?php 
    for ($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_cls);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<?php  if (getVar($q_cls,'Id') == $kwdid) { ?>&nbsp; <?php  } pgetHVar($q_cls,'Name'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
<?php  
    query ("SELECT Name FROM Languages WHERE Id=".getVar($q_cls,'IdLanguage'), 'l');
    $nr2=$NUM_ROWS;
    for ($loop2=0;$loop2<$nr2;$loop2++) {
	fetchRow($l);
	pgetHVar($l,'Name');
	print '&nbsp;';
    }
    ?>&nbsp;
		E_LIST_ITEM
	<?php  if ($mca != 0) { ?> 
		B_LIST_ITEM(<*CENTER*>)
<?php  if (getVar($q_cls,'Id') != $kwdid) { ?>dnl
			<A HREF="X_ROOT/infotype/translate.php?Infotype=<?php  pgetUVar($q_cls,'Id'); ?>"><?php  putGS("Translate"); ?></A>
<?php  } ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<?php  putGS('Delete glossary infotype $1',getHVar($q_cls,'Name')); ?>*>, <*icon/x.gif*>, <*infotype/del.php?Infotype=<?php  pgetUVar($q_cls,'Id'); ?>&Lang=<?php  pgetUVar($q_cls,'IdLanguage'); ?>*>)
		E_LIST_ITEM
	<?php  }
if (getVar($q_cls,'Id') != $kwdid)
    $kwdid=getVar($q_cls,'Id');
?>dnl
	E_LIST_TR
<?php 
    $i--;
    }
}
?>dnl
	B_LIST_FOOTER
<?php  if ($ClsOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?sName=<?php  print encURL($sName); ?>&sLang=<?php   print encURL($sLang); ?>&ClsOffs=<?php  print ($ClsOffs - $lpp);?>*>)
<?php  }
    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?sName=<?php  print encURL($sName); ?>&sLang=<?php   print encURL($sLang); ?>&ClsOffs=<?php  print ($ClsOffs + $lpp);?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No glossary infotypes.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
