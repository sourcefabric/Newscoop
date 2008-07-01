<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/user_types/utypes_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/permission_list.php");
camp_load_translation_strings("users");

$canManage = $g_user->hasPermission('ManageUserTypes');
if (!$canManage) {
	$error = getGS("You do not have the right to change user type permissions.");
	camp_html_display_error($error);
	exit;
}

$rights = camp_get_permission_list();

// gets section rights array
$section_rights = array();
$publications = Publication::GetPublications();
foreach($publications as $publication) {
    $section_rights[$publication->getName()] = array();
    $issues = Issue::GetIssues($publication->getPublicationId());
    foreach($issues as $issue) {
        $section_rights[$publication->getName()][$issue->getName()] = array();
        $sections = Section::GetSections($publication->getPublicationId(),
                                         $issue->getIssueNumber(),
                                         $issue->getLanguageId());
        $rightsKeyValue = array();
        foreach($sections as $section) {
            $right_name = $section->getSectionRightName();
            $right_text = $section->getName();
            $rightsKeyValue[$right_name] = $right_text;
        }
        $section_rights[$publication->getName()][$issue->getName()] = $rightsKeyValue;
    }
}

$crumbs = array();
$crumbs[] = array(getGS("Users"), "");
$crumbs[] = array(getGS("User types"), "/$ADMIN/user_types");
$crumbs[] = array(getGS("Add new user type"), "");
echo camp_html_breadcrumbs($crumbs);

camp_html_display_msgs("0.25em", "0.25em");
?>

<form name="user_type_add" method="post" action="do_add.php">
<p>
<table border="0" cellspacing="0" cellpadding="1" class="table_input">
<tr>
	<td colspan="2" style="padding-top: 5px; padding-left: 10px;">
		<b><?php  putGS("Add new user type"); ?></b>
	</td>
</tr>
<tr>
	<td colspan="2">
		<hr noshade size="1" color="black">
	</td>
</tr>
<tr>
	<td colspan="2" style="padding-top: 5px; padding-bottom: 10px;" align="center">
		<input type="submit" class="button" name="Save" value="<?php putGS('Save'); ?>">
	</td>
</tr>
<tr>
	<td colspan="2" align="left" style="padding-left: 10px;">
		<?php putGS('Name'); ?>:
		<input type="text" class="input_text" name="Name" size="32">
	</td>
</tr>
<?php
foreach ($rights as $group_name=>$group) {
?>
<tr>
	<td colspan="2" align="left" style="padding-top: 10px; padding-left: 10px;">
		--- <?php putGS($group_name); ?> ---
	</td>
</tr>
<?php
foreach ($group as $right_name=>$right_text) {
?>
<tr>
	<td align="right" style="padding-left: 10px;">
		<input type="checkbox" name="<?php echo $right_name; ?>" class="input_checkbox" />
	</td>
	<td style="padding-right: 10px;">
		<?php putGS($right_text); ?>
	</td>
</tr>
<?php
}
}
?>
<tr>
	<td colspan="2" align="left" style="padding-top: 10px; padding-left: 10px;">
		--- <?php p('Sections'); ?> ---
	</td>
</tr>
<?php
foreach ($section_rights as $publication => $issues) {
?>
<tr>
    <td colspan="2">
     <?php p('<strong>Publication</strong>: '.$publication); ?>
    </td>
</tr>
<?php
    if (sizeof($issues) <= 0) {
?>
<tr>
    <td colspan="2" style="padding-left: 10px;">
    <?php putGS("There is no issues"); ?>
    </td>
</tr>
<?php
    } else {
        foreach ($issues as $issue => $sections) {
?>
<tr>
    <td colspan="2" style="padding-left: 10px;">
    <?php p('<strong>Issue</strong>: '.$issue); ?>
    </td>
</tr>
<?php
            if (sizeof($sections) <= 0) {
?>
<tr>
    <td colspan="2" style="padding-left: 20px;">
    <?php putGS("There is no sections"); ?>
    </td>
</tr>
<?php
            } else {
                foreach ($sections as $section_right => $section_name) {
?>
<tr>
	<td align="right" style="padding-left: 20px;">
		<input type="checkbox" name="<?php echo $section_right; ?>" class="input_checkbox" />
	</td>    
    <td>
    <?php p($section_name); ?>
    </td>
</tr>
<?php
                }
            }
        }
    }
}
?>
<tr>
	<td colspan="2" style="padding-top: 5px; padding-bottom: 10px;" align="center">
		<input type="submit" class="button" name="Save" value="<?php putGS('Save'); ?>">
	</td>
</tr>
</table>
</p>
</form>
<script>
document.user_type_add.Name.focus();
</script>
<?php camp_html_copyright_notice(); ?>
