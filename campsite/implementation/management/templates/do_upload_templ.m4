B_HTML
INCLUDE_PHP_LIB(<*..*>)
<?php
    require('./lib_upload.php');
?>
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTempl*>)

B_HEAD
 X_EXPIRES
 X_TITLE(<*Uploading template*>)
<?php  if ($access == 0) { ?>dnl
 X_AD(<*You do not have the right to upload templates.*>)
<?php  } else {
    $debugLevelHigh=false;
    $debugLevelLow=false;

    ## added by sebastian ############################
    todef('Charset');
    todef('Path');
    $GLOBALS['File'] = $HTTP_POST_FILES[File][tmp_name];
    $GLOBALS['File_name'] = $HTTP_POST_FILES[File][name];
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
X_CURRENT(<*Path*>, <*<B><?php  pencHTML(decURL($Path)); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Uploading template*>)
 X_MSGBOX_TEXT(<* <LI> <?php  p($FSresult)?> </LI> *>)
 B_MSGBOX_BUTTONS
  REDIRECT(<*Done*>, <*Done*>, <*<?php  pencHTML(decS($Path)); ?>*>)
 E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<?php        $fileName=$GLOBALS["File"."_name"]; ?>

X_AUDIT(<*111*>, <*getGS('Template $1 uploaded', encHTML(decS($fileName)))*>)

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML