B_HTML
INCLUDE_PHP_LIB(<*.*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Home*>)
<? if ($access==0) { ?>dnl
		X_LOGOUT
<? } ?>
E_HEAD
<?
    query ("SELECT * FROM Articles WHERE 1=0", 'q_art');
    if ($access) {
SET_ACCESS(<*aaa*>, <*AddArticle*>)
SET_ACCESS(<*mpa*>, <*ManagePub*>)
SET_ACCESS(<*muta*>, <*ManageUserTypes*>)
SET_ACCESS(<*mda*>, <*ManageDictionary*>)
SET_ACCESS(<*mca*>, <*ManageClasses*>)
SET_ACCESS(<*mcoa*>, <*ManageCountries*>)
SET_ACCESS(<*mata*>, <*ManageArticleTypes*>)
SET_ACCESS(<*mua*>, <*ManageUsers*>)
SET_ACCESS(<*mla*>, <*ManageLanguages*>)
SET_ACCESS(<*mta*>, <*ManageTempl*>)
SET_ACCESS(<*vla*>, <*ViewLogs*>)
SET_ACCESS(<*caa*>, <*ChangeArticle*>)
?>
B_STYLE
E_STYLE

B_BODY

<?
    if ($caa)
	todefnum('What',0);
    else
	todefnum('What',1);

?>dnl

B_HEADER(<*Home*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<TABLE BORDER="0" CELLSPACING="4" CELLPADDING="2" WIDTH="100%">
<TR><TD COLSPAN="2" BGCOLOR=#D0D0B0><? putGS('Welcome $1!','<B>'.getHVar($Usr,'Name').'</B>'); ?></TD></TR>
<TR>
    <TD VALIGN="TOP">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
<? if ($aaa != 0) { ?>dnl
	X_HITEM(<*pub/add_article.php*>, <*Add new article*>)
<? } ?>dnl
<? if ($mpa != 0) { ?>dnl
	X_HITEM(<*pub/add.php?Back=<? print encURL ($REQUEST_URI); ?>*>, <*Add new publication*>)
<? } ?>dnl
<? if ($mta != 0) { ?>dnl
	X_HITEM(<*templates/upload_templ.php?Path=LOOK_PATH/&Back=<? print encURL ($REQUEST_URI); ?>*>, <*Upload new template*>)
<? } ?>dnl
<? if ($mua != 0) { ?>dnl
	X_HITEM(<*users/add.php?Back=<? print encURL ($REQUEST_URI); ?>*>, <*Add new user account*>)
<? } ?>dnl
<? if ($muta != 0) { ?>dnl
	X_HITEM(<*u_types/add.php?Back=<? print encURL ($REQUEST_URI); ?>*>, <*Add new user type*>)
<? } ?>dnl
<? if ($mata != 0) { ?>dnl
	X_HITEM(<*a_types/add.php?Back=<? print encURL ($REQUEST_URI); ?>*>, <*Add new article type*>)
<? } ?>dnl
<? if ($mcoa != 0) { ?>dnl
	X_HITEM(<*country/add.php?Back=<? print encURL ($REQUEST_URI); ?>*>, <*Add new country*>)
<? } ?>dnl
<? if ($mla != 0) { ?>dnl
	X_HITEM(<*languages/add.php?Back=<? print encURL ($REQUEST_URI); ?>*>, <*Add new language*>)
<? } ?>dnl
<? if ($vla != 0) { ?>dnl
	X_HITEM(<*logs/*>, <*View logs*>)
<? } ?>dnl
	X_HITEM(<*users/chpwd.php*>, <*Change your password*>)
</TABLE>
	</TD>
	<TD VALIGN="TOP">

<? if ($What) { ?>dnl

X_BULLET(<*<? putGS('Your articles'); ?>:*>)

<?
    todefnum('ArtOffs');
    if ($ArtOffs < 0) $ArtOffs=0;
    $lpp=20;
    query ("SELECT * FROM Articles WHERE Iduser=".getVar($Usr,'Id')." ORDER BY Number DESC, IdLanguage LIMIT $ArtOffs, ".($lpp+1), 'q_art');
    $nr=$NUM_ROWS;
    $i=$lpp;
    if ($nr < $lpp) $i = $nr;
    $color=0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Name<BR><SMALL>(click to edit article)</SMALL>*>)
		X_LIST_TH(<*Language*>, <*10%*>)
		X_LIST_TH(<*Status*>, <*10%*>)
	E_LIST_HEADER

<?
    for($loop=0;$loop<$i;$loop++) {

	    fetchRow($q_art);
	    query ("SELECT IdLanguage FROM Sections WHERE IdPublication=".getVar($q_art,'IdPublication')." AND NrIssue=".getVar($q_art,'NrIssue')." AND IdLanguage=".getVar($q_art,'IdLanguage'), 'q_sect');
	    if ($NUM_ROWS == 0)
		query ("SELECT IdLanguage FROM Sections WHERE IdPublication=".getVar($q_art,'IdPublication')." AND NrIssue=".getVar($q_art,'NrIssue')." LIMIT 1", 'q_sect');
	    fetchRow($q_sect);
 ?>
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/articles/edit.php?Pub=<? pgetUVar($q_art,'IdPublication');?>&Issue=<? pgetUVar($q_art,'NrIssue');?>&Section=<? pgetUVar($q_art,'NrSection');?>&Article=<? pgetUVar($q_art,'Number');?>&sLanguage=<? pgetUVar($q_art,'IdLanguage');?>&Language=<? pgetUVar($q_sect,'IdLanguage');?>"><? print pgetHVar($q_art,'Name');?></A>
		E_LIST_ITEM
<? query ("SELECT Name FROM Languages WHERE Id=".getVar($q_art,'IdLanguage'), 'q_lang'); ?>dnl
		B_LIST_ITEM

			<? fetchRow ($q_lang); pgetHVar($q_lang,'Name'); ?>
		E_LIST_ITEM
		B_LIST_ITEM
<? if (getVar($q_art,'Published') == "Y") { ?>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/status.php?Pub=<? pgetUVar($q_art,'IdPublication'); ?>&Issue=<? pgetUVar($q_art,'NrIssue'); ?>&Section=<? pgetUVar($q_art,'NrSection'); ?>&Article=<? pgetUVar($q_art,'Number'); ?>&Language=<? pgetUVar($q_sect,'IdLanguage'); ?>&sLanguage=<? pgetUVar($q_art,'IdLanguage'); ?>&Back=<? print encURL($REQUEST_URI); ?>"><? putGS('Published'); ?></A>
<? } elseif (getVar($q_art,'Published') == "N") { ?>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/status.php?Pub=<? pgetUVar($q_art,'IdPublication'); ?>&Issue=<? pgetUVar($q_art,'NrIssue'); ?>&Section=<? pgetUVar($q_art,'NrSection'); ?>&Article=<? pgetUVar($q_art,'Number'); ?>&Language=<? pgetUVar($q_sect,'IdLanguage'); ?>&sLanguage=<? pgetUVar($q_art,'IdLanguage'); ?>&Back=<? print encURL($REQUEST_URI); ?>"><? putGS('New'); ?></A>
<? } else { ?>dnl
			<A HREF="X_ROOT/pub/issues/sections/articles/status.php?Pub=<? pgetUVar($q_art,'IdPublication'); ?>&Issue=<? pgetUVar($q_art,'NrIssue'); ?>&Section=<? pgetUVar($q_art,'NrSection'); ?>&Article=<? pgetUVar($q_art,'Number'); ?>&Language=<? pgetUVar($q_sect,'IdLanguage'); ?>&sLanguage=<? pgetUVar($q_art,'IdLanguage'); ?>&Back=<? print encURL($REQUEST_URI); ?>"><? putGS('Submitted'); ?></A>
<? } ?>dnl
		E_LIST_ITEM
	E_LIST_TR
<?

}
    ?>dnl

	B_LIST_FOOTER
<? if ($ArtOffs<=0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*home.php?ArtOffs=<?print ($ArtOffs - $lpp); ?>&What=1*>)
<? } ?>dnl

<? if ($nr<$lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*home.php?ArtOffs=<? print ($ArtOffs + $lpp); ?>&What=1*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST

<? } else { ?>dnl

X_BULLET(<*<? putGS('Submitted articles'); ?>:*>)
<?
    todefnum('NArtOffs');
    if ($NArtOffs<0) $NArtOffs=0;
    $lpp=20;
    query ("SELECT * FROM Articles WHERE Published = 'S' ORDER BY Number DESC, IdLanguage LIMIT $NArtOffs, ".($lpp+1), 'q_art');
    $nr=$NUM_ROWS;
    $i=$lpp;
    if ($nr < $lpp) $i = $nr;
    $color=0;
?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Name<BR><SMALL>(click to edit article)</SMALL>*>)
		X_LIST_TH(<*Language*>, <*10%*>)
	E_LIST_HEADER
<?
    for($loop=0;$loop<$i; $loop++) {
	fetchRow($q_art);

	    query ("SELECT IdLanguage FROM Sections WHERE IdPublication=".getVar($q_art,'IdPublication')." AND NrIssue=".getVar($q_art,'NrIssue')." AND IdLanguage=".getVar($q_art,'IdLanguage'), 'q_sect');
	if ($NUM_ROWS == 0) {
		query ("SELECT IdLanguage FROM Sections WHERE IdPublication=".getVar($q_art,'IdPublication')." AND NrIssue=".getVar($q_art,'NrIssue')." LIMIT 1", 'q_sect');
	}
	fetchRow($q_sect);
?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/articles/edit.php?Pub=<? pgetUVar($q_art,'IdPublication'); ?>&Issue=<? pgetUVar($q_art,'NrIssue'); ?>&Section=<? pgetUVar($q_art,'NrSection'); ?>&Article=<? pgetUVar($q_art,'Number'); ?>&sLanguage=<? pgetUVar($q_art,'IdLanguage'); ?>&Language=<? pgetUVar($q_sect,'IdLanguage'); ?>"><? pgetHVar($q_art,'Name'); ?></A>
		E_LIST_ITEM
<? query ("SELECT Name FROM Languages WHERE Id=".getVar($q_art,'IdLanguage'), 'q_lang');?>dnl
		B_LIST_ITEM
			<? fetchRow($q_lang); pgetHVar($q_lang,'Name'); ?>
		E_LIST_ITEM
	E_LIST_TR
<?

} ?>dnl
	B_LIST_FOOTER
<? if ($NArtOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*home.php?NArtOffs=<? print ($NArtOffs - $lpp); ?>&What=0*>)
<? 
    }
    if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*home.php?NArtOffs=<? print ($NArtOffs + $lpp); ?>&What=0*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST

<? } ?>dnl

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
<TR>
<?
    if ($What) {
	if ($caa) { ?>dnl
	<TD>
		X_HITEM(<*home.php?What=0*>, <*Submitted articles*>)
	</TD>
<? } 
    }    
 else { ?>dnl
	<TD>
		X_HITEM(<*home.php?What=1*>, <*Your articles*>)
	</TD>
<? } ?>dnl
</TR>
</TABLE>

    </TD>
</TR>
</TABLE>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

