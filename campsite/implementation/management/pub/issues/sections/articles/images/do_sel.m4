B_HTML
INCLUDE_PHP_LIB(<*../../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*AddImage*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Selecting image*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add images*>)
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
    todefnum('Article');
    todefnum('Pub1');
    todefnum('Issue1');
    todefnum('Section1');
    todefnum('Article1');
    todefnum('Language');
    todefnum('sLanguage');
    todefnum('Image');
    todefnum('Number');
?>
B_HEADER(<*Selecting image*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Images*>, <*pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>*>)
X_HBUTTON(<*Articles*>, <*pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>*>)
X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER
<?php 
query ("SELECT Description, Photographer, Place, Date, ContentType FROM Images WHERE IdPublication=$Pub1 AND NrIssue=$Issue1 AND NrSection=$Section1 AND NrArticle=$Article1 AND Number=$Image", 'q_img');
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
		    fetchRow($q_img);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B>*>)
X_CURRENT(<*Article*>, <*<B><?php  pgetHVar($q_art,'Name'); ?></B>*>)
X_CURRENT(<*Image*>, <*<B><?php  pgetHVar($q_img,'Description'); ?> (<?php  pgetHVar($q_img,'Photographer'); ?>, <?php  pgetHVar($q_img,'Place'); ?>, <?php  pgetHVar($q_img,'Date'); ?>)</B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Selecting image*>)
<?php 

    query ("SELECT Number FROM Images WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND NrArticle=$Article", 'q0');
    if ($NUM_ROWS) {
	query ("SELECT max(Number)+1 FROM Images WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND NrArticle=$Article", 'q0');
	fetchRowNum($q0);
	$Number=getNumVar($q0,0);
    }
    else $Number=1;			// didn't I fix that long ago ?
	
    query ("SELECT * FROM Images WHERE IdPublication=$Pub1 AND NrIssue=$Issue1 AND NrSection=$Section1 AND NrArticle=$Article1 AND Number=$Image", 'q0');
    fetchRow($q0);
	$file_name = "/tmp/blob$Article-$Number";
    query ("SELECT Image FROM Images WHERE IdPublication=$Pub1 AND NrIssue=$Issue1 AND NrSection=$Section1 AND NrArticle=$Article1 AND Number=$Image into outfile '$file_name'",'',false);
	if (file_exists($file_name)) {
		query ("lock tables Images write");
		query ("load data infile '/tmp/blob$Article-$Number' INTO TABLE Images (Image)");
		query ("UPDATE Images set IdPublication=$Pub, NrIssue=$Issue, NrSection=$Section, NrArticle=$Article, Number=$Number, Description='".getSVar($q0,'Description')."', Photographer='".getSVar($q0,'Photographer')."', Place='".getSVar($q0,'Place')."', Date='".getSVar($q0,'Date')."', ContentType='".getSVar($q0,'ContentType')."' where Number=0");
	} else {
		$AFFECTED_ROWS = 0;
?>
		X_MSGBOX_TEXT(<*<LI><font color=red>Make sure the mysql user has the right to create files in /tmp directory!!!</font></LI>*>)
<?php 
	}

    $ar= $AFFECTED_ROWS;
    query ("unlock tables");

    if ($ar) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The image $1 has been added.','<B>'.getHVar($q_img,'Description').'</B>'); ?></LI>*>)
X_AUDIT(<*42*>, <*getGS('The image $1 has been added.',getHVar($q_img,'Description'))*>)
<?php  } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('The image $1 could not be added.','<B>'.getHVar($q_img,'Description').'</B>'); ?></LI>*>)
<?php  } ?>dnl
	B_MSGBOX_BUTTONS
	REDIRECT(<*Done*>, <*Done*>, <*/cgi-bin/cleanb?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Number=<?php  p($Number); ?>&NrArticle=<?php  p($Article); ?>*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

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

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such image.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

