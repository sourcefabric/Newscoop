B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Duplicating article*>)
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY
CHECK_ACCESS(<*AddArticle*>)
<?
	if ($access == 0) {
?>
		X_AD(<*You do not have the right to add articles.*>)
<?
	} else {
?>

<?
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

X_HBUTTON(<*Articles*>, <*pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>*>)
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
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
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><? pgetHVar($q_sect,'Number'); ?>. <? pgetHVar($q_sect,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Duplicating article*>)
	X_MSGBOX_TEXT(<*
<?
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
	<LI><? putGS('The article $1 has been duplicated as $2','<B>'.encHTML(decS($name)).'</B>', '<B>'.encHTML(decS($new_name)).'</B>'); ?></LI>
X_AUDIT(<*31*>, <*getGS('Article $1 added to $2. $3 from $4. $5 of $6',encHTML(decS($new_name)),getHVar($q_sect,'Number'),getHVar($q_sect,'Name'),getHVar($q_iss,'Number'),getHVar($q_iss,'Name'),getHVar($q_pub,'Name') )*>)
<? } else { ?>dnl
	<LI><? putGS('The article $1 could not be duplicated','<B>'.encHTML(decS($name)).'</B>'); ?></LI>
<? }
}
?>dnl
	*>)
<? if ($created) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('Do you want to edit the article?'); ?></LI>*>)
<? } ?>dnl
	B_MSGBOX_BUTTONS
<?
    if ($created) { ?>dnl
	REDIRECT(<*Yes*>, <*Yes*>, <*X_ROOT/pub/issues/sections/articles/edit.php?Pub=<? p($dstPub); ?>&Issue=<? p($dstIssue); ?>&Section=<? p($dstSection); ?>&Article=<? p($new_id); ?>&Language=<? p($Language); ?>&sLanguage=<? pencURL($sLanguage); ?>*>)
	REDIRECT(<*No*>, <*No*>, <*X_ROOT/pub/issues/sections/articles/?Pub=<? p($dstPub); ?>&Issue=<? p($dstIssue); ?>&Section=<? p($dstSection); ?>&Language=<? p($Language); ?>*>)
<? } else { ?>
	REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? pgetNumVar($lii,0); ?>&Language=<? p($Language); ?>&sLanguage=<? pencURL($sLanguage); ?>*>)
<?
}
?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such section.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } // xaccess ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
