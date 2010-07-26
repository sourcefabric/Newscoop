<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/templates/template_common.php");

$f_template_code = Input::Get('f_template_code', 'array', array(), true);
$f_destination_folder = Input::Get('f_destination_folder', 'string', '', true);
$f_current_folder = Input::Get('f_current_folder', 'string', 0, true);
$f_action = Input::Get('f_action');

$f_current_folder = urldecode($f_current_folder);

if (!Template::IsValidPath($f_current_folder) || !Template::IsValidPath($f_destination_folder)) {
	camp_html_goto_page("/$ADMIN/templates/");
}

foreach ($f_template_code as $name) {
     if (!Template::IsValidPath($name, false)) {
    	camp_html_goto_page("/$ADMIN/templates/");
    }
}

//
// Check permissions
//
if ($f_action == "move") {
	if (!$g_user->hasPermission("ManageTempl")) {
		camp_html_display_error(getGS("You do not have the right to move articles."));
		exit;
	}
}

// $articles array:
// The articles that were initially selected to perform the move or duplicate upon.
$templates = array();
for ($i = 0; $i < count($f_template_code); $i++) {
	$tmpTemplate = new Template($f_template_code[$i]);
	$templates[] = $tmpTemplate;
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;
}

// Get all the templates
$folders = array();
$folders = Template::GetAllFolders($folders);
$i = 1;
foreach ($folders as $folder) {
	$tmpTemplateFolder = substr($folder, strlen($Campsite['TEMPLATE_DIRECTORY']));
	if ($f_current_folder != $tmpTemplateFolder) {
		$folders[$i++] = $tmpTemplateFolder;
	}
}
if ($f_current_folder != '/') {
	$folders[0] = '/';
} else {
	array_shift($folders);
}

//
// This section is executed when the user finally hits the action button.
//
if (isset($_REQUEST["action_button"])) {
	if (!SecurityToken::isValid()) {
		camp_html_display_error(getGS('Invalid security token!'));
		exit;
	}

	if (empty($f_destination_folder)) {
		$errorMsg = getGS("You must select a destination folder");
		camp_html_add_msg($errorMsg);
	} else {
		if ($f_destination_folder != '/') {
			$url = "/$ADMIN/templates/index.php?Path=$f_destination_folder";
		} else {
			$url = "/$ADMIN/templates/index.php";
		}

		if ($f_action == "move") {
			$sql = "SELECT Id FROM TemplateTypes WHERE Name = 'nontpl'";
			$nonTplTypeId = $g_ado_db->GetOne($sql);
			// Move all the templates requested.
			foreach ($templates as $template) {
				if ($template->move($f_current_folder, $f_destination_folder)) {
					$searchKey = $template->getName();
					$replacementKey = ltrim($f_destination_folder
							. '/' . basename($template->getName()), '/');
					if ($template->getType() != $nonTplTypeId) {
						$searchKey = ' ' . $searchKey;
						$replacementKey = ' ' . $replacementKey;
					}
					$replaceObj = new FileTextSearch();
					$replaceObj->setExtensions(array('tpl','css'));
					$replaceObj->setSearchKey($searchKey);
					$replaceObj->setReplacementKey($replacementKey);
					$replaceObj->findReplace($Campsite['TEMPLATE_DIRECTORY']);
					Template::UpdateOnChange($template->getName(),
								 $f_destination_folder
								 . '/'
								 . basename($template->getName()));
				}
			}
			// Clear compiled templates
			require_once($GLOBALS['g_campsiteDir']."/template_engine/classes/CampTemplate.php");
			CampTemplate::singleton()->clear_compiled_tpl();

			camp_html_add_msg(getGS("Template(s) moved."), "ok");
			camp_html_goto_page($url);
		}
	}
} // END perform the action

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates/");
$crumbs[] = array(getGS("Move templates"), "");
echo camp_html_breadcrumbs($crumbs);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

camp_html_display_msgs();

?>

<P>
<DIV class="page_title" style="padding-left: 18px;">
<?php p(putGS("These templates")); ?>:
</DIV>

<FORM NAME="move" METHOD="POST" ONSUBMIT="return validateForm(this, 0, 1, 0, 1, 8);">
<?php echo SecurityToken::FormParameter(); ?>
<INPUT type="hidden" name="f_action" value="<?php p($f_action); ?>">
<?php
if (!empty($f_current_folder)) {
?>
<INPUT type="hidden" name="f_current_folder" value="<?php p($f_current_folder); ?>">
<?php
}
foreach ($templates as $template) {
?>
<INPUT type="hidden" name="f_template_code[]" value="<?php p($template->getName()); ?>">
<?php
}
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" style="margin-left: 10px;">
<TR>
	<TD>
		<TABLE cellpadding="3">
		<?php
		$class = 0;
		foreach ($templates as $template) {
		?>
		<TR class="<?php if ($class) { ?>list_row_even<?php } else { ?>list_row_odd<?php } $class = !$class; ?>">
			<TD><?php p($template->getName()); ?></TD>
		</TR>
		<?php
		}
		?>
		</TABLE>
	</TD>
</TR>
</TABLE>
<P>
<DIV class="page_title" style="padding-left: 18px;">
<?php putGS("to folder"); ?>:
</DIV>
<P>
<TABLE CELLSPACING="0" CELLPADDING="0" class="box_table" width="500">
<TR>
	<TD align="left">
		<TABLE align="left" border="0" width="100%">
		<TR>
			<TD colspan="2" style="padding-left: 20px; padding-bottom: 5px;font-size: 12pt; font-weight: bold;"><?php  putGS("Select destination"); ?></TD>
		</TR>
		<TR>
			<TD>
				<TABLE border="0">
				<TR>
					<TD VALIGN="middle" ALIGN="RIGHT" style="padding-left: 20px;"><?php  putGS('Folder'); ?>: </TD>
					<TD valign="middle" ALIGN="LEFT">
						<?php if (count($folders) > 1) { ?>
						<SELECT NAME="f_destination_folder" class="input_select" alt="select" emsg="<?php putGS("You must select a destination folder"); ?>">
						<OPTION VALUE="">---<?php  putGS('Select folder'); ?>---</option>
						<?php
						foreach ($folders as $folder) {
							camp_html_select_option($folder, $f_destination_folder, $folder);
						}
						?>
						</SELECT>
						<?php } elseif (count($folders) == 1) {
							$tmpFolder = camp_array_peek($folders);
							p(htmlspecialchars($tmpFolder));
							?>
							<INPUT type="hidden" name="f_destination_folder" value="<?php p($folder); ?>">

						<?php } else { ?>
							<SELECT class="input_select" DISABLED><OPTION><?php  putGS('No folders'); ?></option></SELECT>
						<?php }	?>
					</TD>
				</TR>
				</TABLE>
			</TD>
		</TR>
		<TR>
			<TD align="center" colspan="2">
				<INPUT TYPE="submit" Name="action_button" Value="<?php p(putGS("Move templates")); ?>" class="button" />
			</TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
