B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTempl*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Creating new template*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to create new templates.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<? todef('cPath'); ?>dnl
B_HEADER(<*Creating new template*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Templates*>, <*templates/?Path=<? pencURL(decS($cPath)); ?>*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Path*>, <*<B><? pencHTML(decURL($cPath)); ?></B>*>)
E_CURRENT

<? 
    todef('cName');
    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Creating new template*>)
	X_MSGBOX_TEXT(<*
<? 
    if ($cName == "") { 
	$correct= 0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<? }

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
	}
	else {
	    putGS('The template $1 could not be created.','<b>'.$cName.'</B>');
	    $correct=0;
	}
	
    }
?>dnl
		*>)
		
<? if ($ok) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('Do you want to edit the template ?'); ?></LI>*>)
<? } ?>dnl		
X_AUDIT(<*114*>, <*getGS('New template $1 was created', encHTML(decS($cPath)).encHTML(decS($cName)))*>)		
	B_MSGBOX_BUTTONS
<? if ($ok) { ?>dnl
		 <A HREF="X_ROOT/templates/edit_template.php?Path=<? pencURL(decS($cPath)); ?>&Name=<?pencURL($cName); ?>"><IMG SRC="X_ROOT/img/button/yes.gif" BORDER="0" ALT="Yes"></A>
		<A HREF="<? p(decS($cPath)) ?>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
<? } else { ?>
	<A HREF="X_ROOT/templates/new_template.php?Path=<? pencURL(decS($cPath)); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<? } ?>dnl

	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

