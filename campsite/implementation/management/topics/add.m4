B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTopics*>)

B_HEAD
	X_TITLE(<*Add new topic*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add topics.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new topic*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER
<?
    todefnum('IdCateg');
?>dnl

B_CURRENT
	<?
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
	X_CURRENT(<*Topic*>, <*<B><?p($Path);?></B>*>)
E_CURRENT

<P>
B_DIALOG(<*Add new topic*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="32">
		<INPUT TYPE="HIDDEN" NAME="IdCateg" VALUE="<?p($IdCateg);?>">
	E_DIALOG_INPUT
	
	B_DIALOG_BUTTONS
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<?
    todef('Back');
    if ($Back != "") { ?>dnl
		<A HREF="<? print($Back); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/topics/index.php?IdCateg=<?p($IdCateg);?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } ?>dnl

	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
