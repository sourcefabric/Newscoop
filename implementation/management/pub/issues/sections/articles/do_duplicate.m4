B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Duplicating article*>)
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY
CHECK_ACCESS(<*AddArticle*>)
<?php 
	if ($access == 0) {
?>
		X_AD(<*You do not have the right to add articles.*>)
<?php 
	} else {
?>

<?php 
	todefnum('Language');
	$sLanguage = $Language;
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('Article');
	todefnum('dstPub');
	todefnum('dstIssue');
	todefnum('dstSection');
?>dnl
B_HEADER(<*Duplicating article*>)
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
    query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	    if ($NUM_ROWS) {
		query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		fetchRow($q_sect);
		fetchRow($q_iss);
		fetchRow($q_pub);
		fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Duplicating article*>)
	X_MSGBOX_TEXT(<*
<?php 
	$correct = true;
	if ($Language <= 0 || $Pub <= 0 || $Issue <= 0 || $Section <= 0 || $Article <= 0 || $dstPub <= 0
	    || $dstIssue <= 0 || $dstSection <= 0) {
		$correct = false;
		echo "<LI>"; putGS('Invalid parameters received'); echo "</LI>\n";
	}
	$created = false;
	if ($correct) {
		$new_id = duplicate_article($Article, $Language, $UserID, $dstPub, $dstIssue, $dstSection, $msg, $name, $new_name);
		if ($new_id > 0) {
			$created = true;
		} else {
			echo "<LI>"; putGS($msg); echo "</LI>\n";
		}
	}

    if ($correct) {
	if ($created) { ?>dnl
	<LI><?php  putGS('The article $1 has been duplicated as $2','<B>'.encHTML(decS($name)).'</B>', '<B>'.encHTML(decS($new_name)).'</B>'); ?></LI>
X_AUDIT(<*31*>, <*getGS('Article $1 added to $2. $3 from $4. $5 of $6',encHTML(decS($new_name)),getHVar($q_sect,'Number'),getHVar($q_sect,'Name'),getHVar($q_iss,'Number'),getHVar($q_iss,'Name'),getHVar($q_pub,'Name') )*>)
<?php  } else { ?>dnl
	<LI><?php  putGS('The article $1 could not be duplicated','<B>'.encHTML(decS($name)).'</B>'); ?></LI>
<?php  }
}
?>dnl
	*>)
<?php  if ($created) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Do you want to edit the article?'); ?></LI>*>)
<?php  } ?>dnl
	B_MSGBOX_BUTTONS
<?php 
    if ($created) { ?>dnl
	REDIRECT(<*Yes*>, <*Yes*>, <*X_ROOT/pub/issues/sections/articles/edit.php?Pub=<?php  p($dstPub); ?>&Issue=<?php  p($dstIssue); ?>&Section=<?php  p($dstSection); ?>&Article=<?php  p($new_id); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pencURL($sLanguage); ?>*>)
	REDIRECT(<*No*>, <*No*>, <*X_ROOT/pub/issues/sections/articles/?Pub=<?php  p($dstPub); ?>&Issue=<?php  p($dstIssue); ?>&Section=<?php  p($dstSection); ?>&Language=<?php  p($Language); ?>*>)
<?php  } else { ?>
	REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  pgetNumVar($lii,0); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  pencURL($sLanguage); ?>*>)
<?php 
}
?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

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

<?php  } // xaccess ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
