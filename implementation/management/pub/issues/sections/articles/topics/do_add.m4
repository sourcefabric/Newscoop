B_HTML
INCLUDE_PHP_LIB(<*../../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ChangeArticle*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new topic to article*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add topics to article.*>)
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
    todefnum('Article');
    todefnum('Language');
    todefnum('sLanguage');
    todefnum('AddTopic');
    todefnum('IdCateg');
?>
B_HEADER(<*Adding new topic to article*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Topics*>, <*pub/issues/sections/articles/topics/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&Section=<? p($Section); ?>&IdCateg=<? p($IdCateg); ?>*>)
X_HBUTTON(<*Articles*>, <*pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>*>)
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
query ("SELECT Name FROM Topics WHERE Id=$AddTopic", 'q_topic');
if ($NUM_ROWS) {
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
		    fetchRow($q_topic);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><? pgetHVar($q_sect,'Number'); ?>. <? pgetHVar($q_sect,'Name'); ?></B>*>)
X_CURRENT(<*Article*>, <*<B><? pgetHVar($q_art,'Name'); ?></B>*>)
X_CURRENT(<*Topic*>, <*<B><? pgetHVar($q_topic,'Name'); ?></B>*>)
E_CURRENT

<BLOCKQUOTE>
<P>
B_MSGBOX(<*Adding new topic to article*>)
<?  query ("SELECT Name FROM Topics WHERE Id = $AddTopic", 'q_topic');
    fetchRow($q_topic);
    query ("INSERT INTO ArticleTopics VALUES($Article, $AddTopic)");
    if ($AFFECTED_ROWS > 0) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('The topic $1 has been successfully added.','<B>'.getHVar($q_topic,'Name').'</B>'); ?></LI>*>)
	X_AUDIT(<*144*>, <*getGS('Topic $1 added to article', getHVar($q_topic, 'Name'));*>)
<? } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('The topic $1 could not be added.','<B>'.getHVar($q_topic,'Name').'</B>'); ?></LI>*>)
<? } ?>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/pub/issues/sections/articles/topics/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&Section=<? p($Section); ?>&IdCateg=<? p($IdCateg) ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

</BLOCKQUOTE>

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

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such article.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such topic.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
