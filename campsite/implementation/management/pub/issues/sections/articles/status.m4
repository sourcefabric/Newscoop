B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Change article status*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    SET_ACCESS(<*pa*>, <*Publish*>)
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Article');
    todefnum('Language');
    todefnum('sLanguage');
?>dnl
B_HEADER(<*Change article status*>)
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
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B>*>)
E_CURRENT

CHECK_XACCESS(<*ChangeArticle*>)

<?php 
    query ("SELECT ($xaccess != 0) or ((".getVar($q_art,'IdUser')." = ".getVar($Usr,'Id').") and ('".getVar($q_art,'Published')."' = 'N'))", 'q_xperm');
    fetchRowNum($q_xperm);
    $has_access = getNumVar($q_xperm,0) && ($pa || getVar($q_art,'Published') == "N");
    if ($has_access) { ?>dnl
<p>
B_MSGBOX(<*Change article status*>)
	<?php 
	    if (getVar($q_art,'Published') == "Y")
		$stat=getGS('Published');
	    elseif (getVar($q_art,'Published')== "S")
		$stat=getGS('Submitted');
	    else
		$stat=getGS('New');
	?>
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Change the status of article $1 ($2) from $3 to', '<B>'.getHVar($q_art,'Name'), getHVar($q_slang,'Name').'</B>', '<B>'.$stat.'</B>' ); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_status.php"><br>
		<?php  if (getVar($q_art,'Published') == "N" && $pa) {
			$check= 1;
		}
		else $check= 0; ?>
		<TABLE><?php  if (getVar($q_art,'Published') != "Y" && $pa) { ?><TR><TD ALIGN=LEFT><INPUT <?php  if ($check == 0) { ?>CHECKED <?php   $check= 1;  }else $check=0; ?> TYPE="RADIO" NAME='Status' value='Y'> <B><?php  putGS('Published'); ?></B></TD></TR> <?php  } ?>
		<?php  if (getVar($q_art,'Published') != "S") { ?><TR><TD ALIGN=LEFT><INPUT <?php  if ($check == 0) { ?>CHECKED<?php  $check= 1;  } ?> TYPE="RADIO" NAME='Status' value='S'> <B><?php  putGS('Submitted'); ?></B></TD></TR> <?php  } ?>
		<?php  if (getVar($q_art,'Published') != "N") { ?><TR><TD ALIGN=LEFT><INPUT <?php  if ($check == 0) { ?>CHECKED<?php  $check= 1;  } ?> TYPE="RADIO" NAME='Status' value='N'> <B><?php  putGS('New'); ?></B></TD></TR><?php  } ?></TABLE>

		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($sLanguage); ?>"><P>
		SUBMIT(<*Save*>, <*Save changes*>)
<?php  todef('Back'); ?>dnl
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<?php  pencHTML($Back); ?>">
<?php  if ($Back != "") { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*<?php  p($Back); ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/pub/issues/sections/articles/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&sLanguage=<?php  p($sLanguage); ?>*>)
<?php  } ?>dnl
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX



<P>
<?php  } else { ?>dnl
    X_XAD(<*You do not have the right to change this article status. Once submitted an article can only changed by authorized users.*>, <*pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
<?php  } ?>dnl

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

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such article.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

