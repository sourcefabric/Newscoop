<?php
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/common.php');
load_common_include_files("user_subscription_sections");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Section.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/SubscriptionSection.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageSubscriptions')) {
	camp_html_display_error(getGS("You do not have the right to add subscriptions."));
	exit;
}

$f_user_id = Input::Get('f_user_id', 'int', 0);
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_subscription_id = Input::Get('f_subscription_id', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);

$publicationObj =& new Publication($f_publication_id);
$languageObj =& new Language($publicationObj->getDefaultLanguageId());
$manageUser =& new User($f_user_id);

$sections = Section::GetUniqueSections($f_publication_id);
$sectionsByLanguage = Section::GetUniqueSections($f_publication_id, true);

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Subscribers"), "/$ADMIN/users/?uType=Subscribers");
$crumbs[] = array(getGS("Account") . " '".$manageUser->getUserName()."'",
			"/$ADMIN/users/edit.php?User=$f_user_id&uType=Subscribers");
$crumbs[] = array(getGS("Subscriptions"), "/$ADMIN/users/subscriptions/?f_user_id=$f_user_id");
$crumbs[] = array(getGS("Subscribed sections").": ".$publicationObj->getName(), "/$ADMIN/users/subscriptions/sections/?f_user_id=$f_user_id&f_subscription_id=$f_subscription_id&f_publication_id=$f_publication_id");
$crumbs[] = array(getGS("Add new subscription"), "");
echo camp_html_breadcrumbs($crumbs);

?>

<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php"  onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD ALIGN="RIGHT"><?php  putGS("Languages"); ?>:</TD>
	<TD>
		<select name="f_language_set" class="input_select" onchange="ToggleRowVisibility('select_section_lang'); ToggleRowVisibility('select_section_all');">
<?php
		$language_sets = array('select'=>getGS('Individual languages'), 'all'=>getGS('Regardless of the language'));
		foreach ($language_sets as $language_set_id=>$language_set_description) {
			camp_html_select_option($language_set_id, '', $language_set_description);
		}
?>
		</select>
</tr>
<TR id="select_section_all" style="display: none;">
	<TD ALIGN="RIGHT" valign="top"><?php  putGS("Sections"); ?>:</TD>
	<TD>
		<SELECT NAME="f_section_number[]" class="input_select" multiple size="3">
		<?php
		foreach ($sections as $section) {
			if (SubscriptionSection::GetNumSections($f_subscription_id, $section['id'], 0) > 0) {
				continue;
			}
			$id = $section['id'];
			$display = $section['id'] . ' - ' . $section['name'];
			camp_html_select_option($id, '', $display);
	    }
		?>
		</SELECT>
	</TD>
</TR>
<TR id="select_section_lang" valign="top">
	<TD ALIGN="RIGHT" ><?php  putGS("Sections"); ?>:</TD>
	<TD>
		<SELECT NAME="f_section_id[]" class="input_select" multiple size="3">
		<?php
		foreach ($sectionsByLanguage as $section) {
			if (SubscriptionSection::GetNumSections($f_subscription_id, $section['id'], $section['IdLanguage']) > 0) {
				continue;
			}
			$id = $section['id'] . '_' . $section['IdLanguage'];
			$display = $section['id'] . ' - ' . $section['name']
				. ' (' . $section['LangName'] . ')';
			camp_html_select_option($id, '', $display);
	    }
		?>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Start"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_subscription_start_date" SIZE="10" VALUE="<?php  p(date("Y-m-d")); ?>" MAXLENGTH="10" alt="date|yyyy/mm/dd|-" emsg="<?php putGS("You must input a valid date."); ?>"> <?php  putGS('(YYYY-MM-DD)'); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Days"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_subscription_days" SIZE="5" MAXLENGTH="5" alt="number|0|1|1000000000" emsg="<?php putGS("You must input a number greater than 0 into the $1 field.", "Days"); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
	<INPUT TYPE="HIDDEN" NAME="f_user_id" VALUE="<?php  p($f_user_id); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_subscription_id" VALUE="<?php  p($f_subscription_id); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php  p($f_publication_id); ?>">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	</TD>
</TR>
<!--<tr><td colspan=2 width=250><?php  putGS('WARNING: If you subscribe to all sections, the periods for previously added sections will be overriden!'); ?></td></tr>-->
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
