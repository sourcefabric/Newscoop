B_HTML
INCLUDE_PHP_LIB(<*..*>)dnl
B_DATABASE

<?
    todefnum('Issue');
    todefnum('Pub');
    todefnum('What');
?>dnl
CHECK_BASIC_ACCESS
<? if ($What != 0) { ?>dnl
CHECK_ACCESS(<*ManageTempl*>)dnl
<? } ?>dnl

B_HEAD
	X_EXPIRES
	<? 
	    if ($What) { ?>
		X_TITLE(<*Select template*>)
	    <? } else { ?>
		X_TITLE(<*Templates management*>)
	<? } ?>
<?
    if ($access == 0) {
	if ($What) { ?>dnl
	X_AD(<*You do not have the right to change default templates.*>)
<? } else { ?>dnl
	X_LOGOUT
<? }
    }
?>dnl
E_HEAD

<? if ($access) { 

SET_ACCESS(<*mta*>, <*ManageTempl*>)
SET_ACCESS(<*dta*>, <*DeleteTempl*>)
?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    if ($What) { ?>
	B_HEADER(<*Select template*>)
    <? } else { ?>
	B_HEADER(<*Templates*>)
    <? } ?>
B_HEADER_BUTTONS
<? if ($What) { ?>dnl
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? pencURL($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
<? } ?>dnl
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    $NUM_ROWS=0;
    if ($What)
	query ("SELECT Name, FrontPage, SingleArticle FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS != 0 || $What == 0) {
	if ($What) 
	    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	if (($NUM_ROWS != 0) || ($What == 0)) {
//				query ("SELECT SUBSTRING_INDEX('$REQUEST_URI', '?', 1), SUBSTRING_INDEX('$REQUEST_URI', '?', -1)", 'q_url');
//				fetchRowNum($q_url);
//				$myurl=getNumVar($q_url,0);
//				$myurl1=getNumVar($q_url,1);
		$dotpos=strrpos($REQUEST_URI,"?");
		$dotpos = $dotpos ? $dotpos: strlen($REQUEST_URI);
		$myurl=substr ($REQUEST_URI,0,$dotpos);
		$myurl1=substr ($REQUEST_URI,$dotpos+1);
   ?>dnl
B_CURRENT
<? if ($What) { ?>dnl
X_CURRENT(<*Publication*>, <*<B><? fetchRow($q_pub); pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pencURL($Issue); ?>. <? fetchRow($q_iss); pgetHVar($q_iss,'Name'); ?> (<? 
    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_language');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRowNum($q_language);
	pencHTML( getNumVar($q_language,0));
    }
?>)</B>*>)
<? } ?>dnl


X_CURRENT(<*Path*>, <*<B><? pencHTML(decURL($myurl)); ?></B>*>)
E_CURRENT
<P>
<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="0">
<TR>


<?
    if ($myurl != "LOOK_PATH/") {
	if ($What) { ?>dnl
<TD>X_NEW_BUTTON(<*Go up*>, <*../?What=<? pencURL($What); ?>&Pub=<? pencURL($Pub); ?>&Issue=<? pencURL($Issue); ?>&Language=<? pencURL($Language); ?>*>)</TD>
<? } else { ?>dnl
<TD>X_NEW_BUTTON(<*Go up*>, <*..*>)</TD>
<? } 
}

	if ($What == 0) {
		if ($mta != 0) { ?>
			<TD>X_NEW_BUTTON(<*Create new folder*>, <*X_ROOT/templates/new_dir.php?Path=<? pencURL($myurl); ?>*>)</TD>
			<TD>X_NEW_BUTTON(<*Upload template*>, <*X_ROOT/templates/upload_templ.php?Path=<? pencURL($myurl); ?>*>)</TD>
			<TD>X_NEW_BUTTON(<*Create new template*>, <*X_ROOT/templates/new_template.php?Path=<? pencURL($myurl); ?>*>)</TD>
		<? }
	} else {?>dnl
<TD>
<? if ($What == 1) { ?>dnl
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
	<TR>
		<TD><IMG SRC="X_ROOT/img/tol.gif" BORDER="0"></TD>
		<TD><? putGS('Select the template for displaying the front page.'); ?></TD>
	</TR>
	</TABLE>
<? } else { ?>dnl
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
	<TR>
		<TD><IMG SRC="X_ROOT/img/tol.gif" BORDER="0"></TD>
		<TD><? putGS('Select the template for displaying a single article.'); ?></TD>
	</TR>
	</TABLE>
<? } ?>dnl
	</TD>
<? } ?>dnl
</TABLE>
<P>
<? 
    // 'What' at this level selects the usage of templates:
    // 0 - you are in the templates management module (create, delete, edit, upload, duplicate etc)
    // 1, 2 - select a template for viewing with it the font page (1) and an independent article (2)

    if ($What) {
	
	$listbasedir=$myurl;
	$params=$myurl1;
	include ('./stempl_dir.php');
    }
    else {
					//dSystem( "$scriptBase/list '$myurl' $mta $dta $DOCUMENT_ROOT");
	$listbasedir=$myurl;
	include ('./list_dir.php');
    }

} else {
?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? }
} else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML


