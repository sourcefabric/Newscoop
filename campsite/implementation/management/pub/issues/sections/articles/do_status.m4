B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
<?php
SET_ACCESS(<*pa*>, <*Publish*>)
SET_ACCESS(<*ch_art*>, <*ChangeArticle*>)
?>

B_HEAD
	X_EXPIRES
	X_TITLE(<*Changing article status*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
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
    todef('Status');
?>dnl
B_HEADER(<*Changing article status*>)
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
    query ("SELECT * FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article AND IdLanguage=$sLanguage", 'q_art');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	    if ($NUM_ROWS) {
		query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
		if ($NUM_ROWS) {
		    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		    query ("SELECT Name FROM Languages WHERE Id=$sLanguage", 'q_slang');

		    fetchRow($q_art);
		    fetchRow($q_sect);
		    fetchRow($q_iss);
		    fetchRow($q_pub);
		    fetchRow($q_lang);
		    fetchRow($q_slang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B>*>)
E_CURRENT

CHECK_XACCESS(<*ChangeArticle*>)

<?php 
    query ("SELECT ($xaccess != 0) or ((".getVar($q_art,'IdUser')." = ".getVar($Usr,'Id').") and ('".getVar($q_art,'Published')."' = 'N'))", 'q_xperm');
    fetchRowNum($q_xperm);
    if (getNumVar($q_xperm,0)) { ?>dnl
<P>

B_MSGBOX(<*Changing article status*>)
<?php 
 	if ((getVar($q_art,'Published') == "Y") && $pa){
		query ("DELETE FROM ArticleIndex WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND NrArticle=$Article AND IdLanguage=$sLanguage");
		//check the deletion
 	}

	$was_published = getVar($q_art,'Published');
	if ((($was_published == 'Y' || $Status == 'Y') && $pa)
		|| ($was_published != 'Y' && $Status != 'Y' && ($ch_art || $pa))) {
		query ("UPDATE Articles SET LockUser=0, Published='$Status', IsIndexed='N' WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article AND IdLanguage=$sLanguage");
		if ($AFFECTED_ROWS > 0) { ?>dnl
			<?php  if ($was_published == "Y")	$stat=getGS('Published');
			else if ($was_published == "S") $stat=getGS('Submitted');
			else $stat=getGS('New');

			if($Status == "Y") $newstat=getGS('Published');
			else if ($Status== "S") $newstat=getGS('Submitted');
			else $newstat=getGS('New');
			?>

			X_MSGBOX_TEXT(<*<LI><?php  putGS('Status of the article $1 ($2) has been changed from $3 to $4.','<B>'.getHVar($q_art,'Name'),getHVar($q_slang,'Name').'</B>',"<B>$stat</B>","<B>$newstat</B>"); ?></LI>*>)
			X_AUDIT(<*35*>, <*getGS('Article $1 status from $2. $3 from $4. $5 ($6) of $7 changed',getSVar($q_art,'Name'),getSVar($q_sect,'Number'),getSVar($q_sect,'Name'),getSVar($q_iss,'Number'),getSVar($q_iss,'Name'),getSVar($q_lang,'Name'),getSVar($q_pub,'Name') )*>)
		<?php  } else { ?>dnl
			X_MSGBOX_TEXT(<*<LI><?php  putGS('Status of the article $1 ($2) could not be changed.','<B>'.getHVar($q_art,'Name'),getHVar($q_slang,'Name').'</B>'); ?></LI>*>)
		<?php }
	} else {?>dnl
		X_MSGBOX_TEXT(<*<?php  putGS('You do not have the right to change this article status. Once submitted an article can only changed by authorized users.'); ?>*>)
	<?php  } ?>
	B_MSGBOX_BUTTONS
<?php 
    todef('Back');
if ($AFFECTED_ROWS > 0) { 
    if ($Back != "") { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*<?php  p($Back); ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
<?php  } ?>dnl
<?php  } else { ?>dnl
<?php  if ($Back != "") { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*<?php  p($Back); ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
<?php  } ?>dnl
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX

<P>
<?php  } else { ?>dnl
    X_XAD(<*You do not have the right to change this article status. Once submitted an article can only changed by authorized users.*>, <*pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
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
