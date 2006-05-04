<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_types");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to merge article types."));
	exit;
}

$f_src = trim(Input::get('f_src'));
$f_dest = trim(Input::get('f_dest'));
$f_ok = trim(Input::get('Ok'));
if (ereg('Back to Step 1', $f_ok)) {
	header("Location: /$ADMIN/article_types/merge.php?f_src=$f_src&f_dest=$f_dest");
	exit;
}	

$f_cur_preview = trim(Input::get('f_cur_preview', 'int', -1)); // The currently previewed article
$f_action = trim(Input::get('f_preview_action', 'string', 'NULL')); // Preview actions: either NEXT, PREV, ORIG
$f_cur_lang = trim(Input::get('f_cur_lang', 'int', -1));

// TODO set the defaults to the first article
// look into f_cur_lang
if ($f_cur_preview == -1)
	$f_cur_preview = 0;
if ($f_cur_lang == -1)  
	$f_cur_lang = 1;

if ($f_action == 'Next') { $f_cur_preview++; }
if ($f_action == 'Prev') { $f_cur_preview--; }
if ($f_action == 'Orig') {
	$curPreview =& new Article($f_cur_lang, $f_cur_preview); 
} else { $curPreview =& new Article($f_cur_lang, $f_cur_preview); }

$src =& new ArticleType($f_src);
$dest =& new ArticleType($f_dest);

foreach ($dest->m_dbColumns as $destColumn) {
	$f_src_c[$destColumn->getName()] = trim(Input::get('f_src_'. $destColumn->getName()));
}


$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Merge article type"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_merge.php?f_src=<?php print $f_src; ?>&f_dest=<?php print $f_dest; ?>">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">Merge Article Types<BR>Step 3 of 3</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<b>Merge configuration for merging <?php print $src->getDisplayName(); ?> into <?php print $dest->getDisplayName(); ?>.</b><BR>
	<UL>
	<?php
	foreach ($f_src_c as $destColumn => $srcColumn) {
		$tmp = array_keys($f_src_c, $srcColumn);

		if ($srcColumn == '--None--') { 
			print "<LI><FONT COLOR=\"TAN\">Merge <b>NOTHING</b> into <b>$destColumn</b> (Null merge warning.).</FONT></LI>";
		} else if (count($tmp) > 1) {
			print "<LI><FONT COLOR=\"TAN\">Merge <b>$srcColumn</b> into <b>$destColumn</b></FONT> (Duplicate warning.)</FONT></LI>";
		} else {
			print "<LI><FONT COLOR=\"GREEN\">Merge <b>$srcColumn</b> into <b>$destColumn</b>.</FONT></LI>";
		}

	} ?>


	<?php 
	// do the warning if they select NONE in red
	foreach ($src->m_dbColumns as $srcColumn) {
		if (!in_array($srcColumn->getPrintName(), $f_src_c)) 
			print "<LI><FONT COLOR=\"RED\">(!) Do <B>NOT</B> merge <b>". $srcColumn->getPrintName() ."</b> (No merge warning.)</FONT></LI>"; 
	} ?>
	</UL>	
	</TD>
	
</TR>
<TR>
	<TD COLSPAN="2">
	<B>Preview a sample of the merge configuration.</B> <SMALL>(Cycle through your articles to verify that the merge configuration is correct.)</SMALL>
	</TD>
</TR>

<TR>
	<TD COLSPAN="2">
	<B>Preview of HellowWorld.doc (<A HREF="#">View the source (<?php print $src->getDisplayName(); ?>) version of HellowWorld.doc.</A>). 
	1 of <?php print $src->getNumArticles(); ?>. 
	<IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/previous.png" BORDER="0">&nbsp;
	<IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/next.png" BORDER="0">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<TABLE BORDER="1"><TR><TD>
	<DIV>
	BLAH BLAH BLAH (PREVIEW OF ARTICLE HERE)
	
	</DIV>
	</TD></TR></TABLE>
	</TD>
</TR>

<TR>
	<TD>
	<INPUT TYPE="CHECKBOX" NAME="f_del_src">Delete the source article type (<?php print $src->getDisplayName(); ?>) when finished.
	</TD>
	<TD>
	<b>Clicking "Merge" will merge <?php print $src->getNumArticles(); ?> articles.</b>
	</TD>
<TR>	
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	
	<?php foreach ($dest->m_dbColumns as $destColumn) { ?>
	<INPUT TYPE="HIDDEN" NAME="f_src_<?php print $destColumn->getName(); ?>" VALUE="<?php print $f_src_c[$destColumn->getName()]; ?>">
	<?php } ?>

	<INPUT TYPE="HIDDEN" NAME="f_cur_lang" VALUE="<?php $curPreview->getLanguage(); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_cur_preview" VALUE="<?php $curPreview->getArticleId(); ?>">
	
	<INPUT TYPE="submit" class="button" NAME="Ok" VALUE="<?php  putGS('Back to Step 2'); ?>">
	<INPUT TYPE="submit" class="button" NAME="Ok" VALUE="<?php  putGS('Merge!'); ?>">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>