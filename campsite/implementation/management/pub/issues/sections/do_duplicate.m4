B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues/sections*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Duplicating section*>)
E_HEAD

<?php 
SET_ACCESS(<*aaa*>, <*AddArticle*>)
SET_ACCESS(<*msa*>, <*ManageSection*>)
if ($aaa != 0 && $msa != 0) {
?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
	todefnum('Language');
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Section');
	todefnum('dstPub');
	todefnum('dstIssue');
	todefnum('dstSection');
?>dnl
B_HEADER(<*Duplicating section*>)
B_HEADER_BUTTONS

X_HBUTTON(<*Sections*>, <*pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  p($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT * FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language AND Number=$Section", 'q_sect');
    if ($NUM_ROWS) {
	query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	    if ($NUM_ROWS) {
		query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		fetchRow($q_sect);
		fetchRow($q_iss);
		fetchRow($q_pub);
		fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Publication*>, <*<B><?php  pgetHVar($q_pub,'Name'); ?></B>*>)
X_CURRENT(<*Issue*>, <*<B><?php  pgetHVar($q_iss,'Number'); ?>. <?php  pgetHVar($q_iss,'Name'); ?> (<?php  pgetHVar($q_lang,'Name'); ?>)</B>*>)
X_CURRENT(<*Section*>, <*<B><?php  pgetHVar($q_sect,'Number'); ?>. <?php  pgetHVar($q_sect,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Duplicating section*>)
	X_MSGBOX_TEXT(<*
<?php 
	$correct = true;
	if ($Language <= 0 || $Pub <= 0 || $Issue <= 0 || $Section <= 0 || $dstPub <= 0 || $dstIssue <= 0) {
		$correct = false;
		echo "<LI>"; putGS('Invalid parameters received'); echo "</LI>\n";
	}
	$sql = "select * from Sections where IdPublication = " . $Pub . " and NrIssue = " . $Issue
	     . " and IdLanguage = " . $Language . " and Number = " . $Section;
	query($sql, 's_curr_sect');
	if ($NUM_ROWS == 0) {
		$correct = false;
		echo "<LI>"; putGS('Invalid parameters received'); echo "</LI>\n";
	}
	if ($correct) {
		$sql = "SELECT * FROM Issues WHERE IdPublication=$dstPub AND Number=$dstIssue AND IdLanguage=$Language";
		query($sql, 'q_dst_iss');
		if ($NUM_ROWS == 0) {
			$correct = false;
			echo "<LI>"; putGS('Invalid parameters received'); echo "</LI>\n";
		} else {
			fetchRow($q_dst_iss);
		}
		$sql = "SELECT * FROM Publications WHERE Id=$dstPub";
		query($sql, 'q_dst_pub');
		if ($NUM_ROWS == 0) {
			$correct = false;
			echo "<LI>"; putGS('Invalid parameters received'); echo "</LI>\n";
		} else {
			fetchRow($q_dst_pub);
		}
	}
	$created = false;
	if ($correct) {
		fetchRow($s_curr_sect);
		$sect_name = getVar($s_curr_sect, 'Name');
		$sql = "select * from Sections where IdPublication = " . $dstPub . " and NrIssue = " . $dstIssue
		     . " and IdLanguage = " . $Language . " and Number = " . $dstSection;
		query($sql);
		if ($NUM_ROWS > 0) {
			$sql = "update Sections set Name = '" . $sect_name . "' where IdPublication = " . $dstPub
			     . " and NrIssue = " . $dstIssue . " and IdLanguage = " . $Language
			     . " and Number = " . $dstSection;
		} else {
			$sql = "insert into Sections (IdPublication, NrIssue, IdLanguage, Number, Name) values(" . $dstPub
			     . ", " . $dstIssue . ", " . $Language . ", " . $dstSection . ", '"
			     . mysql_escape_string($sect_name) . "')";
		}
		query($sql);
		$sql = "select * from Articles where IdPublication = " . $Pub . " and NrIssue = " . $Issue
		     . " and NrSection = " . $Section . " and IdLanguage = " . $Language;
		query($sql, 's_articles');
		$articles = $NUM_ROWS;
		if ($articles) {
			for($loop = 0; $loop < $articles; $loop++) {
				fetchRow($s_articles);
				$Article = getVar($s_articles, 'Number');
				$new_id = duplicate_article($Article, $Language, $UserID, $dstPub, $dstIssue, $dstSection, $msg, $name, $new_name);
				if ($new_id > 0) {
					$created = true;
				} else {
					echo "<LI>"; putGS($msg); echo "</LI>\n";
				}
			}
		}
	}

	if ($correct) {
	if ($created) { ?>dnl
	<LI><?php  putGS('Section $1 has been duplicated to $2. $3 of $4', '<B>'.encHTML(decS($sect_name)).'</B>', '<B>'.$dstIssue.'</B>', '<B>'.getHVar($q_dst_iss,'Name').'</B>', '<B>'.getHVar($q_dst_pub,'Name').'</B>'); ?></LI>
X_AUDIT(<*31*>, <*getGS('Section $1 has been duplicated to $2. $3 of $4', encHTML(decS($sect_name)), $dstIssue, getHVar($q_dst_iss,'Name'), getHVar($q_dst_pub,'Name') )*>)
<?php  } else { ?>dnl
	<LI><?php  putGS('The section $1 could not be duplicated','<B>'.encHTML(decS($sect_name)).'</B>'); ?></LI>
<?php  }
}
?>dnl
	*>)
	B_MSGBOX_BUTTONS
<?php  if ($created) { ?>
	REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/issues/sections/articles/?Pub=<?php  p($dstPub); ?>&Issue=<?php  p($dstIssue); ?>&Section=<?php  p($dstSection); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($Language); ?>*>)
<?php  } else { ?>
	REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>*>)
<?php  } ?>dnl
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

X_HR
X_COPYRIGHT
E_BODY
<?php 
} else {
	if ($aaa == 0) {
?>
		X_AD(<*You do not have the right to add articles.*>)
<?php 
	}
	if ($msa == 0) {
?>
		X_AD(<*You do not have the right to add sections.*>)
<?php 
	}
}
?>dnl

E_DATABASE
E_HTML
