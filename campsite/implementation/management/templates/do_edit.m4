B_HTML
INCLUDE_PHP_LIB(<*..*>)dnl
B_DATABASE

CHECK_BASIC_ACCESS
<?php 
	todefnum('What');
	if ($What != 0) {
?>dnl
CHECK_ACCESS(<*ManageTempl*>)dnl
<?php  } ?>dnl

<?php 
todef('query');
todef('Path');
todef('Name');
todef('cField');
?>

B_HEAD
	X_EXPIRES
	X_TITLE(<*Templates management*>)

<?php 
    if ($access == 0) {
	if ($What) { ?>dnl
	X_AD(<*You do not have the right to change default templates.*>)
<?php  } else { ?>dnl
	X_LOGOUT
<?php  }
    }
?>dnl
E_HEAD

<?php
if (strncmp($Path, "/look/", 6) != 0) {
	$access = 0;
?>
	X_AD(<*You do not have the right to edit scripts outside the templates directory.*>)
<?php
}
if ($access) {
	SET_ACCESS(<*mta*>, <*ManageTempl*>)
	SET_ACCESS(<*dta*>, <*DeleteTempl*>)
?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Edit template*>)
B_HEADER_BUTTONS

X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Path*>, <*<B><?php  pencHTML(decURL($Path)); ?></B>*>)
X_CURRENT(<*Template*>, <*<B><?php  pencHTML(decURL($Name)); ?></B>*>)
E_CURRENT

<P>

B_MSGBOX(<*Edit template*>)
<?php 
	if($dta){
		$filename = "$DOCUMENT_ROOT".decURL($Path)."$Name";
		$fd = fopen ($filename, "w");
		$nField = str_replace("\\r", "\r", $cField);
		$nField = str_replace("\\n", "\n", $nField);
		$nField = decS($nField);
		$res = fwrite ($fd, $nField);
		if($res >  0){ ?>dnl
			X_MSGBOX_TEXT(<* <LI><?php putGS('The template has been saved.'); ?></LI> *>)
		<?php  }
		else { ?>dnl
			X_MSGBOX_TEXT(<* <LI><?php  putGS('The template could not be saved'); ?></LI> *>)
		<?php  }
		fclose ($fd);
	?>dnl
	X_AUDIT(<*113*>, <*getGS('Template $1 was changed',encHTML(decS($Path)).encHTML(decS($Name)) )*>)	
		B_MSGBOX_BUTTONS
		<?php  if ($res > 0) { ?>dnl
			REDIRECT(<*Done*>, <*Done*>, <*<?php  pencHTML(decS($Path)); ?>*>)
		<?php  } else { ?>dnl
			REDIRECT(<*OK*>, <*OK*>, <*<?php  pencHTML(decS($Path)); ?>*>)
		<?php  } ?>dnl
		E_MSGBOX_BUTTONS
	<?php }
	else{?>
		X_MSGBOX_TEXT(<* <LI><?php putGS('You do not have the right to modify templates.'); ?></LI> *>)
		B_MSGBOX_BUTTONS
			REDIRECT(<*OK*>, <*OK*>, <*<?php  pencHTML(decS($Path)); ?>*>)
		E_MSGBOX_BUTTONS
	<?php }
?>dnl
	
E_MSGBOX

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML


