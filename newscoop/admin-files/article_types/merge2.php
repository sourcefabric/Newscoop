<?php
camp_load_translation_strings("article_types");
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to merge article types."));
	exit;
}

$f_src = trim(Input::get('f_src'));
$f_dest = trim(Input::get('f_dest'));
$errorMsgs = array();

if ($f_src == $f_dest) {
	$errorMsgs[] = getGS("You cannot merge the same type into itself.");
}

$src = new ArticleType($f_src);
$dest = new ArticleType($f_dest);

$srcNumArticles = $src->getNumArticles();

if ($srcNumArticles <= 0) {
    $errorMsgs[] = getGS("The source article type ($1) does not have any articles.", $f_src);
}

if (count($errorMsgs)) {

	$crumbs = array();
	$crumbs[] = array(getGS("Configure"), "");
	$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
	$crumbs[] = array(getGS("Merge article type"), "");

	echo camp_html_breadcrumbs($crumbs);

	?>
	<P>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Merge Article Types: Step $1 of $2", "1", "3"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
			<BLOCKQUOTE>
			<?php
			foreach ($errorMsgs as $errorMsg) {
				echo "<li>".$errorMsg."</li>";
			}
			?>
			</BLOCKQUOTE>
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/merge.php?f_src=<?php p($f_src); ?>&f_dest=<?php p($f_dest); ?>'">
		</DIV>
		</TD>
	</TR>
	</TABLE>
	<P>

	<?php camp_html_copyright_notice(); return; ?>

<?php
} // endif count(errorMessages)


$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Merge article type"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/article_types/merge3.php?f_src=<?php print $f_src; ?>&f_dest=<?php print $f_dest; ?>">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
	<TD COLSPAN="3">
		<b><?php putGS("Merge Article Types: Step $1 of $2", "2", "3"); ?></b>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<tr>
	<td>
		<table cellpadding="2">
		<tr>
			<td colspan="3" style="padding-bottom: 10px;">
				<b><?php putGS("There are $1 articles associated with $2 that will be merged.", $srcNumArticles, $src->getDisplayName());?></b>

			</td>
		</tr>
		<TR>
			<TD align="right">
				<u><?php putGS("Source Article Type");?></u>
			</TD>
			<td>
			</td>
			<TD align="left" style="padding-left: 2px;">
				<u><?php putGS("Destination Article Type"); ?></u>
			</TD>
		</TR>
		<tr>
			<td align="right">
				 <b><?php print $src->getDisplayName(); ?></b>
			</td>
			<td>-&gt;</td>
			<td align="left">
				<b><?php print $dest->getDisplayName(); ?></b>
			</td>
		</tr>
		<?php foreach ($dest->getUserDefinedColumns(null, true, true) as $destColumn) { ?>
		<TR>
			<TD align="right">
				<SELECT CLASS="input_select" NAME="f_src_<?php print $destColumn->getPrintName(); ?>">
				<?php
				$selected = false;
				foreach ($src->getUserDefinedColumns(null, true, true) as $srcColumn) {
					if (!$destColumn->isConvertibleFrom($srcColumn)) {
						continue;
					}
					$selected = ($srcColumn->getType() == $destColumn->getType()
					|| $destColumn->getPrintName() == $srcColumn->getPrintName()) && !$selected;
				?>
					<OPTION VALUE="<?php print $srcColumn->getPrintName(); ?>" <?php if ($selected) { print "SELECTED"; } ?>><?php print $srcColumn->getDisplayName(); ?></OPTION>
				<?php } ?>
					<OPTION VALUE="NULL" <?php if (!$selected) { print "SELECTED"; } ?>><?php putGS("--None--"); ?></OPTION>
				</SELECT>
			</TD>
			<td>-&gt;</td>
			<TD align="left"><?php print $destColumn->getDisplayName(); ?></TD>
		</TR>
		<?php } ?>
		</table>
	</td>
</tr>

<TR>
	<TD COLSPAN="2" align="center" style="padding-top: 20px; padding-bottom: 10px;">
	<INPUT TYPE="hidden" NAME="f_src" VALUE="<?php print $f_src; ?>">
	<INPUT TYPE="hidden" NAME="f_dest" VALUE="<?php print $f_dest; ?>">
	<INPUT TYPE="hidden" NAME="f_action" VALUE="">
	<INPUT TYPE="submit" class="button" NAME="Ok" ONCLICK="dialog.f_action.value='Step1'" VALUE="<?php  putGS('Back to Step 1'); ?>">
	&nbsp;&nbsp;&nbsp;&nbsp;
	<INPUT TYPE="submit" class="button" NAME="Ok" ONCLICK="dialog.f_action.value='Step3'" VALUE="<?php  putGS('Go to Step 3'); ?>">
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
