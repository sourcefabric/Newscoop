B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTopics*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Delete topic*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete topics.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete topic*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('IdCateg');
    todefnum('DelCateg');
    query ("SELECT * FROM Topics WHERE Id=$DelCateg", 'p');
    if ($NUM_ROWS) {
	fetchRow($p);
?>dnl

B_CURRENT
	<?
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
B_MSGBOX(<*Delete topic*>)
	X_MSGBOX_TEXT(<*<LI><? putGS('Do you want to delete the topic $1?','<B>'.getHVar($p,'Name').'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="IdCateg" VALUE="<? p($IdCateg); ?>">
		<INPUT TYPE="HIDDEN" NAME="DelCateg" VALUE="<? p($DelCateg); ?>">
		<INPUT TYPE="IMAGE" NAME="Yes" SRC="X_ROOT/img/button/yes.gif" BORDER="0">
		<A HREF="X_ROOT/topics/index.php?IdCateg=<?p($IdCateg);?>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such topic.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
