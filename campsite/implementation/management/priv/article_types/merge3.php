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

$src =& new ArticleType($f_src);
$dest =& new ArticleType($f_dest);

foreach ($dest->m_dbColumns as $columnName) { 
	



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
	<LI><FONT COLOR="GREEN">Merging Field1 into Intro</FONT>
	<LI><FONT COLOR="GREEN">Merging Field2 into Body</FONT>
	<LI><FONT COLOR="YELLOW">Merging NOTHING into Bibliography (NULL MERGE WARNING)</FONT>
	<LI><FONT COLOR="YELLOW">Merging MyCaption into caption_small (DUPLICATE WARNING)</FONT>
	<LI><FONT COLOR="YELLOW">Merging MyCaption into caption_lg (DUPLICATE WARNING)</FONT>
	<LI><FONT COLOR="RED">! Do NOT merge Field3</FONT>
	<LI><FONT COLOR="RED">! Do NOT merge Field4</FONT>
	</UL>	
	</TD>
	
</TR>
<TR>
	<TD COLSPAN="2">
	<B>Preview a sample of the merge configuration.</B><BR>
	Cycle through your articles to verify that the merge configuration is correct.
	</TD>
</TR>

<TR>
	<TD COLSPAN="2">
	<B>Preview of HellowWorld.doc (<A HREF="#">View the source (<?php print $src->getDisplayName(); ?>) version of HellowWorld.doc.</A>). 
	1 of 213. 
	<IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/previous.png" BORDER="0">&nbsp;
	<IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/next.png" BORDER="0">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	BLAH BLAH BLAH (PREVIEW OF ARTICLE HERE)
	</TD>
</TR>

<TR>
	<TD>
	<INPUT TYPE="CHECKBOX" NAME="f_del_src"> Delete the source article type (<?php print $src->getDisplayName(); ?>) when finished.
	</TD>
	<TD>
	Will merge 203 articles.
	</TD>
<TR>	
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="submit" class="button" NAME="Ok" VALUE="<?php  putGS('Back to Step 2'); ?>">
	<INPUT TYPE="submit" class="button" NAME="Ok" VALUE="<?php  putGS('Merge!'); ?>">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>