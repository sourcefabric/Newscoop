B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/templates*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTempl*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Creating new template*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to create new templates.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php  todef('cPath'); ?>dnl
B_HEADER(<*Creating new template*>)
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
B_MSGBOX(<*Creating new template*>)
	X_MSGBOX_TEXT(<*
<?php  
    if ($cName == "") { 
	$correct= 0; ?>dnl
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<?php  }

    if ($correct) {
	$cName=decS($cName);
	$cName=strtr($cName,'?~#%*&|"\'\\/<>', '_____________');
	$newTempl=$DOCUMENT_ROOT.decURL($cPath).$cName;
	$exists=0;

	if (file_exists($newTempl)) {
	    $exists=1;
						//if (!is_dir($newTempl))
						//$exists=0;
	}
	
	$ok=0;
	
	if (!($exists)) {
		$res = touch ($newTempl);
		if ($res==true) {
                             $ok = 1;
  		}
	
	}
	
	if ($ok) {
		putGS('The template $1 has been created.','<b>'.$cName.'</B>');
		$templates_dir = $DOCUMENT_ROOT . '/look/';
		register_templates($templates_dir, $errors);
	}
	else {
	    putGS('The template $1 could not be created.','<b>'.$cName.'</B>');
	    $correct=0;
	}
	
    }
?>dnl
		*>)
		
<?php  if ($ok) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Do you want to edit the template ?'); ?></LI>*>)
<?php  } ?>dnl		
X_AUDIT(<*114*>, <*getGS('New template $1 was created', encHTML(decS($cPath)).encHTML(decS($cName)))*>)		
	B_MSGBOX_BUTTONS
<?php  if ($ok) { ?>dnl
		REDIRECT(<*Yes*>, <*Yes*>, <*X_ROOT/templates/edit_template.php?Path=<?php  pencURL(decS($cPath)); ?>&Name=<?php pencURL($cName); ?>*>)
		REDIRECT(<*No*>, <*No*>, <*<?php  p(decS($cPath)) ?>*>)
<?php  } else { ?>
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/templates/new_template.php?Path=<?php  pencURL(decS($cPath)); ?>*>)
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

