INCLUDE_PHP_LIB(<*../../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Images*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  }
    query ("SELECT * FROM Images WHERE 1=0", 'q_img');
?>dnl
E_HEAD

<?php  if ($access) { 

SET_ACCESS(<*aia*>, <*AddImage*>)
SET_ACCESS(<*cia*>, <*ChangeImage*>)
SET_ACCESS(<*dia*>, <*DeleteImage*>)
?>dnl
B_STYLE
E_STYLE

B_BODY
<?php 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Article');
    todefnum('Language');
    todefnum('sLanguage');
?>
B_HEADER(<*Images*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Articles*>, <*pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT * FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article", 'q_art');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	    if ($NUM_ROWS) {
		query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
		if ($NUM_ROWS) {
		    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');

		    fetchRow($q_art);
		    fetchRow($q_sect);
		    fetchRow($q_iss);
		    fetchRow($q_pub);
		    fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B>*>)
X_CURRENT(<*Article*>, <*<B><?php  pgetHVar($q_art,'Name'); ?></B>*>)
E_CURRENT

<table>
<?php  if ($aia != 0) { ?>
<tr><td>X_NEW_BUTTON(<*Add new image*>, <*add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>*>)</td>
<td>X_NEW_BUTTON(<*Select an old image*>, <*select.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>*>)</td></tr>
<?php  } ?>
<tr><td>X_NEW_BUTTON(<*Back to article details*>, <*../edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>*>)</td></tr>
</table>

<P><?php 
    todefnum('ImgOffs');
    if ($ImgOffs < 0) $ImgOffs= 0;
    todefnum('lpp', 20);

    query ("SELECT * FROM Images WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND NrArticle=$Article ORDER BY Number LIMIT $ImgOffs, ".($lpp+1), 'q_img');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=$lpp;
	$color= 0;
	?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Nr*>, <*1%*>)
		X_LIST_TH(<*Click to view image*>)
		X_LIST_TH(<*Photographer*>)
		X_LIST_TH(<*Place*>)
		X_LIST_TH(<*Date<BR><SMALL>(yyyy-mm-dd)</SMALL>*>)
	<?php  if ($cia != 0) { ?>
		X_LIST_TH(<*Info*>, <*1%*>)
	<?php  }
	    
	    if ($dia != 0) { ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
	<?php  } ?>
	E_LIST_HEADER
<?php 
    for($loop=0; $loop<$nr; $loop++) {
	fetchRow($q_img);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM(<*RIGHT*>)
			<?php  pgetHVar($q_img,'Number'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/articles/images/view.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Image=<?php  pgetUVar($q_img,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>"><?php  pgetHVar($q_img,'Description'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM
			<?php  pgetHVar($q_img,'Photographer'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
			<?php  pgetHVar($q_img,'Place'); ?>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
			<?php  pgetHVar($q_img,'Date'); ?>
		E_LIST_ITEM
	<?php  if ($cia != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			<A HREF="X_ROOT/pub/issues/sections/articles/images/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Image=<?php  pgetUVar($q_img,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>"><?php  putGS("Change");?></A>
		E_LIST_ITEM
	<?php  }
	    if ($dia != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<?php  putGS('Delete image $1',getHVar($q_img,'Description')); ?>*>, <*icon/x.gif*>, <*pub/issues/sections/articles/images/del.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Image=<?php  pgetHVar($q_img,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>*>)
		E_LIST_ITEM
	<?php  } ?>
	E_LIST_TR
<?php 
    $i--;
    }
}
?>dnl
	B_LIST_FOOTER
<?php  if ($ImgOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&ImgOffs=<?php  p($ImgOffs - $lpp); ?>*>)
<?php  } ?>dnl
<?php  if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&ImgOffs=<?php  p($ImgOffs + $lpp); ?>*>)
<?php  } ?>dnl
	E_LIST_FOOTER
E_LIST
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No images.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such section.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such article.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

