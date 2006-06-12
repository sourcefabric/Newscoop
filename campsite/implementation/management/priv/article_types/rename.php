<?php
camp_load_translation_strings("article_types");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to rename article types."));
	exit;
}

$articleTypes = ArticleType::GetArticleTypes();
$f_name = trim(Input::get('f_name'));

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Rename article type"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_rename.php" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<INPUT TYPE="hidden" VALUE="<?php p($f_name); ?>" NAME="f_oldName">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php putGS("Rename article type"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
</TR>
<TR><TD COLSPAN="2">You may only use letters or the underscore (_) for a name.</TD></TR>
<TR>
	<TD ALIGN="LEFT" ><?php  putGS("Name"); ?>:</TD>
	<TD ALIGN="LEFT">
	<INPUT TYPE="TEXT" VALUE="<?php p($f_name); ?>" class="input_text" NAME="f_name" ALT="alnum|1|A|false|false|_" emsg="<?php putGS("The name field may only contain letters and the underscore (_) character."); ?>" SIZE="15" MAXLENGTH="15">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="submit" class="button" NAME="Ok" VALUE="<?php  putGS('Save'); ?>">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>