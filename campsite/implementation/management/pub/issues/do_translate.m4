INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageIssue*>)

B_HEAD
	X_TITLE(<*Adding new translation*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add issues.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todef('cName');
    todef('cShortName');
    todefnum('cNumber');
    todefnum('cLang');
    todefnum('Language');
    todefnum('cPub');
?>
B_HEADER(<*Adding new translation*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  pencURL($cPub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php  
    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new translation*>)
	X_MSGBOX_TEXT(<*
<?php 
    $cName=trim($cName);
    $cNumber=trim($cNumber);
    
    if ($cLang == 0) {
	$correct= 0;
	?>dnl
		<LI><?php  putGS('You must select a language.'); ?></LI>
    <?php  }
    
    if ($cName == "" || $cName == " ") {
	$correct= 0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
    <?php  }
    
    if ($cShortName == "" || $cShortName == " ") {
	$correct= 0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('URL Name').'</B>'); ?></LI>
    <?php  }
    
    if ($cNumber == "" || $cNumber == " ") {
	$correct= 0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Number').'</B>'); ?></LI>
    <?php  }
    
    if ($correct) {
	query ("INSERT IGNORE INTO Issues SET Name='$cName', ShortName = '$cShortName', IdPublication=$cPub, IdLanguage=$cLang, Number=$cNumber");
	$created= ($AFFECTED_ROWS > 0);
	if($created){
		$sql = "SELECT * FROM Sections WHERE IdPublication=$cPub AND NrIssue=$cNumber AND IdLanguage=$Language";
		query($sql, 'q_sect');
		$nr2=$NUM_ROWS;
		for($loop2=0;$loop2<$nr2;$loop2++) {
			fetchRow($q_sect);
			$section = getSVar($q_sect,'Number');
			$sql = "INSERT IGNORE INTO Sections SET IdPublication=$cPub, NrIssue=$cNumber, IdLanguage=$cLang, Number=$section, ShortName='$section', Name='".getSVar($q_sect,'Name')."'";
			query($sql);
		}
	}
    }

    if ($created) { ?>dnl
		<LI><?php  putGS('The issue $1 has been successfuly added.','<B>'.encHTML(decS($cName)).'</B>' ); ?></LI>
X_AUDIT(<*11*>, <*getGS('Issue $1 added',$cName)*>)
<?php  } else {
    if ($correct != 0) { ?>dnl
		<LI><?php  putGS('The issue could not be added.'); ?></LI><LI><?php  putGS('Please check if another issue with the same number/language does not already exist.'); ?></LI>
<?php  }
} ?>dnl
		*>)
<?php  if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*Another*>, <*Add another*>, <*X_ROOT/pub/issues/translate.php?Pub=<?php  pencURL($cPub); ?>&Issue=<?php  pencURL($cNumber); ?>*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/issues/?Pub=<?php  pencURL($cPub); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } else { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/issues/translate.php?Pub=<?php  pencURL($cPub); ?>&Issue=<?php  pencURL($cNumber); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } ?>dnl
E_MSGBOX
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
