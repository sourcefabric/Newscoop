INCLUDE_PHP_LIB(<*$ADMIN_DIR/templates*>)dnl
B_DATABASE

<?php
	todefnum('Issue');
	todefnum('Pub');
	todefnum('What');
	todefnum('Language');
	todef('Path');
	todef('REQUEST_URI', $_SERVER[REQUEST_URI]);
?>dnl
CHECK_BASIC_ACCESS
<?php  if ($What != 0) { ?>dnl
CHECK_ACCESS(<*ManageTempl*>)dnl
<?php  } ?>dnl

B_HEAD
 <?php
     if ($What) { ?>
  X_TITLE(<*Select template*>)
     <?php  } else { ?>
  X_TITLE(<*Templates management*>)
 <?php  } ?>
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
if ($access) {

SET_ACCESS(<*mta*>, <*ManageTempl*>)
SET_ACCESS(<*dta*>, <*DeleteTempl*>)
?>dnl
B_STYLE
E_STYLE

B_BODY

<?php
    if ($What) { ?>
 B_HEADER(<*Select template*>)
    <?php  } else { ?>
 B_HEADER(<*Templates*>)
    <?php  } ?>
B_HEADER_BUTTONS
<?php  if ($What) { ?>dnl
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
X_HBUTTON(<*Publications*>, <*pub/*>)
<?php  } ?>dnl
E_HEADER_BUTTONS
E_HEADER

<?php
    $NUM_ROWS=0;
    if ($What)
 query ("SELECT Name, FrontPage, SingleArticle FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS != 0 || $What == 0) {
 if ($What)
     query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
 if (($NUM_ROWS != 0) || ($What == 0)) {
   ?>dnl
B_CURRENT
<?php  if ($What) { ?>dnl
X_CURRENT(<*Publication*>, <*<?php  fetchRow($q_pub); pgetHVar($q_pub,'Name'); ?>*>)
X_CURRENT(<*Issue*>, <*<?php  pencURL($Issue); ?>. <?php  fetchRow($q_iss); pgetHVar($q_iss,'Name'); ?> (<?php
    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_language');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
 fetchRowNum($q_language);
 pencHTML( getNumVar($q_language,0));
    }
?>)*>)
<?php  } ?>dnl

<P>
B_MSGBOX(<*Refreshing the templates directory*>)

<?php

$templates_dir = $DOCUMENT_ROOT . "/look/";
$missing_templates = array();
$deleted_templates = array();
$verify_errors = array();
$verify_res = verify_templates($templates_dir, $missing_templates, $deleted_templates, $verify_errors);

$register_errors = array();
$register_res = register_templates($templates_dir, $register_errors);

if (sizeof($verify_errors) > 0 || sizeof($register_errors) > 0) {
?>
	X_MSGBOX_TEXT(<*<font color=red><b>
	<?php
	putGS('There were the following errors while refreshing the template directory:');
	foreach($verify_errors as $err)
		echo "<li>" . $err . "</li>\n";
	foreach($register_errors as $err)
		echo "<li>" . $err . "</li>\n";
	?>
	</b></font>*>)
<?php
}

if (sizeof($missing_templates) > 0) {
?>
	X_MSGBOX_TEXT(<*<font color=red><b>
	<?php
	putGS('The following templates were registered and used but are missing from the templates directory:');
	foreach($missing_templates as $tpl_id=>$tpl_name)
		echo "<li>" . $tpl_name . "</li>\n";
	?>
	</b></font>*>)
<?php
}

if (sizeof($verify_errors) == 0 && sizeof($register_errors) == 0 && sizeof($missing_templates) == 0) {
?>
	X_MSGBOX_TEXT(<*<?php putGS('The templates directory was refreshed succesfully.') ?>*>)
<?php
}

if (sizeof($deleted_templates) > 0) {
?>
	X_MSGBOX_TEXT(<*
	<?php 
	putGS('The following templates had been registered but are missing from the templates directory and were deleted:');
	foreach($deleted_templates as $tpl_id=>$tpl_name)
		echo "<li>" . $tpl_name . "</li>\n";
	?>
	*>)
<?php
}

if ($register_res > 0) {
?>
	X_MSGBOX_TEXT(<*<?php putGS('There were $1 new template(s) found.', $register_res) ?>*>)
<?php
}

if (sizeof($verify_errors) == 0 && sizeof($register_errors) == 0 
	&& sizeof($missing_templates) == 0 && sizeof($deleted_templates) == 0 && $register_res == 0) {
?>
	X_REFRESH(<*0; URL=<?php echo $Path; ?>*>)
<?php
}

?>

B_MSGBOX_BUTTONS
	REDIRECT(<*Done*>, <*Done*>, <*<?php echo $Path; ?>*>)
E_MSGBOX_BUTTONS
E_MSGBOX

<?php
} else {
?>dnl
<BLOCKQUOTE>
 <LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  }
} else { ?>dnl
<BLOCKQUOTE>
 <LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML


