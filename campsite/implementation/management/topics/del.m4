INCLUDE_PHP_LIB(<*$ADMIN_DIR/topics*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTopics*>)

B_HEAD
	X_TITLE(<*Delete topic*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete topics.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete topic*>)
B_HEADER_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<?php 
    todefnum('IdCateg');
    todefnum('DelCateg');
    query ("SELECT * FROM Topics WHERE Id=$DelCateg", 'p');
    if ($NUM_ROWS) {
	fetchRow($p);
?>dnl

B_CURRENT
	<?php 
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
B_MSGBOX(<*Delete topic*>)
	X_MSGBOX_TEXT(<*<LI><?php  putGS('Do you want to delete the topic $1?','<B>'.getHVar($p,'Name').'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="IdCateg" VALUE="<?php  p($IdCateg); ?>">
		<INPUT TYPE="HIDDEN" NAME="DelCateg" VALUE="<?php  p($DelCateg); ?>">
		SUBMIT(<*Yes*>, <*Yes*>)
		REDIRECT(<*No*>, <*No*>, <*X_ROOT/topics/index.php?IdCateg=<?php p($IdCateg);?>*>)
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such topic.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
