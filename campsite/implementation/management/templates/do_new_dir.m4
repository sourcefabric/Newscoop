B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTempl*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Creating new folder*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to create new folders.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<? todef('cPath'); ?>dnl
B_HEADER(<*Creating new folder*>)
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
B_MSGBOX(<*Creating new folder*>)
	X_MSGBOX_TEXT(<*
<? 
    if ($cName == "") { 
	$correct= 0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<? }

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
<? if ($correct) { ?>dnl
		<A HREF="X_ROOT/templates/new_dir.php?Path=<? pencURL(decS($cPath)); ?>"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Create another folder"></A>
		<A HREF="<? p(decS($cPath)); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>
		<A HREF="X_ROOT/templates/new_dir.php?Path=<? pencURL(decS($cPath)); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
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

