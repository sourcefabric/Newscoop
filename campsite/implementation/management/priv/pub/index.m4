INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
    X_TITLE(<*Publications*>)
<?php  if ($access == 0) { ?>dnl
    X_LOGOUT
<?php  }
    query ("SELECT * FROM Publications WHERE 1=0", 'publ');
    query("SELECT  Id as IdLang FROM Languages WHERE code='$_COOKIE[TOL_Language]'", 'q_lang');
    if($NUM_ROWS == 0){
        query("SELECT IdDefaultLanguage as IdLang  FROM Publications WHERE Id=1", 'q_lang');
    }
    fetchRow($q_lang);
    $IdLang = getVar($q_lang,'IdLang');

?>dnl
E_HEAD

<?php  if ($access) {
SET_ACCESS(<*mpa*>, <*ManagePub*>)
SET_ACCESS(<*dpa*>, <*DeletePub*>)
?>dnl

B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Publications*>)
B_HEADER_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<?php  if ($mpa != 0) { ?>dnl
    <P>
    <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
    <TR>
    	<TD>
    		<A HREF="add.php?Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A>
    	</TD>
    	<TD>
    		<A HREF="add.php?Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><B><?php  putGS("Add new publication"); ?></B></A>
    	</TD>
    </TR>
    </TABLE>
<?php  } ?>dnl

<P><?php
    todefnum('PubOffs');
    if ($PubOffs < 0) $PubOffs= 0;
    $lpp=20;

	$sql = "SELECT p.Id, p.Name, a.Name as Alias, u.Name as URLType, l.OrigName FROM "
	     . "Publications as p, Aliases as a, URLTypes as u, Languages as l WHERE "
	     . "p.IdDefaultAlias = a.Id AND p.IdURLType = u.Id AND p.IdDefaultLanguage = l.Id "
	     . "ORDER BY p.Name ASC LIMIT $PubOffs, ".($lpp+1);
	query($sql, 'publ');
    if ($NUM_ROWS) {
    $nr= $NUM_ROWS;
    $i= $lpp;
    $color= 0;
?>dnl
B_LIST
    B_LIST_HEADER
        X_LIST_TH(<*Name<BR><SMALL>(click to see issues)</SMALL>*>)
        X_LIST_TH(<*Default Site Alias*>, <*20%*>)
        X_LIST_TH(<*Default Language*>, <*20%*>)
    <?php  if ($mpa != 0) { ?>dnl
        X_LIST_TH(<*URL Type*>, <*20%*>)
        X_LIST_TH(<*Configure*>, <*1%*>)
    <?php  }
    if ($dpa != 0) { ?>dnl
        X_LIST_TH(<*Delete*>, <*1%*>)
    <?php  } ?>dnl
    E_LIST_HEADER
<?php
    for($loop=0;$loop<$nr;$loop++) {
    fetchRow($publ);
    if ($i) { ?>dnl
    B_LIST_TR
        B_LIST_ITEM
            <A HREF="X_ROOT/issues/?Pub=<?php  pgetUVar($publ,'Id'); ?>"><?php  pgetHVar($publ,'Name'); ?></A>
        E_LIST_ITEM
        B_LIST_ITEM
            <?php  pgetHVar($publ,'Alias'); ?>&nbsp;
        E_LIST_ITEM
        B_LIST_ITEM
            <?php  pgetHVar($publ,'OrigName'); ?>&nbsp;
        E_LIST_ITEM
<?php  if ($mpa != 0) { ?>dnl
        B_LIST_ITEM
            <?php  pgetHVar($publ,'URLType'); ?>&nbsp;
        E_LIST_ITEM
        B_LIST_ITEM(<*CENTER*>)
            <A HREF="X_ROOT/pub/edit.php?Pub=<?php  pgetUVar($publ,'Id'); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/configure.png" alt="<?php  putGS("Configure"); ?>" title="<?php  putGS("Configure"); ?>"  border="0"></A>
        E_LIST_ITEM
<?php  }
    if ($dpa != 0) { ?>dnl
        B_LIST_ITEM(<*CENTER*>)
            X_BUTTON(<*<?php  putGS('Delete publication $1',getHVar($publ,'Name')); ?>*>, <*/delete.png*>, <*pub/del.php?Pub=<?php  pgetVar($publ,'Id'); ?>*>)
        E_LIST_ITEM
<?php  } ?>dnl
    E_LIST_TR
<?php
    $i--;
    }
} ?>dnl
    B_LIST_FOOTER
<?php  if ($PubOffs <= 0) { ?>dnl
        X_PREV_I
<?php  } else { ?>dnl
        X_PREV_A(<*index.php?PubOffs=<?php  print ($PubOffs - $lpp); ?>*>)
<?php  } ?>dnl
<?php  if ($nr < $lpp+1) { ?>dnl
        X_NEXT_I
<?php  } else { ?>dnl
        X_NEXT_A(<*index.php?PubOffs=<?php  print ($PubOffs + $lpp); ?>*>)
<?php  } ?>dnl
    E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
    <LI><?php  putGS('No publications.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
