B_HTML
INCLUDE_PHP_LIB(<*../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Changing article details*>)
<?php  if ($access == 0) { ?>dnl
	X_LOGOUT
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY
<?php 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Section');
    todefnum('Language');
    todefnum('sLanguage');
    todefnum('Article');
    todef('query');
    todef('cName');
    query ("SHOW COLUMNS FROM Articles LIKE 'XXYYZZ'", 'q_fld');
?>dnl
define(<*x_init*>, <*<?php 
    todef('$1');
    if ($$1 == "on")
	$$1= "Y";
    else
	$$1= "N";
?>*>)dnl
x_init(<*cOnFrontPage*>)
x_init(<*cOnSection*>)
x_init(<*cPublic*>)
<?php  todef('cKeywords'); ?>dnl
B_HEADER(<*Changing article details*>)
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
X_CURRENT(<*Article*>, <*<B><?php  pgetHVar($q_art,'Name'); ?> (<?php  pgetHVar($q_slang,'Name'); ?>)</B>*>)
E_CURRENT

CHECK_XACCESS(<*ChangeArticle*>)

<?php 
    query ("SELECT ($xaccess != 0) or ((".getVar($q_art,'IdUser')." = ".getVar($Usr,'Id').") and ('".getVar($q_art,'Published')."' = 'N'))", 'q_xperm');
    fetchRowNum($q_xperm);
    if (getNumVar($q_xperm,0)) {
?>dnl
<P>

<?php  $chngd= 0; ?>dnl

B_MSGBOX(<*Changing article details*>)
	X_MSGBOX_TEXT(<*
<?php 
    query ("UPDATE Articles SET Name='$cName', OnFrontPage='$cOnFrontPage', OnSection='$cOnSection', Keywords='$cKeywords', Public='$cPublic', IsIndexed='N' WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article AND IdLanguage=$sLanguage");
//        print ("UPDATE Articles SET Name='$cName', OnFrontPage='$cOnFrontPage', OnSection='$cOnSection', Keywords='$cKeywords', Public='$cPublic', IsIndexed='N' WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article AND IdLanguage=$sLanguage<br>");
	if ($AFFECTED_ROWS > 0)
		$chngd= 1;

	## added by sebastian
	if (function_exists ("incModFile"))
		incModFile ();
    
	query ("SHOW COLUMNS FROM X".getSVar($q_art,'Type')." LIKE 'F%'", 'q_fld');
    $nr=$NUM_ROWS;
    $query = "";
    $first = true;
    for($loop=0;$loop<$nr;$loop++) {
                fetchRowNum($q_fld);
                $save = false;
                $ischar=strpos(getNumVar($q_fld,1),'char');
                $isdate=strpos(getNumVar($q_fld,1),'date');
                if(!($ischar === false)) $save = true;
                if(!($isdate === false)) $save = true;
                if ($save === true) {                                   // only save the non-blob fields; the blobs are saves separately, by their specific editors
                        if($first === false)
                                $query = $query.", ";
                        $first = false;
                        $fld= getNumVar($q_fld,0);
                        $query = $query." ". $fld."='".encSQL($$fld)."'";
                }
    }
    //print ("<p>UPDATE X".getSVar($q_art,'Type')." SET $query WHERE NrArticle=$Article AND IdLanguage=$sLanguage<br>");
    query ("UPDATE X".getSVar($q_art,'Type')." SET $query WHERE NrArticle=$Article AND IdLanguage=$sLanguage");
        if ($AFFECTED_ROWS > 0)
	$chngd= 1;

    if ($chngd) { ?>dnl
	<LI><?php  putGS('The article has been updated.'); ?></LI>
<?php  } else { ?>dnl
	<LI><?php  putGS('The article cannot be updated or no changes have been made.'); ?></LI>
<?php  } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/issues/sections/articles/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&sLanguage=<?php  p($sLanguage); ?>*>)
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
