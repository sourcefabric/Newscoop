B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Changing issue template*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change issue templates.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY
<?
    todefnum('What');
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
    
    
    query ("SELECT Name FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
	query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) { 
?>dnl
B_HEADER(<*Changing issue template*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<? pencURL($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_languages');
    $nr=$NUM_ROWS;
    fetchRow($q_pub);
    fetchRow($q_iss);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Name'); ?> (<? 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_languages);
	pgetHVar($q_languages,'Name');
    }
	?>)</B>*>)
E_CURRENT

<P>
<?
    $AFFECTED_ROWS= 0;
    if ($What == 1) { ?>dnl
B_MSGBOX(<*Changing issue template for front page*>)
<?
    query ("UPDATE Issues SET FrontPage='$Path' WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language");
} else { ?>dnl
B_MSGBOX(<*Changing issue template for single article*>)
<?
    query ("UPDATE Issues SET SingleArticle='$Path' WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language");
    }
    ?>dnl
	X_MSGBOX_TEXT(<*
<? if ($AFFECTED_ROWS > 0) { ?>dnl
	<LI><? putGS('The template has been successfully changed.'); ?></LI>
<? if ($What == 1) { ?>dnl
X_AUDIT(<*13*>, <*getGS('Issue template for publication $1 changed to $2',getVar($q_pub,'Name'),$Path)*>)
<? } else { ?>dnl
X_AUDIT(<*36*>, <*getGS('Issue template for single articles from $1 changed to $2',getVar($q_pub,'Name'),$Path)*>)
<? }
} else { ?>dnl
	<LI><? putGS('The template could not be changed.'); ?></LI>
<? } ?>dnl
	*>)
	B_DIALOG_BUTTONS
	<A HREF="X_ROOT/pub/issues/?Pub=<? pencURL($Pub); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_DIALOG_BUTTONS
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

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
