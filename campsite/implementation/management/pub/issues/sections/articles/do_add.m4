B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*AddArticle*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new article*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add articles.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY


<? 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Language');
?>dnl
B_HEADER(<*Adding new article*>)
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

<?
    todef('cName');
    todef('cFrontPage');
    todef('cSectionPage');
    todef('cType');
    todefnum('cLanguage');
    todef('cKeywords');

    if ($cFrontPage == "on")
	$cFrontPage= "Y";
    else
	$cFrontPage= "N";

    if ($cSectionPage == "on")
	$cSectionPage= "Y";
    else
	$cSectionPage= "N";

    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new article*>)
	X_MSGBOX_TEXT(<*
<?
    $cName=trim($cName);
    $cType=trim($cType);
    $cLanguage=trim($cLanguage);
    
    if ($cName == "" || $cName == " ") {
	$correct= 0; ?>dnl
	<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>' ); ?></LI>
    <? }
    
    if ($cType == "" || $cType == " ") {
	$correct= 0; ?>dnl
	<LI><? putGS('You must select an article type.'); ?></LI>
    <? }
    
    if ($cLanguage == "" || $cLanguage == "0") {
	$correct= 0; ?>dnl
	<LI><? putGS('You must select a language.'); ?></LI>
    <? }
    
    if ($correct) {
	query ("UPDATE AutoId SET ArticleId=LAST_INSERT_ID(ArticleId + 1)");
	query ("INSERT IGNORE INTO Articles SET IdPublication=$Pub, NrIssue=$Issue, NrSection = $Section, Number = LAST_INSERT_ID(), IdLanguage=$cLanguage, Type='$cType', Name='$cName', Keywords='$cKeywords', OnFrontPage='$cFrontPage', OnSection='$cSectionPage', UploadDate=NOW(), IdUser=".getVar($Usr,'Id').", Public='Y'");
	if ($AFFECTED_ROWS > 0) {
	    query ("INSERT IGNORE INTO X$cType SET NrArticle=LAST_INSERT_ID(), IdLanguage=$cLanguage");
	    if ($AFFECTED_ROWS > 0) {
		query ("SELECT LAST_INSERT_ID()", 'lii');
		fetchRowNum($lii);
		$created= 1;
	    }
	}
    }

    if ($correct) {
	if ($created) { ?>dnl
	<LI><? putGS('The article $1 has been created','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
X_AUDIT(<*31*>, <*getGS('Article $1 added to $2. $3 from $4. $5 of $6',$cName,getHVar($q_sect,'Number'),getHVar($q_sect,'Name'),getHVar($q_iss,'Number'),getHVar($q_iss,'Name'),getHVar($q_pub,'Name') )*>)
<? } else { ?>dnl
	<LI><? putGS('The article $1 could not be created','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
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
	<A HREF="X_ROOT/pub/issues/sections/articles/edit.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? pgetNumVar($lii,0); ?>&Language=<? p($Language); ?>&sLanguage=<? pencURL($cLanguage); ?>"><IMG SRC="X_ROOT/img/button/yes.gif" BORDER="0" ALT="Yes"></A>
	<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Language=<? p($Language); ?>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
<? } else { ?>
	<A HREF="X_ROOT/pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? pgetNumVar($lii,0); ?>&Language=<? p($Language); ?>&sLanguage=<? pencURL($cLanguage); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
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

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
