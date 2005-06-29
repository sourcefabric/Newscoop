INCLUDE_PHP_LIB(<*$ADMIN_DIR/topics*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTopics*>)

B_HEAD
	X_TITLE(<*Add new topic*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add topics.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new topic*>)
B_HEADER_BUTTONS
E_HEADER_BUTTONS
E_HEADER
<?php 
    todefnum('IdCateg');
?>dnl

B_CURRENT
	<?php 
		todef('Path');
		$crtCat = $IdCateg;
		while($crtCat != 0){
			query ("SELECT * FROM Topics WHERE Id = $crtCat", 'q_cat');
			fetchRow($q_cat);									//should I release the resource ?
			$Path= getVar($q_cat,'Name')."/".$Path;
			$crtCat =getVar($q_cat, 'ParentId');
		}
		if($Path == '') $Path="/";
	?>
	X_CURRENT(<*Topic*>, <*<?php p($Path);?>*>)
E_CURRENT

<P>
B_DIALOG(<*Add new topic*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="255">
		<INPUT TYPE="HIDDEN" NAME="IdCateg" VALUE="<?php p($IdCateg);?>">
	E_DIALOG_INPUT
	
	B_DIALOG_BUTTONS
		SUBMIT(<*Save*>, <*Save changes*>)
<?php 
    todef('Back');
    if ($Back != "") { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*<?php  print($Back); ?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/topics/index.php?IdCateg=<?php p($IdCateg);?>*>)
<?php  } ?>dnl

	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
