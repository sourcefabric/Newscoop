B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/templates*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteTempl*>)

<?php 
    todef('Path');
    todef('Name');
    todefnum('What');
?>dnl

B_HEAD
	X_EXPIRES
	<?php  if ($What == 1) {?>dnl
		X_TITLE(<*Deleting template*>)
	<?php }
	else {?>dnl
		X_TITLE(<*Deleting folder*>)
	<?php }?>dnl
	
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete templates.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY


<?php if ($What == 1){?>dnl
	B_HEADER(<*Deleting template*>)
<?php }
else{?>dnl
	B_HEADER(<*Deleting folder*>)
<?php }?>dnl
B_HEADER_BUTTONS
X_HBUTTON(<*Templates*>, <*templates/?Path=<?php  pencURL(decS($Path)); ?>*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Path*>, <*<B><?php  pencHTML(decURL($Path)); ?></B>*>)
E_CURRENT

<P>

<?php  if($What == 1){?>dnl
	B_MSGBOX(<*Deleting template*>)
<?php }
else {?>dnl
	B_MSGBOX(<*Deleting folder*>)
<?php }?>dnl
	X_MSGBOX_TEXT(<*
<?php 
	$dir = decURL(decS($Path)).decURL(decS($Name));
	$file = $DOCUMENT_ROOT.decURL($Path).$Name;
	$file_path = $DOCUMENT_ROOT.decURL($Path);
	$templates_dir = $DOCUMENT_ROOT.'/look/';
	$olderr =  error_reporting(0);

	if ($What=='0') {
		$msg_ok="The folder has been deleted.";
		$msg_fail="The folder could not be deleted.";
		$res= rmdir($file);
	} else {
		$template_path = template_path($Path, $Name);
		if (template_is_used($template_path) == false) {
			$msg_ok="The template has been deleted.";
			$msg_fail="The template could not be deleted.";
			$res = unlink($file);
			verify_templates($templates_dir, $mt, $dt, $errors);
		} else {
			$msg_fail = "The template $1 is in use and can not be deleted.";
			$res = 0;
		}
	}

	error_reporting($olderr);

	if ($res == 0) $msg = $msg_fail;
	else $msg = $msg_ok;

	print "<LI>";
	putGS($msg, $template_path);
	print "</li>";
?>
	*>)
<?php if ($res != 0) { ?>
	X_AUDIT(<*112*>, <*getGS('Templates deleted from $1',encHTML(decS($Path)).encHTML(decS($Name)) )*>)
<?php } ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*Done*>, <*Done*>, <*<?php  p(decS($Path)); ?>*>)
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

