B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Change article status*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
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
?>dnl
B_HEADER(<*Change article status*>)
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
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><? pgetHVar($q_iss,'Number'); ?>. <? pgetHVar($q_iss,'Name'); ?> (<? pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><? pgetHVar($q_sect,'Number'); ?>. <? pgetHVar($q_sect,'Name'); ?></B>*>)
E_CURRENT

CHECK_XACCESS(<*ChangeArticle*>)

<?
    query ("SELECT ($xaccess != 0) or ((".getVar($q_art,'IdUser')." = ".getVar($Usr,'Id').") and ('".getVar($q_art,'Published')."' = 'N'))", 'q_xperm');
    fetchRowNum($q_xperm);
    if (getNumVar($q_xperm,0)) { ?>dnl
<p>
B_MSGBOX(<*Change article status*>)
	<?
	    if (getVar($q_art,'Published') == "Y")
		$stat=getGS('Published');
	    elseif (getVar($q_art,'Published')== "S")
		$stat=getGS('Submitted');
	    else
		$stat=getGS('New');
	?>
	X_MSGBOX_TEXT(<*<LI><? putGS('Change the status of article $1 ($2) from $3 to', '<B>'.getHVar($q_art,'Name'), getHVar($q_slang,'Name').'</B>', '<B>'.$stat.'</B>' ); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_status.php"><br>
		<? if (getVar($q_art,'Published') == "N") {
			$check= 1;
		}
		else $check= 0; ?>
		<TABLE><? if (getVar($q_art,'Published') != "Y") { ?><TR><TD ALIGN=LEFT><INPUT <? if ($check == 0) { ?>CHECKED <?  $check= 1;  }else $check=0; ?> TYPE="RADIO" NAME='Status' value='Y'> <B><? putGS('Published'); ?></B></TD></TR> <? } ?>
		<? if (getVar($q_art,'Published') != "S") { ?><TR><TD ALIGN=LEFT><INPUT <? if ($check == 0) { ?>CHECKED<? $check= 1;  } ?> TYPE="RADIO" NAME='Status' value='S'> <B><? putGS('Submitted'); ?></B></TD></TR> <? } ?>
		<? if (getVar($q_art,'Published') != "N") { ?><TR><TD ALIGN=LEFT><INPUT <? if ($check == 0) { ?>CHECKED<? $check= 1;  } ?> TYPE="RADIO" NAME='Status' value='N'> <B><? putGS('New'); ?></B></TD></TR><? } ?></TABLE>

		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<? p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<? p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<? p($Article); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<? p($Language); ?>">
		<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<? p($sLanguage); ?>"><P>
		SUBMIT(<*Save*>, <*Save changes*>)
<? todef('Back'); ?>dnl
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<? pencHTML($Back); ?>">
<? if ($Back != "") { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*<? p($Back); ?>*>)
<? } else { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/sections/articles/edit.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>&Article=<? p($Article); ?>&sLanguage=<? p($sLanguage); ?>*>)
<? } ?>dnl
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX



<P>
<? } else { ?>dnl
    X_XAD(<*You do not have the right to change this article status. Once submitted an article can only changed by authorized users.*>, <*pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>*>)
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

