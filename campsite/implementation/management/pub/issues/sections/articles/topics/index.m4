INCLUDE_PHP_LIB(<*../../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Article topics*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? }
    query ("SELECT * FROM Topics WHERE 1=0", 'q_topic');
?>dnl
E_HEAD

<? if ($access) {

SET_ACCESS(<*acc*>, <*ChangeArticle*>)
?>dnl
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
    todefnum('IdCateg');
    todefnum('CatOffs');
?>
B_HEADER(<*Article topics*>)
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
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><? pgetHVar($q_sect,'Number'); ?>. <? pgetHVar($q_sect,'Name'); ?></B>*>)
X_CURRENT(<*Article*>, <*<B><? pgetHVar($q_art,'Name'); ?></B>*>)
E_CURRENT

<p>X_NEW_BUTTON(<*Back to article details*>, <*../edit.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>*>)

<P><?
    todefnum('ArtTopicOffs');
    if ($ArtTopicOffs < 0) $ArtTopicOffs= 0;
    todefnum('lpp', 10);

    query ("SELECT * FROM ArticleTopics, Topics WHERE ArticleTopics.NrArticle = $Article and ArticleTopics.TopicId = Topics.Id ORDER BY Topics.Name LIMIT $ArtTopicOffs, ".($lpp+1), 'q_topic');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=$lpp;
	$color= 0;
	?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Topic name*>)
	<? if ($acc != 0) { ?>
		X_LIST_TH(<*Delete*>, <*1%*>)
	<? } ?>
	E_LIST_HEADER
<?
    for($loop=0; $loop<$nr; $loop++) {
	fetchRow($q_topic);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<? pgetHVar($q_topic,'Name'); ?>&nbsp;
		E_LIST_ITEM
	<? if ($acc != 0) { ?>
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete topic $1 from article',getHVar($q_topic,'Name')); ?>*>, <*icon/x.gif*>, <*pub/issues/sections/articles/topics/del.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&DelTopic=<? pgetHVar($q_topic,'TopicId'); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&IdCateg=<? p($IdCateg); ?>*>)
		E_LIST_ITEM
	<? } ?>
	E_LIST_TR
<?
    $i--;
    }
}
?>dnl
	B_LIST_FOOTER
<? if ($ArtTopicOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&ArtTopicOffs=<? p($ArtTopicOffs - $lpp); ?>&IdCateg=<? p($IdCateg); ?>*>)
<? } ?>dnl
<? if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&ArtTopicOffs=<? p($ArtTopicOffs + $lpp); ?>&IdCateg=<? p($IdCateg); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No article topics.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl


<?
	todef('Path');
	todef('Top');
	if($IdCateg != 0) $Top="<A HREF=index.php?Pub=$Pub&Issue=$Issue&Section=$Section&Article=$Article&Language=$Language&sLanguage=$sLanguage&ArtTopicOffs=$ArtTopicOffs> Top </A>";
	todef('cCateg');
?>dnl

<?
	if($cCateg != ""){
		query ("SELECT * FROM Topics WHERE Name = '$cCateg'", 'q_cat');
		if($NUM_ROWS) {
			fetchRow($q_cat);
			$IdCateg = getVar($q_cat, 'Id');
		}
	}
?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<TD ALIGN="LEFT"><b><? putGS("Available topics"); ?></b></TD>
	<TD ALIGN="RIGHT">
	B_SEARCH_DIALOG(<*GET*>, <*index.php*>)
		<TD><? putGS('Topic'); ?>:</TD>
		<TD><INPUT TYPE="TEXT" NAME="cCateg" SIZE="8" MAXLENGTH="20"></TD>
		<TD>SUBMIT(<*Search*>, <*Search*>)</TD>
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<? p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<? p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<? p($Article); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? p($Language); ?>">
		<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<? p($sLanguage); ?>">
		<INPUT TYPE="HIDDEN" NAME="ArtTopicOffs" VALUE="<? p($ArtTopicOffs); ?>">
		<INPUT TYPE="HIDDEN" NAME="IdCateg" VALUE="<? p($IdCateg); ?>">
	E_SEARCH_DIALOG
	</TD>
</TABLE>
B_CURRENT
	<?
		$crtCat = $IdCateg;
		while($crtCat != 0){
			query ("SELECT * FROM Topics WHERE Id = $crtCat", 'q_cat');
			fetchRow($q_cat);									//should I release the resource ?
			$Path= "<A HREF=index.php?Pub=$Pub&Issue=$Issue&Section=$Section&Article=$Article&Language=$Language&sLanguage=$sLanguage&ArtTopicOffs=$ArtTopicOffs&IdCateg=".getVar($q_cat, 'Id')."> ".getVar($q_cat,'Name')."</A>/".$Path;
			$crtCat =getVar($q_cat, 'ParentId');
		}
		$Path=$Top."/".$Path;
		if($Path == '') $Path="/";
	?>
	X_CURRENT(<*Topic*>, <*<B><?p($Path);?></B>*>)
E_CURRENT
<p>
<?
	todefnum('CatOffs');
	if ($CatOffs < 0) $CatOffs= 0;
	$lpp=10;

	query ("SELECT * FROM Topics WHERE ParentId = $IdCateg ORDER BY Name LIMIT $CatOffs, ".($lpp+1), 'categ');
	if ($NUM_ROWS) {
		$nr= $NUM_ROWS;
		$i= $lpp;
		$color= 0;
	?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Name*>)
		X_LIST_TH(<*Add*>, <*1%*>)
	E_LIST_HEADER
<?
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($categ);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="index.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&ArtTopicOffs=<? p($ArtTopicOffs); ?>&IdCateg=<?pgetVar($categ,'Id');?>"><? pgetHVar($categ,'Name'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Add topic $1 to article',getHVar($categ,'Name')); ?>*>, <*icon/image.gif*>, <*pub/issues/sections/articles/topics/do_add.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&ArtTopicOffs=<? p($ArtTopicOffs); ?>&IdCateg=<?p($IdCateg);?>&AddTopic=<? pgetVar($categ,'Id'); ?>*>)
		E_LIST_ITEM
    E_LIST_TR
<?
    $i--;
    }
} ?>dnl
	B_LIST_FOOTER
<? if ($CatOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&ArtTopicOffs=<? p($ArtTopicOffs); ?>&IdCateg=<?p($IdCateg);?>&CatOffs=<? print ($CatOffs - $lpp); ?>*>)
<? } ?>dnl
<? if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage); ?>&ArtTopicOffs=<? p($ArtTopicOffs); ?>&IdCateg=<?p($IdCateg);?>&CatOffs=<? print ($CatOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No topics.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl


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

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

