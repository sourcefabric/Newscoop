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

<?php 
	todef('sDescription');		$sDescription =  decURL($sDescription);
	todef('sPhotographer');	$sPhotographer  = decURL($sPhotographer);
	todef('sPlace');			$sPlace = decURL($sPlace);
	todef('cIssue');
	todefnum('ImgOffs');
	if ($ImgOffs < 0) $ImgOffs= 0;
	todefnum('lpp', 20);
?>

X_NEW_BUTTON(<*Back to current article*>, <*./?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>*>)

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<TD></TD>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*select.php*>)
		<TD><?php  putGS('Issue'); ?></TD>
		<TD><INPUT TYPE="TEXT" NAME="cIssue" VALUE="<?php  p($cIssue); ?>" SIZE="4" MAXLENGTH="8"></TD>
		<TD><?php  putGS('Description'); ?></TD>
		<TD><INPUT TYPE="TEXT" NAME="sDescription" VALUE="<?php  pencHTML($sDescription); ?>" SIZE="16" MAXLENGTH="32"></TD>
		<TD><?php  putGS('Photographer'); ?></TD>
		<TD><INPUT TYPE="TEXT" NAME="sPhotographer" VALUE="<?php  pencHTML($sPhotographer); ?>" SIZE="8" MAXLENGTH="32"></TD>
		<TD><?php  putGS('Place'); ?></TD>
		<TD><INPUT TYPE="TEXT" NAME="sPlace" VALUE="<?php  pencHTML($sPlace); ?>" SIZE="16" MAXLENGTH="32"></TD>
		<TD>SUBMIT(<*Search*>, <*Search*>)</TD>
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article);?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section);?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($sLanguage); ?>">
	E_SEARCH_DIALOG
	</TD>
</TABLE>

<?php 
	$cIssue=trim($cIssue);
	$sDescription = trim($sDescription);
	$sPhotographer = trim($sPhotographer);
	$sPlace = trim($sPlace);
	
	if(($cIssue == 0) || ($cIssue == '') || !is_numeric($cIssue)) {
		// check if numeric !!!!
		query ("SELECT * FROM Images WHERE IdPublication=$Pub AND NrArticle != $Article AND Description LIKE '%$sDescription%' AND Photographer LIKE '%$sPhotographer%' AND Place LIKE '%$sPlace%'  ORDER BY IdPublication, NrIssue, NrSection, NrArticle, Number LIMIT $ImgOffs, ".($lpp+1), 'q_img');
	}
	else query ("SELECT * FROM Images WHERE IdPublication=$Pub AND NrIssue=$cIssue AND NrArticle != $Article AND Description LIKE '%$sDescription%' AND Photographer LIKE '%$sPhotographer%' AND Place LIKE '%$sPlace%'  ORDER BY IdPublication, NrIssue, NrSection, NrArticle, Number LIMIT $ImgOffs, ".($lpp+1), 'q_img');
	if ($NUM_ROWS) {
		$nr= $NUM_ROWS;
		$i=$lpp;
		$color= 0;
	?>dnl

B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Image number*>, <*1%*>)
		X_LIST_TH(<*Click to view image*>)
		X_LIST_TH(<*Photographer*>)
		X_LIST_TH(<*Place*>)
		X_LIST_TH(<*Date<BR><SMALL>(yyyy-mm-dd)</SMALL>*>)
		X_LIST_TH(<*Add to current article*>, <*1%*>)
	E_LIST_HEADER
<?php 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_img);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM(<*LEFT*>)
			<?php  pgetHVar($q_img,'IdPublication'); ?>_<?php  pgetHVar($q_img,'NrIssue'); ?>_<?php  pgetHVar($q_img,'NrSection'); ?>_<?php  pgetHVar($q_img,'NrArticle'); ?>_<?php  pgetHVar($q_img,'Number'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/articles/images/viewsel.php?Pub=<?php  pgetUVar($q_img,'IdPublication'); ?>&Issue=<?php  pgetUVar($q_img,'NrIssue'); ?>&Section=<?php  pgetUVar($q_img,'NrSection'); ?>&Article=<?php  pgetUVar($q_img,'NrArticle'); ?>&Image=<?php  pgetUVar($q_img,'Number'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>"><?php  pgetHVar($q_img,'Description'); ?></A>
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
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/articles/images/do_sel.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Image=<?php  pgetUVar($q_img,'Number'); ?>&Pub1=<?php  pgetUVar($q_img,'IdPublication'); ?>&Issue1=<?php  pgetUVar($q_img,'NrIssue'); ?>&Section1=<?php  pgetUVar($q_img,'NrSection'); ?>&Article1=<?php  pgetUVar($q_img,'NrArticle'); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>"><?php  putGS('Add now'); ?></A>
		E_LIST_ITEM
	E_LIST_TR
<?php  
    $i--;
    }
}
?>dnl
	B_LIST_FOOTER
		<?php 	print "(<*Publ. no.*>_<*Issue no.*>_<*Section no.*>_<*Article*>_<*Image number*>)<br>"; ?>
<?php  if ($ImgOffs <= 0) { ?>dnl
		X_PREV_I
<?php  } else { ?>dnl
		X_PREV_A(<*select.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>&sDescription=<?php  pencURL(encHTML($sDescription));?>&sPhotographer=<?php  pencURL(encHTML($sPhotographer));?>&sPlace=<?php  pencURL(encHTML($sPlace));?>&cIssue=<?php  p($cIssue); ?>&ImgOffs=<?php  p($ImgOffs - $lpp); ?>*>)
<?php  } ?>dnl
<?php  if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<?php  } else { ?>dnl
		X_NEXT_A(<*select.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>&sDescription=<?php  pencURL(encHTML($sDescription));?>&sPhotographer=<?php  pencURL(encHTML($sPhotographer));?>&sPlace=<?php  pencURL(encHTML($sPlace));?>&cIssue=<?php  p($cIssue); ?>&ImgOffs=<?php  p($ImgOffs + $lpp); ?>*>)
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

