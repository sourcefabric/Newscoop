B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteTempl*>)

<?
    todef('Path');
    todef('Name');
    todefnum('What');
?>dnl

B_HEAD
	X_EXPIRES
	<? if ($What == 1) {?>dnl
		X_TITLE(<*Deleting template*>)
	<?}
	else {?>dnl
		X_TITLE(<*Deleting folder*>)
	<?}?>dnl
	
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete templates.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY


<?if ($What == 1){?>dnl
	B_HEADER(<*Deleting template*>)
<?}
else{?>dnl
	B_HEADER(<*Deleting folder*>)
<?}?>dnl
B_HEADER_BUTTONS
X_HBUTTON(<*Templates*>, <*templates/?Path=<? pencURL(decS($Path)); ?>*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Path*>, <*<B><? pencHTML(decURL($Path)); ?></B>*>)
E_CURRENT

<P>

<? if($What == 1){?>dnl
	B_MSGBOX(<*Deleting template*>)
<?}
else {?>dnl
	B_MSGBOX(<*Deleting folder*>)
<?}?>dnl
	X_MSGBOX_TEXT(<*
<?
	$dir=decURL(decS($Path)).decURL(decS($Name));
	$file = $DOCUMENT_ROOT.decURL($Path).$Name;
	$olderr =  error_reporting(0);

    if ($What=='0') {
	$msg_ok="The folder has been deleted.";
	$msg_fail="The folder could not be deleted.";
	$res= rmdir($file);
    } else {
	$msg_ok="The template has been deleted.";
	$msg_fail="The template could not be deleted.";
	$res = unlink($file);
    }

    	error_reporting($olderr);

    if($res == 0) $msg=$msg_fail;
    else $msg=$msg_ok;

    print "<LI>";
    putGS($msg);
    print "</li>";
    
    
?>
X_AUDIT(<*112*>, <*getGS('Templates deleted from $1',encHTML(decS($Path)).encHTML(decS($Name)) )*>)
	*>)
	B_MSGBOX_BUTTONS
		<A HREF="<? p(decS($Path)); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

