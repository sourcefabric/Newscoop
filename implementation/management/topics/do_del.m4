B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTopics*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting topic*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete topics.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting topic*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
	todefnum('IdCateg');
	todefnum('DelCateg');
	todefnum('del',1);
	query ("SELECT Name FROM Topics WHERE Id=$DelCateg AND LanguageId = 1", 'q_cat');
	if ($NUM_ROWS) {
	    fetchRow($q_cat);

	?>dnl
<P>
B_MSGBOX(<*Deleting topic*>)
	X_MSGBOX_TEXT(<*
<?
	query ("SELECT COUNT(*) FROM Topics WHERE ParentId=$DelCateg AND LanguageId = 1", 'q_sons');
	fetchRowNum($q_sons);
	if (getNumVar($q_sons,0) != 0) {
		$del= 0; ?>dnl
		<LI><? putGS('There are $1 subtopics left.',getNumVar($q_sons,0)); ?></LI>
    <? }
	query ("SELECT COUNT(*) FROM ArticleTopics WHERE TopicId=$DelCateg", 'q_tart');
	fetchRowNum($q_tart);
	if (getNumVar($q_tart,0) != 0) {
		$del= 0; ?>dnl
		<LI><? putGS('There are $1 articles using the topic.',getNumVar($q_tart,0)); ?></LI>
    <? }

    $AFFECTED_ROWS=0;

    if ($del)
	query ("DELETE FROM Topics WHERE Id=$DelCateg");

	if ($AFFECTED_ROWS > 0) { ?>dnl
		<LI><? putGS('The topic $1 has been deleted.','<B>'.getHVar($q_cat,'Name').'</B>'); ?></LI>
		X_AUDIT(<*142*>, <*getGS('Topic $1 deleted',getHVar($q_cat,'Name'))*>)
	<? } else { ?>dnl
		<LI><? putGS('The topic $1 could not be deleted.','<B>'.getHVar($q_cat,'Name').'</B>'); ?></LI>
	<? } ?>dnl
*>)

	B_MSGBOX_BUTTONS
<? if ($AFFECTED_ROWS > 0) { ?>dnl
		<A HREF="X_ROOT/topics/index.php?IdCateg=<?p($IdCateg);?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/topics/index.php?IdCateg=<?p($IdCateg);?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such topic.'); ?></LI>
</BLOCKQUOTE>
<? } ?>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
