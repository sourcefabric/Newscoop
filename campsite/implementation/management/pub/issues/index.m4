B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
<script>
<!--
/*
A slightly modified version of "Break-out-of-frames script"
By JavaScript Kit (http://javascriptkit.com)
*/

if (window != top.fmain && window != top) {
	if (top.fmenu)
		top.fmain.location.href=location.href
	else
		top.location.href=location.href
}
// -->
</script>
	X_EXPIRES
	X_TITLE(<*Issues*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  }
    query ("SELECT * FROM Issues WHERE 1=0", 'q_iss');
?>dnl
E_HEAD

<?php  if ($access) { 
SET_ACCESS(<*mia*>, <*ManageIssue*>)
SET_ACCESS(<*dia*>, <*DeleteIssue*>)

?>dnl
B_STYLE
E_STYLE

B_BODY

<?php  todefnum('Pub'); ?>dnl

<?php  function tplRedirect($s){
	if (file_exists(getenv("DOCUMENT_ROOT")."/".decURL($s))){
		$dotpos=strrpos($s,"/");
		if($dotpos){
			$tplpath=substr ($s,0,$dotpos);
		} else $tplpath="LOOK_PATH";
	}	
	else $tplpath="LOOK_PATH";
	return $tplpath;
}?>dnl

B_HEADER(<*Issues*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php   query ("SELECT Name, IdDefaultLanguage FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);
	$IdLang = getVar($q_pub,'IdDefaultLanguage');
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT

<?php  if ($mia != 0) {
	query ("SELECT MAX(Number) FROM Issues WHERE IdPublication=$Pub", 'q_nr');
	fetchRowNum($q_nr);
	if (getNumVar($q_nr,0) == "") { ?>
	<P>X_NEW_BUTTON(<*Add new issue*>, <*add_new.php?Pub=<?php  pencURL($Pub); ?>*>)
	<?php  } else { ?>dnl
	<P>X_NEW_BUTTON(<*Add new issue*>, <*qadd.php?Pub=<?php  pencURL($Pub); ?>*>)
	<?php  }
    }
    $IssNr= "xxxxxxxxx";
?>
<P><?php 
    todefnum('IssOffs');
    if ($IssOffs < 0) $IssOffs= 0;
    $lpp=20;

    query ("SELECT Name, IdLanguage, abs(IdLanguage-$IdLang) as IdLang, Number, Name, PublicationDate, if($mia, 'Publish', 'No') as Pub, Published, FrontPage, SingleArticle  FROM Issues WHERE IdPublication=$Pub ORDER BY Number DESC, IdLang ASC LIMIT $IssOffs, ".($lpp+1), 'q_iss');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=$lpp;
	$color=0;
?>dnl
B_LIST
	B_LIST_HEADER
	<?php  if ($mia != 0) { ?>
		X_LIST_TH(<*Nr*>, <*1%*>)
		X_LIST_TH(<*Name<BR><SMALL>(click to see sections)</SMALL>*>)
		X_LIST_TH(<*Language*>)
		X_LIST_TH(<*Front Page Template<BR><SMALL>(click to change)</SMALL>*>)
		X_LIST_TH(<*Single Article Template<BR><SMALL>(click to change)</SMALL>*>)
		X_LIST_TH(<*Published<BR><SMALL>(yyyy-mm-dd)</SMALL>*>, <*1%*>)
		X_LIST_TH(<*Translate*>, <*1%*>)
		X_LIST_TH(<*Change*>, <*1%*>) 
		X_LIST_TH(<*Preview*>, <*1%*>)
	<?php  } else { ?>
		X_LIST_TH(<*Nr*>, <*1%*>)
		X_LIST_TH(<*Name<BR><SMALL>(click to see sections)</SMALL>*>)
		X_LIST_TH(<*Language*>)
		X_LIST_TH(<*Published<BR><SMALL>(yyyy-mm-dd)</SMALL>*>, <*1%*>)
		X_LIST_TH(<*Preview*>, <*1%*>)
	<?php  }
	
	if ($dia != 0) { ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
	<?php  } ?>
	E_LIST_HEADER

<?php 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_iss);
	if ($i) { ?>dnl
	B_LIST_TR
<?php  if ($mia != 0) { ?>
		B_LIST_ITEM(<*RIGHT*>)
	<?php  if ($IssNr != getVar($q_iss,'Number'))
		pgetHVar($q_iss,'Number');
	    else
		print '&nbsp;';
	 ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM
		<?php  if ($IssNr == getVar($q_iss,'Number')) print "&nbsp;";?>
			<A HREF="X_ROOT/pub/issues/sections/?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>"><?php  pgetHVar($q_iss,'Name'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM
	<?php  query ("SELECT Name FROM Languages WHERE Id=".getVar($q_iss,'IdLanguage'), 'language');
	    for($loop2=0;$loop2<$NUM_ROWS;$loop2++) {
		fetchRow($language);
		print getHVar($language,'Name');
	    } 
	 ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="<?php  p(tplRedirect(getHVar($q_iss,'FrontPage')));?>/?What=1&Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>"><?php  if (getVar($q_iss,'FrontPage') != "") { pdecURL(getHVar($q_iss,'FrontPage')); } else { putGS("Click here to set..."); } ?></A>
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="<?php  p(tplRedirect(getHVar($q_iss,'SingleArticle')));?>/?What=2&Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>"><?php  if (getVar($q_iss,'SingleArticle') != "") { pdecURL(getHVar($q_iss,'SingleArticle')); } else { putGS("Click here to set..."); } ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/pub/issues/status.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>"><?php  if (getHVar($q_iss, 'Published') == 'Y') pgetHVar($q_iss,'PublicationDate'); else print putGS(getHVar($q_iss,'Pub')); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
	<?php  if ($IssNr == getVar($q_iss,'Number')) { ?>dnl
			&nbsp;
	<?php  } else { ?>dnl
			<A HREF="X_ROOT/pub/issues/translate.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>"><?php  putGS("Translate"); ?></A>
	<?php  } ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/pub/issues/edit.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>"><?php  putGS("Change"); ?></A>
                E_LIST_ITEM 		B_LIST_ITEM(<*CENTER*>)
			<A HREF="" ONCLICK="window.open('X_ROOT/pub/issues/preview.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>', 'fpreview', PREVIEW_OPT); return false"><?php  putGS("Preview"); ?></A>
		E_LIST_ITEM
<?php  } else { ?>
		B_LIST_ITEM(<*RIGHT*>)
	<?php  if ($IssNr != getVar($q_iss,'Number')) {
		pgetHVar($q_iss,'Number');
	    } else { ?>dnl
		&nbsp;
	<?php  } ?>dnl
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>"><?php  pgetHVar($q_iss,'Name'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM
	<?php  query ("SELECT Name FROM Languages WHERE Id=".getVar($q_iss,'IdLanguage'), 'language');
	    for($loop2=0;$loop2<$NUM_ROWS;$loop2++) {
		fetchRow($language);
		print getHVar($language,'Name');
	    } 
	    
	?>dnl
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<?php  pgetHVar($q_iss,'Pub'); ?>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="" ONCLICK="window.open('X_ROOT/pub/issues/preview.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>', 'fpreview', PREVIEW_OPT); return false"><?php  putGS("Preview"); ?></A>
		E_LIST_ITEM
<?php  }
    
    if ($dia != 0) { ?> 
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<?php  putGS('Delete issue $1',getHVar($q_iss,'Name')); ?>*>, <*icon/x.gif*>, <*pub/issues/del.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>*>)
		E_LIST_ITEM
<?php  } ?>
	E_LIST_TR
<?php 
    $IssNr=getVar($q_iss,'Number');
    $i--;
    }
}

?>dnl
	B_LIST_FOOTER
<?php  if ($IssOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?Pub=<?php  pencURL($Pub); ?>&IssOffs=<?php  print ($IssOffs - $lpp); ?>*>)
<?php  }
    
    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?Pub=<?php  pencURL($Pub); ?>&IssOffs=<?php  print ($IssOffs + $lpp); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No issues.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
