INCLUDE_PHP_LIB(<*$ADMIN_DIR/templates*>)
<?php
    require('./lib_upload.php');
?>
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTempl*>)

B_HEAD
 X_TITLE(<*Uploading template*>)
<?php  if ($access == 0) { ?>dnl
 X_AD(<*You do not have the right to upload templates.*>)
<?php  } else {
 //dSystem( "$scriptBase/process_t '$Id'");
    $debugLevelHigh=false;
    $debugLevelLow=false;

    ## added by sebastian ############################
    todef('Charset');
    todef('Path');
    todef('File', $HTTP_POST_FILES[File][tmp_name]);
    todef('File_name', $HTTP_POST_FILES[File][name]);
    todef('UNIQUE_ID');
    ##################################################
    
    doUpload("File",$Charset,$DOCUMENT_ROOT.'/'.decS($Path));

} ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php  todefnum('Id'); ?>dnl
B_HEADER(<*Uploading template*>)
B_HEADER_BUTTONS
X_HEADER_NO_BUTTONS
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Path*>, <*<?php  pencHTML(decURL($Path)); ?>*>)
E_CURRENT

<P>
B_MSGBOX(<*Uploading template*>)
 X_MSGBOX_TEXT(<* <LI> <?php  p($FSresult)?> </LI> *>)
 B_MSGBOX_BUTTONS
  REDIRECT(<*Done*>, <*Done*>, <*<?php  pencHTML(decS($Path)); ?>*>)
 E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<?php
	$fileName=$GLOBALS["File"."_name"];
	$templates_dir = $DOCUMENT_ROOT . '/look/';
	register_templates($templates_dir, $errors);
?>

X_AUDIT(<*111*>, <*getGS('Template $1 uploaded', encHTML(decS($fileName)))*>)

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML