B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/templates*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTempl*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Creating new folder*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to create new folders.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php  todef('cPath'); ?>dnl
B_HEADER(<*Creating new folder*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Templates*>, <*templates/?Path=<?php  pencURL(decS($cPath)); ?>*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Path*>, <*<?php  pencHTML(decURL($cPath)); ?>*>)
E_CURRENT

<?php  
    todef('cName');
    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Creating new folder*>)
	X_MSGBOX_TEXT(<*
<?php  
    if ($cName == "") { 
	$correct= 0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<?php  }

    if ($correct) {
	//	dSystem( "$scriptBase/make_dir '$cPath' '$cName' $DOCUMENT_ROOT");
	$cName=decS($cName);
	$cName=strtr($cName,'?~#%*&|"\'\\/<>', '_____________');
	$newdir=$DOCUMENT_ROOT.decURL($cPath).$cName;
	$exists=0;
	if (file_exists($newdir)) {
	    $exists=1;
	    if (!is_dir($newdir))
		$exists=0;
	}
	
	$ok=0;
	
	if (!($exists)) {
	    $dir=mkdir($newdir,0755);
    	    if ($dir===true)
		$ok=1;
	}
	    
	if ($ok) {
	    putGS('The folder $1 has been created','<b>'.$cName.'</B>');
	}
	else {
	    putGS('The folder $1 could not be created','<b>'.$cName.'</B>');
	    $correct=0;
	}
	
    }
?>dnl
		*>)
	B_MSGBOX_BUTTONS
<?php  if ($correct) { ?>dnl
		REDIRECT(<*New*>, <*Add another*>, <*X_ROOT/templates/new_dir.php?Path=<?php  pencURL(decS($cPath)); ?>*>)
		REDIRECT(<*Done*>, <*Done*>, <*<?php  p(decS($cPath)); ?>*>)
<?php  } else { ?>
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/templates/new_dir.php?Path=<?php  pencURL(decS($cPath)); ?>*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

