<?php
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/common.php');
load_common_include_files("user_subscription_sections");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Subscription.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/SubscriptionSection.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_user_id = Input::Get('f_user_id', 'int', 0);
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_subscription_id = Input::Get('f_subscription_id', 'int', 0);

$publicationObj =& new Publication($f_publication_id);
$languageObj =& new Language($publicationObj->getDefaultLanguageId());

$manageUser =& new User($f_user_id);
$sections = SubscriptionSection::GetSubscriptionSections($f_subscription_id);

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Subscribers"), "/$ADMIN/users/?uType=Subscribers");
$crumbs[] = array(getGS("Account") . " '".$manageUser->getUserName()."'", 
			"/$ADMIN/users/edit.php?User=$f_user_id&uType=Subscribers");
$crumbs[] = array(getGS("Subscriptions"), "/$ADMIN/users/subscriptions/?f_user_id=$f_user_id");
$crumbs[] = array(getGS("Subscribed sections").": ".$publicationObj->getName(), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<table cellpadding="0" cellspacing="0" class="action_buttons">
<tr>
	<td valign=top>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><A HREF="add.php?f_subscription_id=<?php p($f_subscription_id); ?>&f_publication_id=<?php p($f_publication_id); ?>&f_user_id=<?php  p($f_user_id); ?>" ><IMG SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/add.png" BORDER="0"></A></TD>
			<TD><A HREF="add.php?f_subscription_id=<?php p($f_subscription_id); ?>&f_publication_id=<?php p($f_publication_id); ?>&f_user_id=<?php  p($f_user_id); ?>" ><B><?php  putGS("Add new section to subscription"); ?></B></A></TD>
		</TR>
		</TABLE>
	</td>

	<td valign="top">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><A HREF="change.php?f_subscription_id=<?php p($f_subscription_id); ?>&f_publication_id=<?php p($f_publication_id); ?>&f_user_id=<?php p($f_user_id); ?>" ><IMG SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/tol.gif" BORDER="0"></A></TD>
			<TD><A HREF="change.php?f_subscription_id=<?php p($f_subscription_id); ?>&f_publication_id=<?php p($f_publication_id); ?>&f_user_id=<?php p($f_user_id); ?>" ><B><?php  putGS("Change all sections"); ?></B></A></TD>
		</TR>
		</TABLE>
	</td>
</tr>
</table>
<p>

<?PHP
$isPaid = 0;
$sType = $sections[0]->getProperty('Type');
if ($sType == 'P') {
    $isPaid = 1;
}
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Section"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Start Date<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Days"); ?></B></TD>
	<?php  if ($isPaid) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Paid Days"); ?></B></TD>
	<?php  } ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Delete"); ?></B></TD>
</TR>

<?php 
$color= 0;
foreach ($sections as $section) { ?>	
<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
	<TD >
		<A HREF="/<?php p($ADMIN); ?>/users/subscriptions/sections/change.php?f_user_id=<?php p($f_user_id); ?>&f_publication_id=<?php p($f_publication_id); ?>&f_subscription_id=<?php p($f_subscription_id); ?>&f_section_number=<?php p($section->getSectionNumber()); ?>"><?php p(htmlspecialchars($section->getProperty('Name'))); ?></A>
	</TD>
	
	<TD>
		<?php  p(htmlspecialchars($section->getStartDate())); ?>
	</TD>
	
	<TD>
		<?php  p($section->getDays()); ?>
	</TD>
	
	<?php  if ($isPaid) { ?>
	<TD>
		<?php  p($section->getPaidDays()); ?>
	</TD>
	<?php  } ?>
	
	<TD ALIGN="CENTER">
		<A HREF="/<?php p($ADMIN); ?>/users/subscriptions/sections/do_del.php?f_user_id=<?php p($f_user_id); ?>&f_publication_id=<?php p($f_publication_id); ?>&f_subscription_id=<?php p($f_subscription_id); ?>&f_section_number=<?php p($section->getSectionNumber()); ?>"><IMG SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete subscription to section $1?', htmlspecialchars($section->getProperty('Name'))); ?>" TITLE="<?php  putGS('Delete subscription to section $1?', htmlspecialchars($section->getProperty('Name'))); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the subscription to the section $1?', htmlspecialchars($section->getProperty('Name'))); ?>');"></A>
	</TD>
</TR>
<?php 
}
?>	

<?php camp_html_copyright_notice(); ?>
